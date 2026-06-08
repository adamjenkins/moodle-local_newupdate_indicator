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

namespace local_newupdate_indicator\privacy;

/**
 * Privacy provider.
 *
 * This plugin only stores per-course display preferences (which labels, icons,
 * positions and time-spans to use). No personal data is stored or processed.
 *
 * @package    local_newupdate_indicator
 * @copyright  2026 Adam Jenkins <adam@wisecat.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\null_provider {
    /**
     * Returns the language string explaining why no personal data is stored.
     *
     * @return string the identifier of the explanatory language string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
