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
 * Backup support for local_newupdate_indicator.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Includes the per-course indicator overrides in course backups.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_local_newupdate_indicator_plugin extends backup_local_plugin {
    /**
     * Attach the course override record to the course element.
     *
     * @return backup_plugin_element
     */
    protected function define_course_plugin_structure() {
        $plugin = $this->get_plugin_element();

        $pluginwrapper = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($pluginwrapper);

        $override = new backup_nested_element('override', null, [
            'timespan', 'newenabled', 'newlabel', 'newicon', 'newcolour',
            'updatedenabled', 'updatedlabel', 'updatedicon', 'updatedcolour',
            'position', 'showrecentlist', 'recentlistlimit',
        ]);
        $pluginwrapper->add_child($override);

        $override->set_source_table('local_newupdate_indicator', ['courseid' => backup::VAR_COURSEID]);

        return $plugin;
    }
}
