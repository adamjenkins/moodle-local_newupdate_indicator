<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_newupdate_indicator\local;

/**
 * Works out which course modules should currently display a "new" or "updated" indicator.
 *
 * A module is considered "new" while its course_modules.added timestamp is within the
 * configured time-span. Once that period has elapsed it may instead be considered
 * "updated" if its activity instance reports a more recent timemodified value that is
 * still within the time-span.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class indicator_finder {
    /** Status identifier for newly added activities. */
    const STATUS_NEW = 'new';

    /** Status identifier for recently updated activities. */
    const STATUS_UPDATED = 'updated';

    /** @var array<string, array<int, int>> Cache of instance timemodified values, keyed by modname */
    protected static $timemodifiedcache = [];

    /** @var array<string, bool> Cache of whether a module table has a timemodified column */
    protected static $hastimemodifiedcache = [];

    /**
     * Returns the modules in a course that should currently show an indicator.
     *
     * @param int $courseid
     * @param \stdClass|null $config Optional pre-resolved configuration (avoids a repeat lookup)
     * @return array<int, \stdClass> Indicator info keyed by course module id, each containing
     *                               'status' (self::STATUS_*) and 'timestamp' (int)
     */
    public static function get_indicators(int $courseid, ?\stdClass $config = null): array {
        $config = $config ?? config::get_for_course($courseid);
        $newenabled = !empty($config->newenabled);
        $updatedenabled = !empty($config->updatedenabled);
        if ((!$newenabled && !$updatedenabled) || empty($config->timespan)) {
            return [];
        }

        $cms = get_fast_modinfo($courseid)->get_cms();
        if (empty($cms)) {
            return [];
        }

        $cutoff = time() - $config->timespan;
        $addedtimes = self::get_added_times($cms);
        $modifiedtimes = self::get_modified_times($cms, $addedtimes);

        $indicators = [];
        foreach ($cms as $cm) {
            if (!$cm->uservisible || !isset($addedtimes[$cm->id])) {
                continue;
            }

            $indicator = self::classify(
                $addedtimes[$cm->id],
                $modifiedtimes[$cm->modname][$cm->instance] ?? null,
                $cutoff,
                $newenabled,
                $updatedenabled
            );
            if ($indicator !== null) {
                $indicators[$cm->id] = $indicator;
            }
        }

        return $indicators;
    }

    /**
     * Looks up the course_modules.added timestamp for each course module.
     *
     * @param \cm_info[] $cms
     * @return array<int, int> added timestamps keyed by course module id
     */
    protected static function get_added_times(array $cms): array {
        global $DB;

        [$insql, $inparams] = $DB->get_in_or_equal(array_keys($cms), SQL_PARAMS_NAMED);
        $records = $DB->get_records_select('course_modules', "id $insql", $inparams, '', 'id, added');

        $addedtimes = [];
        foreach ($records as $record) {
            $addedtimes[$record->id] = (int) $record->added;
        }
        return $addedtimes;
    }

    /**
     * Looks up the activity instance timemodified values for each course module,
     * grouped by module name (the table that needs to be queried).
     *
     * @param \cm_info[] $cms
     * @param int[] $addedtimes added timestamps keyed by course module id,
     *                          used to determine which modules to look up
     * @return array<string, array<int, int>> timemodified values keyed by [modname][instanceid]
     */
    protected static function get_modified_times(array $cms, array $addedtimes): array {
        $instanceidsbymodname = [];
        foreach ($cms as $cm) {
            if (isset($addedtimes[$cm->id])) {
                $instanceidsbymodname[$cm->modname][] = $cm->instance;
            }
        }

        $modifiedtimes = [];
        foreach ($instanceidsbymodname as $modname => $instanceids) {
            $modifiedtimes[$modname] = self::get_instance_timemodified($modname, $instanceids);
        }
        return $modifiedtimes;
    }

    /**
     * Determines whether a course module should currently show a "new" or "updated" indicator.
     *
     * @param int $added The course_modules.added timestamp
     * @param int|null $modified The activity instance timemodified value, if known
     * @param int $cutoff Timestamps before this are considered too old to display
     * @param bool $newenabled Whether the "new" indicator is enabled
     * @param bool $updatedenabled Whether the "updated" indicator is enabled
     * @return \stdClass|null An object with 'status' and 'timestamp' properties, or null
     */
    protected static function classify(
        int $added,
        ?int $modified,
        int $cutoff,
        bool $newenabled,
        bool $updatedenabled
    ): ?\stdClass {
        if ($newenabled && $added > 0 && $added >= $cutoff) {
            return (object) ['status' => self::STATUS_NEW, 'timestamp' => $added];
        }

        if ($updatedenabled && $modified !== null && $modified > $added && $modified >= $cutoff) {
            return (object) ['status' => self::STATUS_UPDATED, 'timestamp' => $modified];
        }

        return null;
    }

    /**
     * Looks up the timemodified value for a set of activity module instances.
     *
     * Not all activity module tables include a timemodified column, so the
     * lookup is skipped (and an empty array returned) for those that do not.
     *
     * @param string $modname The activity module name, e.g. 'quiz'
     * @param int[] $instanceids
     * @return array<int, int> timemodified keyed by instance id
     */
    protected static function get_instance_timemodified(string $modname, array $instanceids): array {
        global $DB;

        if (empty($instanceids)) {
            return [];
        }

        if (!isset(self::$hastimemodifiedcache[$modname])) {
            $columns = $DB->get_columns($modname);
            self::$hastimemodifiedcache[$modname] = isset($columns['timemodified']);
        }

        if (!self::$hastimemodifiedcache[$modname]) {
            return [];
        }

        if (!isset(self::$timemodifiedcache[$modname])) {
            self::$timemodifiedcache[$modname] = [];
        }

        $missing = array_diff($instanceids, array_keys(self::$timemodifiedcache[$modname]));
        if (!empty($missing)) {
            [$insql, $inparams] = $DB->get_in_or_equal($missing, SQL_PARAMS_NAMED);
            $records = $DB->get_records_select($modname, "id $insql", $inparams, '', 'id, timemodified');
            foreach ($records as $record) {
                self::$timemodifiedcache[$modname][$record->id] = (int) $record->timemodified;
            }
        }

        return array_intersect_key(self::$timemodifiedcache[$modname], array_flip($instanceids));
    }

    /**
     * Clears the internal caches. Mainly useful for unit tests.
     *
     * @return void
     */
    public static function reset_caches(): void {
        self::$timemodifiedcache = [];
        self::$hastimemodifiedcache = [];
    }
}
