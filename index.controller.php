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

/**
 * Backup files management tool.
 *
 * @package    tool
 * @subpackage backupmanager
 * @copyright  2015 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('MOODLE_INTERNAL')) die('You cannot use this script this way');

if ($action == 'purgezone') {
    $zone = required_param('zone', PARAM_TEXT);
    $contextid = required_param('contextid', PARAM_INT);
    $olderthan = optional_param('olderthan', null, PARAM_INT);

    tool_backupmanager::purge_backup_area($zone, $contextid, $olderthan);
}
if ($action == 'bulkdelete') {
    $zone = required_param('zone', PARAM_TEXT);
    $contexts = required_param_array('contextid', PARAM_INT);
    $olderthan = optional_param('olderthan', null, PARAM_INT);

    if ($goold = optional_param('go_old', false, PARAM_TEXT)) {
        $olderthantime = $olderthan;
    } else {
        $olderthantime = null;
    }

    if ($contexts) {
        foreach($contexts as $contextid) {
            tool_backupmanager::purge_backup_area($zone, $contextid, $olderthantime);
        }
    }
}
if ($action == 'deleteall') {
    $olderthan = optional_param('olderthan', null, PARAM_INT);
    tool_backupmanager::purge_backup_zone('course', $olderthan);
    tool_backupmanager::purge_backup_zone('user', $olderthan);
    tool_backupmanager::purge_backup_zone('automated', $olderthan);
}
if ($action == 'deletezone') {
    $zone = required_param('zone', PARAM_TEXT);
    $olderthan = optional_param('olderthan', null, PARAM_INT);
    tool_backupmanager::purge_backup_zone($zone, $olderthan);
}
if ($action == 'cleartrash') {
    fulldelete($CFG->dataroot.'/trashdir');
}
