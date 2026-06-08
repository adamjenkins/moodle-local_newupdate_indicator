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
 * Helper for resolving the effective indicator configuration for a course.
 *
 * Site administration settings provide the defaults; individual courses may
 * override any of these settings via {@see \local_newupdate_indicator\local\config::overridable_fields()}.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config {
    /** @var array<int, \stdClass> Per-course resolved configuration cache */
    protected static $coursecache = [];

    /** @var \stdClass|null Site default configuration cache */
    protected static $defaultscache = null;

    /**
     * Names of the settings that may be overridden at course level.
     *
     * These match the column names of the local_newupdate_indicator table
     * (excluding id, courseid and timemodified) and the site setting names.
     *
     * @return string[]
     */
    public static function overridable_fields(): array {
        return [
            'timespan',
            'newenabled',
            'newlabel',
            'newicon',
            'newcolour',
            'updatedenabled',
            'updatedlabel',
            'updatedicon',
            'updatedcolour',
            'position',
            'showrecentlist',
            'recentlistlimit',
        ];
    }

    /**
     * Returns the site default configuration, derived from the admin settings.
     *
     * @return \stdClass
     */
    public static function get_site_defaults(): \stdClass {
        if (self::$defaultscache !== null) {
            return self::$defaultscache;
        }

        $stored = get_config('local_newupdate_indicator');

        self::$defaultscache = (object) [
            'timespan' => isset($stored->timespan) ? (int) $stored->timespan : WEEKSECS,
            'newenabled' => isset($stored->newenabled) ? (bool) $stored->newenabled : true,
            'newlabel' => $stored->newlabel ?? get_string('defaultnewlabel', 'local_newupdate_indicator'),
            'newicon' => $stored->newicon ?? 'star',
            'newcolour' => $stored->newcolour ?? 'success',
            'updatedenabled' => isset($stored->updatedenabled) ? (bool) $stored->updatedenabled : true,
            'updatedlabel' => $stored->updatedlabel ?? get_string('defaultupdatedlabel', 'local_newupdate_indicator'),
            'updatedicon' => $stored->updatedicon ?? 'notifications',
            'updatedcolour' => $stored->updatedcolour ?? 'info',
            'position' => $stored->position ?? 'afterlink',
            'showrecentlist' => isset($stored->showrecentlist) ? (bool) $stored->showrecentlist : false,
            'recentlistlimit' => isset($stored->recentlistlimit) ? (int) $stored->recentlistlimit : 5,
        ];

        return self::$defaultscache;
    }

    /**
     * Returns the effective configuration for a course, merging site defaults
     * with any per-course overrides.
     *
     * @param int $courseid
     * @return \stdClass
     */
    public static function get_for_course(int $courseid): \stdClass {
        if (isset(self::$coursecache[$courseid])) {
            return self::$coursecache[$courseid];
        }

        $effective = clone self::get_site_defaults();

        $override = self::get_override_record($courseid);
        if ($override !== null) {
            foreach (self::overridable_fields() as $field) {
                if ($override->$field === null) {
                    continue;
                }
                $effective->$field = self::cast_override_value($field, $override->$field);
            }
        }

        self::$coursecache[$courseid] = $effective;
        return $effective;
    }

    /**
     * Casts a raw override value to the type expected for the given field.
     *
     * @param string $field One of {@see self::overridable_fields()}
     * @param mixed $value The raw stored override value (guaranteed non-null)
     * @return mixed The value cast to its effective type
     */
    protected static function cast_override_value(string $field, $value) {
        static $booleanfields = ['newenabled', 'updatedenabled', 'showrecentlist'];
        static $integerfields = ['timespan', 'recentlistlimit'];

        if (in_array($field, $booleanfields, true)) {
            return (bool) $value;
        }
        if (in_array($field, $integerfields, true)) {
            return (int) $value;
        }
        return $value;
    }

    /**
     * Returns the raw per-course override record, or null if none exists.
     *
     * @param int $courseid
     * @return \stdClass|null
     */
    public static function get_override_record(int $courseid): ?\stdClass {
        global $DB;

        $record = $DB->get_record('local_newupdate_indicator', ['courseid' => $courseid]);
        return $record ?: null;
    }

    /**
     * Clears the internal caches. Mainly useful for unit tests.
     *
     * @return void
     */
    public static function reset_caches(): void {
        self::$coursecache = [];
        self::$defaultscache = null;
    }

    /**
     * Returns the available icon identifiers and their display names.
     *
     * @return array<string, string>
     */
    public static function get_icon_options(): array {
        $options = [];
        foreach (array_keys(self::icon_pix_map()) as $identifier) {
            $options[$identifier] = get_string('icon_' . $identifier, 'local_newupdate_indicator');
        }
        $options['none'] = get_string('icon_none', 'local_newupdate_indicator');
        return $options;
    }

    /**
     * Returns the available colour style identifiers and their display names.
     *
     * @return array<string, string>
     */
    public static function get_colour_options(): array {
        $options = [];
        foreach (self::colour_identifiers() as $identifier) {
            $options[$identifier] = get_string('colour_' . $identifier, 'local_newupdate_indicator');
        }
        return $options;
    }

    /**
     * Returns the list of valid colour style identifiers.
     *
     * These correspond to Bootstrap contextual colour names, so themes that
     * follow Bootstrap's palette will display the indicators consistently.
     *
     * @return string[]
     */
    public static function colour_identifiers(): array {
        return ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
    }

    /**
     * Returns the available indicator position identifiers and their display names.
     *
     * @return array<string, string>
     */
    public static function get_position_options(): array {
        $options = [];
        foreach (self::position_identifiers() as $identifier) {
            $options[$identifier] = get_string('position_' . $identifier, 'local_newupdate_indicator');
        }
        return $options;
    }

    /**
     * Returns the list of valid indicator position identifiers.
     *
     * @return string[]
     */
    public static function position_identifiers(): array {
        return ['beforelink', 'afterlink', 'topleft', 'topright', 'bottomleft', 'bottomright'];
    }

    /**
     * Renders the markup for an icon identifier.
     *
     * @param string $identifier
     * @param string $alt Alt text for the icon
     * @return string HTML, or an empty string if no icon should be shown
     */
    public static function render_icon(string $identifier, string $alt = ''): string {
        global $OUTPUT;

        $map = self::icon_pix_map();
        if ($identifier === 'none' || !isset($map[$identifier])) {
            return '';
        }

        return $OUTPUT->pix_icon($map[$identifier], $alt, 'core', ['class' => 'local-newupdate-indicator-icon-img']);
    }

    /**
     * Maps icon identifiers to core pix icon names.
     *
     * @return array<string, string>
     */
    protected static function icon_pix_map(): array {
        return [
            'star' => 'i/star',
            'flagged' => 'i/flagged',
            'marker' => 'i/marker',
            'new' => 'i/new',
            'notifications' => 'i/notifications',
            'news' => 'i/news',
            'info' => 'i/circleinfo',
            'warning' => 'i/warning',
        ];
    }
}
