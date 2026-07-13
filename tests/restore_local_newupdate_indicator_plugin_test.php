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

/**
 * Tests for the restore-time sanitisation of course backup override data.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \restore_local_newupdate_indicator_plugin
 */
final class restore_local_newupdate_indicator_plugin_test extends \advanced_testcase {
    /** @var \restore_local_newupdate_indicator_plugin Instance built without invoking the restore-task constructor */
    protected $restore;

    /**
     * Set up.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/local/newupdate_indicator/backup/moodle2/'
            . 'restore_local_newupdate_indicator_plugin.class.php');

        $class = new \ReflectionClass(\restore_local_newupdate_indicator_plugin::class);
        $this->restore = $class->newInstanceWithoutConstructor();
    }

    /**
     * Invokes the protected clean_label() method.
     *
     * @param mixed $value
     * @return string|null
     */
    protected function clean_label($value): ?string {
        $method = new \ReflectionMethod($this->restore, 'clean_label');
        $method->setAccessible(true);
        return $method->invoke($this->restore, $value);
    }

    /**
     * Invokes the protected clean_option() method.
     *
     * @param mixed $value
     * @param array $options
     * @return string|null
     */
    protected function clean_option($value, array $options): ?string {
        $method = new \ReflectionMethod($this->restore, 'clean_option');
        $method->setAccessible(true);
        return $method->invoke($this->restore, $value, $options);
    }

    /**
     * A label carrying HTML (as a hand-crafted backup could) is stripped down to its text.
     */
    public function test_clean_label_strips_html(): void {
        $this->assertSame('', $this->clean_label('<img src=x onerror=alert(1)>'));
        $this->assertSame('Hello world', $this->clean_label('Hello <b>world</b>'));
        $this->assertNull($this->clean_label(null));
    }

    /**
     * An option value is only kept if it is one of the allowed identifiers.
     */
    public function test_clean_option_rejects_unknown_values(): void {
        $options = config::get_colour_options();

        $this->assertSame('danger', $this->clean_option('danger', $options));
        $this->assertNull($this->clean_option('<script>evil</script>', $options));
        $this->assertNull($this->clean_option('not-a-real-colour', $options));
        $this->assertNull($this->clean_option(null, $options));
        $this->assertNull($this->clean_option('', $options));
    }
}
