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

/**
 * Library functions and callbacks.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Adds a link to the course administration menu for managing per-course indicator settings.
 *
 * @param navigation_node $settingsnav
 * @param context $context
 * @return void
 */
function local_newupdate_indicator_extend_settings_navigation(navigation_node $settingsnav, context $context): void {
    global $PAGE;

    if ($context->contextlevel != CONTEXT_COURSE || $context->instanceid == SITEID) {
        return;
    }

    if (!has_capability('local/newupdate_indicator:manage', $context)) {
        return;
    }

    $coursenode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
    if (!$coursenode) {
        return;
    }

    $url = new moodle_url('/local/newupdate_indicator/courseconfig.php', ['courseid' => $context->instanceid]);
    $node = navigation_node::create(
        get_string('coursesettingsnode', 'local_newupdate_indicator'),
        $url,
        navigation_node::NODETYPE_LEAF,
        'local_newupdate_indicator',
        'local_newupdate_indicator',
        new pix_icon('i/star', '')
    );

    if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
        $node->make_active();
    }

    $coursenode->add_node($node);
}
