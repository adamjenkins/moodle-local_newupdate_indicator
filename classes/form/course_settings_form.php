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

namespace local_newupdate_indicator\form;

use local_newupdate_indicator\local\config;
use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

/**
 * Per-course override form for the new/updated indicator settings.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_settings_form extends moodleform {
    /**
     * Form definition.
     *
     * @return void
     */
    protected function definition() {
        $mform = $this->_form;
        $courseid = $this->_customdata['courseid'];
        $component = 'local_newupdate_indicator';

        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('advcheckbox', 'override', '', get_string('override', $component));
        $mform->setType('override', PARAM_BOOL);
        $mform->addHelpButton('override', 'override', $component);

        $mform->addElement('header', 'generalsettings', get_string('generalsettings', $component));
        $mform->setExpanded('generalsettings');

        $mform->addElement('duration', 'timespan', get_string('timespan', $component));
        $mform->setType('timespan', PARAM_INT);
        $mform->hideIf('timespan', 'override', 'notchecked');

        $mform->addElement('header', 'newindicatorsettings', get_string('newindicatorsettings', $component));
        $mform->setExpanded('newindicatorsettings');

        $mform->addElement('advcheckbox', 'newenabled', get_string('newenabled', $component));
        $mform->setType('newenabled', PARAM_BOOL);
        $mform->hideIf('newenabled', 'override', 'notchecked');

        $mform->addElement('text', 'newlabel', get_string('newlabel', $component));
        $mform->setType('newlabel', PARAM_TEXT);
        $mform->hideIf('newlabel', 'override', 'notchecked');

        $mform->addElement('select', 'newicon', get_string('newicon', $component), config::get_icon_options());
        $mform->setType('newicon', PARAM_ALPHA);
        $mform->hideIf('newicon', 'override', 'notchecked');

        $mform->addElement('select', 'newcolour', get_string('newcolour', $component), config::get_colour_options());
        $mform->setType('newcolour', PARAM_ALPHA);
        $mform->hideIf('newcolour', 'override', 'notchecked');

        $mform->addElement('header', 'updatedindicatorsettings', get_string('updatedindicatorsettings', $component));
        $mform->setExpanded('updatedindicatorsettings');

        $mform->addElement('advcheckbox', 'updatedenabled', get_string('updatedenabled', $component));
        $mform->setType('updatedenabled', PARAM_BOOL);
        $mform->hideIf('updatedenabled', 'override', 'notchecked');

        $mform->addElement('text', 'updatedlabel', get_string('updatedlabel', $component));
        $mform->setType('updatedlabel', PARAM_TEXT);
        $mform->hideIf('updatedlabel', 'override', 'notchecked');

        $mform->addElement('select', 'updatedicon', get_string('updatedicon', $component), config::get_icon_options());
        $mform->setType('updatedicon', PARAM_ALPHA);
        $mform->hideIf('updatedicon', 'override', 'notchecked');

        $mform->addElement('select', 'updatedcolour', get_string('updatedcolour', $component), config::get_colour_options());
        $mform->setType('updatedcolour', PARAM_ALPHA);
        $mform->hideIf('updatedcolour', 'override', 'notchecked');

        $mform->addElement('header', 'displaysettings', get_string('displaysettings', $component));
        $mform->setExpanded('displaysettings');

        $mform->addElement('select', 'position', get_string('position', $component), config::get_position_options());
        $mform->setType('position', PARAM_ALPHA);
        $mform->hideIf('position', 'override', 'notchecked');

        $mform->addElement('header', 'recentlistsettings', get_string('recentlistsettings', $component));
        $mform->setExpanded('recentlistsettings');

        $mform->addElement('advcheckbox', 'showrecentlist', get_string('showrecentlist', $component));
        $mform->setType('showrecentlist', PARAM_BOOL);
        $mform->hideIf('showrecentlist', 'override', 'notchecked');

        $mform->addElement('text', 'recentlistlimit', get_string('recentlistlimit', $component));
        $mform->setType('recentlistlimit', PARAM_INT);
        $mform->hideIf('recentlistlimit', 'override', 'notchecked');

        $mform->addElement('hidden', 'returnurl', $this->_customdata['returnurl'] ?? '');
        $mform->setType('returnurl', PARAM_LOCALURL);

        $this->add_action_buttons();
    }

    /**
     * Validates that the select-based fields contain one of their known identifiers.
     *
     * The select elements already constrain the choices in the UI, but form data is
     * submitted as plain strings, so the values must also be checked server-side.
     *
     * @param array $data
     * @param array $files
     * @return array<string, string> Validation errors keyed by field name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['override'])) {
            return $errors;
        }

        $checks = [
            'newicon' => array_keys(config::get_icon_options()),
            'newcolour' => array_keys(config::get_colour_options()),
            'updatedicon' => array_keys(config::get_icon_options()),
            'updatedcolour' => array_keys(config::get_colour_options()),
            'position' => array_keys(config::get_position_options()),
        ];

        foreach ($checks as $field => $allowed) {
            if (isset($data[$field]) && !in_array($data[$field], $allowed, true)) {
                $errors[$field] = get_string('invalidoption', 'local_newupdate_indicator');
            }
        }

        return $errors;
    }

    /**
     * Loads the form with either the current override values or the site defaults.
     *
     * @param \stdClass|null $override The current override record, or null if none exists
     * @return void
     */
    public function load_current_values(?\stdClass $override): void {
        $defaults = config::get_site_defaults();

        $values = [
            'override' => $override !== null ? 1 : 0,
        ];

        foreach (config::overridable_fields() as $field) {
            if ($override !== null && $override->$field !== null) {
                $values[$field] = $override->$field;
            } else {
                $values[$field] = $defaults->$field;
            }
        }

        $this->set_data((object) $values);
    }
}
