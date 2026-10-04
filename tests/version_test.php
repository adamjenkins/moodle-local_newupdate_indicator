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

/**
 * Tests for the plugin's version.php metadata consistency.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
final class version_test extends \basic_testcase {
    /**
     * Release build numbers (the x.y.0 core $version) of each Moodle branch.
     *
     * Read from the vX.Y.0 tags of core's version.php.
     */
    private const BRANCH_FLOORS = [
        500 => 2025041400,
        501 => 2025100600,
        502 => 2026042000,
    ];

    /**
     * $plugin->requires must not be below the lowest branch in $plugin->supported.
     */
    public function test_requires_matches_lowest_supported_branch(): void {
        $plugin = new \stdClass();
        require(__DIR__ . '/../version.php');

        $this->assertIsArray($plugin->supported);
        $lowest = min($plugin->supported);
        $this->assertArrayHasKey($lowest, self::BRANCH_FLOORS, "Unknown floor for branch {$lowest}");
        $this->assertGreaterThanOrEqual(
            self::BRANCH_FLOORS[$lowest],
            $plugin->requires,
            "\$plugin->requires is below the release build of the lowest supported branch {$lowest}"
        );
    }
}
