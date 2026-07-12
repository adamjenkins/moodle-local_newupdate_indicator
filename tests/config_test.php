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

namespace local_newupdate_indicator;

use local_newupdate_indicator\local\config;

/**
 * Tests for configuration resolution and course deletion cleanup.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \local_newupdate_indicator\local\config
 * @covers      \local_newupdate_indicator\local\observer
 */
final class config_test extends \advanced_testcase {
    /**
     * Set up.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        config::reset_caches();
    }

    /**
     * Without an override row, a course resolves to the site defaults.
     */
    public function test_course_without_override_uses_site_defaults(): void {
        $course = $this->getDataGenerator()->create_course();

        set_config('timespan', 3 * DAYSECS, 'local_newupdate_indicator');
        config::reset_caches();

        $effective = config::get_for_course($course->id);
        $this->assertSame(3 * DAYSECS, $effective->timespan);
        $this->assertTrue($effective->newenabled);
    }

    /**
     * Non-null override fields win over site defaults; null fields fall through.
     */
    public function test_override_fields_take_precedence(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('local_newupdate_indicator', (object) [
            'courseid' => $course->id,
            'timespan' => DAYSECS,
            'newlabel' => 'Fresh!',
            'timemodified' => time(),
        ]);
        config::reset_caches();

        $effective = config::get_for_course($course->id);
        $this->assertSame(DAYSECS, $effective->timespan);
        $this->assertSame('Fresh!', $effective->newlabel);
        // Fields left null in the override keep the site default.
        $this->assertSame(config::get_site_defaults()->updatedlabel, $effective->updatedlabel);
    }

    /**
     * Deleting a course removes its override row via the observer.
     */
    public function test_course_deletion_removes_override(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('local_newupdate_indicator', (object) [
            'courseid' => $course->id,
            'timespan' => DAYSECS,
            'timemodified' => time(),
        ]);

        delete_course($course->id, false);

        $this->assertFalse($DB->record_exists('local_newupdate_indicator', ['courseid' => $course->id]));
    }
}
