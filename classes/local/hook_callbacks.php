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

namespace local_newupdate_indicator\local;

/**
 * Hook callbacks for local_newupdate_indicator.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Injects the indicator JavaScript on course view pages.
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     * @return void
     */
    public static function before_footer(\core\hook\output\before_footer_html_generation $hook): void {
        global $PAGE;

        if ($PAGE->course->id == SITEID) {
            return;
        }

        if (strpos((string) $PAGE->pagetype, 'course-view-') !== 0) {
            return;
        }

        $data = page_injector::build_page_data($PAGE->course);
        if (empty($data)) {
            return;
        }

        // The pre-rendered markup is too large for js_call_amd arguments
        // (limited to 1024 characters), so it travels in a JSON script tag
        // that the AMD module reads back out of the DOM.
        $hook->add_html(\html_writer::tag(
            'script',
            json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
            ['type' => 'application/json', 'data-region' => 'local_newupdate_indicator-data']
        ));
        $PAGE->requires->js_call_amd('local_newupdate_indicator/indicator', 'init', []);
    }
}
