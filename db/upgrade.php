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
 * Upgrade steps.
 *
 * @package     local_newupdate_indicator
 * @copyright   2026 Adam Jenkins <adam@wisecat.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade steps for local_newupdate_indicator.
 *
 * @param int $oldversion The version being upgraded from
 * @return bool
 */
function xmldb_local_newupdate_indicator_upgrade(int $oldversion): bool {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2026060801) {
        $table = new xmldb_table('local_newupdate_indicator');

        $newcolour = new xmldb_field('newcolour', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'newicon');
        if (!$dbman->field_exists($table, $newcolour)) {
            $dbman->add_field($table, $newcolour);
        }

        $updatedcolour = new xmldb_field('updatedcolour', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'updatedicon');
        if (!$dbman->field_exists($table, $updatedcolour)) {
            $dbman->add_field($table, $updatedcolour);
        }

        upgrade_plugin_savepoint(true, 2026060801, 'local', 'newupdate_indicator');
    }

    if ($oldversion < 2026060802) {
        $table = new xmldb_table('local_newupdate_indicator');

        $newenabled = new xmldb_field('newenabled', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'timespan');
        if (!$dbman->field_exists($table, $newenabled)) {
            $dbman->add_field($table, $newenabled);
        }

        $updatedenabled = new xmldb_field('updatedenabled', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'newcolour');
        if (!$dbman->field_exists($table, $updatedenabled)) {
            $dbman->add_field($table, $updatedenabled);
        }

        // The single combined "enabled" override is replaced by separate per-type
        // toggles; carry its value forward into both before dropping it.
        $enabled = new xmldb_field('enabled');
        if ($dbman->field_exists($table, $enabled)) {
            $DB->execute("UPDATE {local_newupdate_indicator}
                             SET newenabled = enabled, updatedenabled = enabled
                           WHERE enabled IS NOT NULL");
            $dbman->drop_field($table, $enabled);
        }

        upgrade_plugin_savepoint(true, 2026060802, 'local', 'newupdate_indicator');
    }

    if ($oldversion < 2026060803) {
        // The capability was renamed from local/newupdateindicator:manage to
        // local/newupdate_indicator:manage to match the component name (the
        // rename itself is handled by the automatic capabilities update; any
        // role customisations of the old capability revert to the archetype
        // defaults). Event observers and course backup/restore support were
        // also added; there is no data to migrate.
        upgrade_plugin_savepoint(true, 2026060803, 'local', 'newupdate_indicator');
    }

    if ($oldversion < 2026060804) {
        // The legacy before_footer lib.php callback was migrated to the Hooks
        // API (db/hooks.php); there is no data to migrate.
        upgrade_plugin_savepoint(true, 2026060804, 'local', 'newupdate_indicator');
    }

    return true;
}
