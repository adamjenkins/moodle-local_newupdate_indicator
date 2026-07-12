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
 * Per-course override settings for the new/updated indicator.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

use local_newupdate_indicator\form\course_settings_form;
use local_newupdate_indicator\local\config;

$courseid = required_param('courseid', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('local/newupdate_indicator:manage', $context);

$pageurl = new moodle_url('/local/newupdate_indicator/courseconfig.php', ['courseid' => $courseid]);
$returnurl = new moodle_url('/course/view.php', ['id' => $courseid]);

$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('coursesettingsnode', 'local_newupdate_indicator'));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('coursesettingsnode', 'local_newupdate_indicator'));

$form = new course_settings_form($pageurl, [
    'courseid' => $courseid,
    'returnurl' => $returnurl->out_as_local_url(false),
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $form->get_data()) {
    $existing = $DB->get_record('local_newupdate_indicator', ['courseid' => $courseid]);

    $record = $existing ?: (object) ['courseid' => $courseid];

    if (empty($data->override)) {
        foreach (config::overridable_fields() as $field) {
            $record->$field = null;
        }
    } else {
        foreach (config::overridable_fields() as $field) {
            switch ($field) {
                case 'newenabled':
                case 'updatedenabled':
                case 'showrecentlist':
                    $record->$field = !empty($data->$field) ? 1 : 0;
                    break;
                default:
                    $record->$field = $data->$field;
            }
        }
    }
    $record->timemodified = time();

    if ($existing) {
        $DB->update_record('local_newupdate_indicator', $record);
    } else {
        $DB->insert_record('local_newupdate_indicator', $record);
    }

    config::reset_caches();

    $returnto = !empty($data->returnurl) ? new moodle_url($data->returnurl) : $pageurl;
    redirect($returnto, get_string('settingssaved', 'local_newupdate_indicator'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$override = $DB->get_record('local_newupdate_indicator', ['courseid' => $courseid]);
$form->load_current_values($override ?: null);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('courseoverrideheading', 'local_newupdate_indicator', format_string($course->fullname)));
$form->display();
echo $OUTPUT->footer();
