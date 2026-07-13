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

        $data->newlabel = $this->clean_label($data->newlabel ?? null);
        $data->updatedlabel = $this->clean_label($data->updatedlabel ?? null);
        $data->newicon = $this->clean_option($data->newicon ?? null, \local_newupdate_indicator\local\config::get_icon_options());
        $data->updatedicon = $this->clean_option(
            $data->updatedicon ?? null,
            \local_newupdate_indicator\local\config::get_icon_options()
        );
        $data->newcolour = $this->clean_option(
            $data->newcolour ?? null,
            \local_newupdate_indicator\local\config::get_colour_options()
        );
        $data->updatedcolour = $this->clean_option(
            $data->updatedcolour ?? null,
            \local_newupdate_indicator\local\config::get_colour_options()
        );
        $data->position = $this->clean_option(
            $data->position ?? null,
            \local_newupdate_indicator\local\config::get_position_options()
        );

        if ($existing = $DB->get_record('local_newupdate_indicator', ['courseid' => $data->courseid])) {
            $data->id = $existing->id;
            $DB->update_record('local_newupdate_indicator', $data);
        } else {
            $DB->insert_record('local_newupdate_indicator', $data);
        }

        \local_newupdate_indicator\local\config::reset_caches();
    }

    /**
     * Sanitises a restored label value the same way the interactive form does.
     *
     * @param mixed $value
     * @return string|null
     */
    protected function clean_label($value): ?string {
        if ($value === null) {
            return null;
        }
        return clean_param($value, PARAM_TEXT);
    }

    /**
     * Validates a restored option value against the allowed identifiers,
     * discarding anything that is not one of them (falls back to the site
     * default, mirroring how a null override column is treated).
     *
     * @param mixed $value
     * @param array $options Allowed identifiers, keyed the same way as config::get_icon_options() and siblings
     * @return string|null
     */
    protected function clean_option($value, array $options): ?string {
        if ($value === null || $value === '') {
            return null;
        }
        return array_key_exists($value, $options) ? $value : null;
    }
}
