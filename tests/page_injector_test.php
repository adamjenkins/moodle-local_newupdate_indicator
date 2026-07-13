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

namespace local_newupdate_indicator;

use local_newupdate_indicator\local\config;
use local_newupdate_indicator\local\page_injector;

/**
 * Tests for badge markup rendering.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \local_newupdate_indicator\local\page_injector
 */
final class page_injector_test extends \advanced_testcase {
    /**
     * Set up.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        config::reset_caches();
    }

    /**
     * A label containing HTML must be encoded in the rendered badge markup,
     * even if it reached the config unsanitised (e.g. via a legacy or
     * hand-crafted course restore).
     */
    public function test_label_is_html_encoded_in_badge_markup(): void {
        global $DB;

        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_module('page', ['course' => $course->id]);

        $payload = '<img src=x onerror=alert(1)>';
        $DB->insert_record('local_newupdate_indicator', (object) [
            'courseid' => $course->id,
            'newlabel' => $payload,
            'timemodified' => time(),
        ]);
        config::reset_caches();

        $data = page_injector::build_page_data($course);
        $this->assertNotNull($data);
        $this->assertNotEmpty($data['badges']);

        $html = $data['badges'][0]['html'];
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString(s($payload), $html);
    }
}
