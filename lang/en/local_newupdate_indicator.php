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
 * English language strings.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'New and updated activity indicator';
$string['privacy:metadata'] = 'The New and updated activity indicator plugin does not store any personal data. It only stores per-course display preferences.';

// Capability.
$string['newupdateindicator:manage'] = 'Manage new/updated indicator settings for a course';

// Navigation.
$string['coursesettingsnode'] = 'New/updated indicator settings';
$string['settingssaved'] = 'New/updated indicator settings saved';
$string['backtocourse'] = 'Back to course';

// Admin settings - general.
$string['generalsettings'] = 'General settings';
$string['timespan'] = 'Indicator time-span';
$string['timespan_desc'] = 'How long an activity continues to display a "new" or "updated" indicator after it was added or modified. Once this period has elapsed, no indicator is shown.';

// Admin settings - new indicator.
$string['newindicatorsettings'] = '"New" indicator';
$string['newenabled'] = 'Show "new" indicator';
$string['newenabled_desc'] = 'When enabled, newly added activities and resources are highlighted on the course page.';
$string['newlabel'] = 'Label text';
$string['newlabel_desc'] = 'The text displayed next to newly added activities.';
$string['newicon'] = 'Icon';
$string['newicon_desc'] = 'The icon displayed next to newly added activities.';
$string['newcolour'] = 'Colour style';
$string['newcolour_desc'] = 'The colour style used for the "new" indicator badge.';
$string['defaultnewlabel'] = 'New';

// Admin settings - updated indicator.
$string['updatedindicatorsettings'] = '"Updated" indicator';
$string['updatedenabled'] = 'Show "updated" indicator';
$string['updatedenabled_desc'] = 'When enabled, recently modified activities and resources (that are not new) are highlighted on the course page.';
$string['updatedlabel'] = 'Label text';
$string['updatedlabel_desc'] = 'The text displayed next to recently updated activities.';
$string['updatedicon'] = 'Icon';
$string['updatedicon_desc'] = 'The icon displayed next to recently updated activities.';
$string['updatedcolour'] = 'Colour style';
$string['updatedcolour_desc'] = 'The colour style used for the "updated" indicator badge.';
$string['defaultupdatedlabel'] = 'Updated';

// Admin settings - display.
$string['displaysettings'] = 'Display settings';
$string['position'] = 'Indicator position';
$string['position_desc'] = 'Where the indicator is displayed relative to the activity link on the course page.';
$string['position_beforelink'] = 'Before the link';
$string['position_afterlink'] = 'After the link';
$string['position_topleft'] = 'Top-left corner';
$string['position_topright'] = 'Top-right corner';
$string['position_bottomleft'] = 'Bottom-left corner';
$string['position_bottomright'] = 'Bottom-right corner';

// Admin settings - recent content list.
$string['recentlistsettings'] = 'Recent content list';
$string['showrecentlist'] = 'Show recent content list';
$string['showrecentlist_desc'] = 'Display a short list of newly added or recently updated content at the top of the course page.';
$string['recentlistlimit'] = 'Maximum number of items';
$string['recentlistlimit_desc'] = 'The maximum number of items to display in the recent content list.';
$string['recentlisttitle'] = 'New and recently updated';

// Colour style options.
$string['colour_primary'] = 'Primary (blue)';
$string['colour_secondary'] = 'Secondary (grey)';
$string['colour_success'] = 'Success (green)';
$string['colour_danger'] = 'Danger (red)';
$string['colour_warning'] = 'Warning (yellow)';
$string['colour_info'] = 'Info (cyan)';
$string['colour_light'] = 'Light';
$string['colour_dark'] = 'Dark';

// Icon options.
$string['icon_none'] = 'No icon';
$string['icon_star'] = 'Star';
$string['icon_flagged'] = 'Flag';
$string['icon_marker'] = 'Marker';
$string['icon_new'] = 'New';
$string['icon_notifications'] = 'Bell';
$string['icon_news'] = 'Megaphone';
$string['icon_info'] = 'Information';
$string['icon_warning'] = 'Warning';

// Course override form.
$string['courseoverrideheading'] = 'New/updated indicator settings for {$a}';
$string['override'] = 'Override site default settings for this course';
$string['override_help'] = 'When enabled, the settings below apply only to this course and take precedence over the site administration defaults. When disabled, this course follows the site defaults and any values entered below are ignored.';
$string['usesitedefault'] = 'Use site default ({$a})';
$string['invalidoption'] = 'Please choose one of the available options.';
$string['yes'] = 'Yes';
$string['no'] = 'No';

// Status text used in the recent content list.
$string['statusnew'] = 'New';
$string['statusupdated'] = 'Updated';
