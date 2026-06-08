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
 * Injects "new"/"updated" indicator badges next to activity links on the course page,
 * and optionally a short list of recently added/updated content at the top of the
 * course content area.
 *
 * The server pre-renders all markup (badges and the recent content list); this module
 * is only responsible for placing that markup in the right spot in the DOM, since there
 * is no core hook that lets a local plugin add content directly to each activity item.
 *
 * @module      local_newupdate_indicator/indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const SELECTORS = {
    activityCard: '.activity-item',
    activityName: '.activityname',
    courseContent: '#region-main .course-content',
};

/** Positions that are placed in a corner of the activity item using absolute positioning. */
const CORNER_POSITIONS = ['topleft', 'topright', 'bottomleft', 'bottomright'];

/** Marker class added to the activity card to give corner badges a positioning context. */
const POSITIONED_CLASS = 'local-newupdate-indicator-positioned';

/**
 * Returns the course module list item element for a given course module id.
 *
 * @param {Number} cmid
 * @return {Element|null}
 */
const getActivityItem = (cmid) => document.querySelector(`[data-for="cmitem"][data-id="${cmid}"]`);

/**
 * Converts a markup string into a single detached element.
 *
 * @param {String} html
 * @return {Element|null}
 */
const createElementFromHtml = (html) => {
    const wrapper = document.createElement('div');
    wrapper.innerHTML = html.trim();
    return wrapper.firstElementChild;
};

/**
 * Inserts a pre-rendered indicator badge at the configured position within an activity item.
 *
 * @param {Element} activityItem The course module list item ("cmitem")
 * @param {String} position One of: beforelink, afterlink, topleft, topright, bottomleft, bottomright
 * @param {String} html Pre-rendered badge markup
 * @return {void}
 */
const insertBadge = (activityItem, position, html) => {
    const badge = createElementFromHtml(html);
    if (!badge) {
        return;
    }

    if (CORNER_POSITIONS.includes(position)) {
        const card = activityItem.querySelector(SELECTORS.activityCard);
        if (card) {
            card.classList.add(POSITIONED_CLASS);
            card.appendChild(badge);
        }
        return;
    }

    const nameArea = activityItem.querySelector(SELECTORS.activityName);
    if (!nameArea || !nameArea.parentNode) {
        return;
    }

    if (position === 'beforelink') {
        nameArea.parentNode.insertBefore(badge, nameArea);
    } else {
        nameArea.parentNode.insertBefore(badge, nameArea.nextSibling);
    }
};

/**
 * Prepends the pre-rendered "recent content" list to the top of the course content area.
 *
 * @param {String} html Pre-rendered list markup
 * @return {void}
 */
const insertRecentList = (html) => {
    const container = document.querySelector(SELECTORS.courseContent);
    const list = createElementFromHtml(html);
    if (container && list) {
        container.insertAdjacentElement('afterbegin', list);
    }
};

/**
 * Initialises the indicator overlay for the current course page.
 *
 * @param {Object} data
 * @param {Array} data.badges Array of {cmid, position, html} describing each indicator badge
 * @param {String|null} data.recentlisthtml Pre-rendered recent content list markup, or null
 * @return {void}
 */
export const init = (data) => {
    if (!data) {
        return;
    }

    (data.badges || []).forEach((badge) => {
        const activityItem = getActivityItem(badge.cmid);
        if (activityItem) {
            insertBadge(activityItem, badge.position, badge.html);
        }
    });

    if (data.recentlisthtml) {
        insertRecentList(data.recentlisthtml);
    }
};
