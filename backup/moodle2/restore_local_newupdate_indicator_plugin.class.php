<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Restore support for local_newupdate_indicator.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restores the per-course indicator overrides included in course backups.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_local_newupdate_indicator_plugin extends restore_local_plugin {
    /**
     * Declare the paths handled at course level.
     *
     * @return restore_path_element[]
     */
    protected function define_course_plugin_structure() {
        return [
            new restore_path_element('override', $this->get_pathfor('/override')),
        ];
    }

    /**
     * Process one override element, upserting it for the destination course.
     *
     * @param array|\stdClass $data
     * @return void
     */
    public function process_override($data) {
        global $DB;

        $data = (object) $data;
        $data->courseid = (int) $this->task->get_courseid();
        $data->timemodified = time();
        unset($data->id);

        if ($existing = $DB->get_record('local_newupdate_indicator', ['courseid' => $data->courseid])) {
            $data->id = $existing->id;
            $DB->update_record('local_newupdate_indicator', $data);
        } else {
            $DB->insert_record('local_newupdate_indicator', $data);
        }

        \local_newupdate_indicator\local\config::reset_caches();
    }
}
