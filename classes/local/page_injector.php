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

namespace local_newupdate_indicator\local;

/**
 * Builds the data structure handed to the indicator AMD module for a course page.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_injector {
    /**
     * Builds the JS payload for a course, or null if nothing should be displayed.
     *
     * @param \stdClass $course
     * @return array|null
     */
    public static function build_page_data(\stdClass $course): ?array {
        global $OUTPUT;

        $config = config::get_for_course($course->id);
        if (empty($config->newenabled) && empty($config->updatedenabled)) {
            return null;
        }

        $indicators = indicator_finder::get_indicators($course->id, $config);
        if (empty($indicators)) {
            return null;
        }

        $modinfo = get_fast_modinfo($course->id);
        $cms = $modinfo->get_cms();

        $badges = [];
        $listitems = [];

        foreach ($indicators as $cmid => $info) {
            if (!isset($cms[$cmid])) {
                continue;
            }

            ['badge' => $badge, 'listitem' => $listitem] = self::build_indicator_entry($cms[$cmid], $info, $config);
            $badges[] = $badge;
            $listitems[] = $listitem;
        }

        if (empty($badges)) {
            return null;
        }

        $recentlisthtml = null;
        if (!empty($config->showrecentlist)) {
            usort($listitems, function (\stdClass $a, \stdClass $b): int {
                return $b->timestamp - $a->timestamp;
            });
            $limit = max(1, (int) $config->recentlistlimit);
            $recentlisthtml = $OUTPUT->render_from_template('local_newupdate_indicator/recentlist', [
                'title' => get_string('recentlisttitle', 'local_newupdate_indicator'),
                'items' => array_slice($listitems, 0, $limit),
            ]);
        }

        return [
            'badges' => $badges,
            'recentlisthtml' => $recentlisthtml,
        ];
    }

    /**
     * Builds the badge and recent-list-item data for a single indicator.
     *
     * @param \cm_info $cm
     * @param \stdClass $info Indicator info with 'status' and 'timestamp' properties
     * @param \stdClass $config The effective course configuration
     * @return array{badge: array, listitem: \stdClass}
     */
    protected static function build_indicator_entry(\cm_info $cm, \stdClass $info, \stdClass $config): array {
        if ($info->status === indicator_finder::STATUS_NEW) {
            $statusclass = 'new';
            $label = $config->newlabel;
            $iconidentifier = $config->newicon;
            $colour = $config->newcolour;
        } else {
            $statusclass = 'updated';
            $label = $config->updatedlabel;
            $iconidentifier = $config->updatedicon;
            $colour = $config->updatedcolour;
        }

        $iconhtml = config::render_icon($iconidentifier, $label);
        $cmurl = $cm->url;

        return [
            'badge' => [
                'cmid' => $cm->id,
                'position' => $config->position,
                'html' => self::render_badge($statusclass, $colour, $config->position, $iconhtml, $label),
            ],
            'listitem' => (object) [
                'cmid' => $cm->id,
                'name' => $cm->get_formatted_name(),
                'url' => $cmurl ? $cmurl->out(false) : null,
                'status' => $statusclass,
                'colour' => $colour,
                'label' => $label,
                'iconhtml' => $iconhtml,
                'timestamp' => $info->timestamp,
            ],
        ];
    }

    /**
     * Renders the markup for a single indicator badge.
     *
     * @param string $statusclass 'new' or 'updated'
     * @param string $colour One of {@see config::colour_identifiers()}
     * @param string $position One of {@see config::position_identifiers()}
     * @param string $iconhtml Pre-rendered icon markup, or an empty string
     * @param string $label The indicator label text
     * @return string
     */
    protected static function render_badge(
        string $statusclass,
        string $colour,
        string $position,
        string $iconhtml,
        string $label
    ): string {
        $classes = "local-newupdate-indicator local-newupdate-indicator-{$statusclass} "
            . "local-newupdate-indicator-colour-{$colour} local-newupdate-indicator-pos-{$position}";

        $content = '';
        if ($iconhtml !== '') {
            $content .= \html_writer::tag('span', $iconhtml, ['class' => 'local-newupdate-indicator-icon']);
        }
        $content .= \html_writer::tag('span', s($label), ['class' => 'local-newupdate-indicator-label']);

        return \html_writer::tag('span', $content, ['class' => $classes]);
    }
}
