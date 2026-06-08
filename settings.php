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
 * Site administration settings.
 *
 * These act as the defaults for every course; individual courses may override
 * any of them via the "New/updated indicator settings" course administration page.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $component = 'local_newupdate_indicator';

    $settings = new admin_settingpage($component, get_string('pluginname', $component));
    $ADMIN->add('localplugins', $settings);

    $iconoptions = \local_newupdate_indicator\local\config::get_icon_options();
    $colouroptions = \local_newupdate_indicator\local\config::get_colour_options();
    $positionoptions = \local_newupdate_indicator\local\config::get_position_options();

    $settings->add(new admin_setting_configduration(
        $component . '/timespan',
        get_string('timespan', $component),
        get_string('timespan_desc', $component),
        WEEKSECS
    ));

    $settings->add(new admin_setting_configcheckbox(
        $component . '/newenabled',
        get_string('newenabled', $component),
        get_string('newenabled_desc', $component),
        1
    ));

    $settings->add(new admin_setting_configtext(
        $component . '/newlabel',
        get_string('newlabel', $component),
        get_string('newlabel_desc', $component),
        get_string('defaultnewlabel', $component),
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configselect(
        $component . '/newicon',
        get_string('newicon', $component),
        get_string('newicon_desc', $component),
        'star',
        $iconoptions
    ));

    $settings->add(new admin_setting_configselect(
        $component . '/newcolour',
        get_string('newcolour', $component),
        get_string('newcolour_desc', $component),
        'success',
        $colouroptions
    ));

    $settings->add(new admin_setting_configcheckbox(
        $component . '/updatedenabled',
        get_string('updatedenabled', $component),
        get_string('updatedenabled_desc', $component),
        1
    ));

    $settings->add(new admin_setting_configtext(
        $component . '/updatedlabel',
        get_string('updatedlabel', $component),
        get_string('updatedlabel_desc', $component),
        get_string('defaultupdatedlabel', $component),
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configselect(
        $component . '/updatedicon',
        get_string('updatedicon', $component),
        get_string('updatedicon_desc', $component),
        'notifications',
        $iconoptions
    ));

    $settings->add(new admin_setting_configselect(
        $component . '/updatedcolour',
        get_string('updatedcolour', $component),
        get_string('updatedcolour_desc', $component),
        'info',
        $colouroptions
    ));

    $settings->add(new admin_setting_configselect(
        $component . '/position',
        get_string('position', $component),
        get_string('position_desc', $component),
        'afterlink',
        $positionoptions
    ));

    $settings->add(new admin_setting_configcheckbox(
        $component . '/showrecentlist',
        get_string('showrecentlist', $component),
        get_string('showrecentlist_desc', $component),
        0
    ));

    $settings->add(new admin_setting_configtext(
        $component . '/recentlistlimit',
        get_string('recentlistlimit', $component),
        get_string('recentlistlimit_desc', $component),
        5,
        PARAM_INT
    ));
}
