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
 * @package    tool_backupmanager
 * @copyright  2015 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

class tool_backupmanager {

    public static function get_by_course($filearea = 'course', $olderthantime = null) {
        global $DB;

        if (is_null($olderthantime)) $olderthantime = time();

        $sql = "
            SELECT
                c.id,
                c.shortname,
                c.fullname,
                ctx.id as contextid,
                COUNT(*) as count,
                SUM(filesize) as filesize,
                SUM(CASE WHEN f.timecreated < $olderthantime THEN 1 ELSE 0 END) as oldcount,
                SUM(CASE WHEN f.timecreated < $olderthantime THEN filesize ELSE 0 END) as oldfilesize
            FROM
                {files} f,
                {context} ctx,
                {course} c
            WHERE
                f.contextid = ctx.id  AND
                ctx.contextlevel = 50 AND
                ctx.instanceid = c.id AND
                component = 'backup' AND
                filearea = ? AND
                filesize > 0
            GROUP BY 
                c.id,c.shortname,c.fullname
            ORDER BY 
                filesize desc
        ";

        return $DB->get_records_sql($sql, array($filearea));
    }

    public static function get_by_user($olderthantime = null) {
        global $DB;

        if (is_null($olderthantime)) $olderthantime = time();

        $sql = "
            SELECT
                u.id,
                u.lastname,
                u.firstname,
                ctx.id as contextid,
                COUNT(*) as count,
                SUM(filesize) as filesize,
                SUM(CASE WHEN f.timecreated < $olderthantime THEN 1 ELSE 0 END) as oldcount,
                SUM(CASE WHEN f.timecreated < $olderthantime THEN filesize ELSE 0 END) as oldfilesize
            FROM
                {files} f,
                {context} ctx,
                {user} u
            WHERE
                f.contextid = ctx.id  AND
                ctx.contextlevel = ".CONTEXT_USER." AND
                ctx.instanceid = u.id AND
                component = 'user' AND
                filearea = 'backup' AND
                filesize > 0
            GROUP BY 
                u.id,u.lastname,u.firstname
            ORDER BY 
                filesize desc
        ";

        return $DB->get_records_sql($sql);
    }

    public static function purge_backup_area($zone, $contextid, $olderthan) {
        global $DB;

        if (is_null($olderthan)) {
            $olderthan = time();
        }

        $fs = get_file_storage();

        $parts = self::purge_get_zone_components($zone);
        if ($parts) {
            list($component, $filearea) = $parts;
            $files = $fs->get_area_files($contextid, $component, $filearea, false, "itemid, filepath, filename", false);
            if ($files) {
                foreach ($files as $file) {
                    if ($olderthan > $file->get_timecreated()) {
                        $file->delete();
                    }
                }
            }
        }
    }

    public static function purge_backup_zone($zone, $olderthan = null) {
        global $DB;

        if (is_null($olderthan)) {
            $olderthan = time();
        }

        if ($parts = self::purge_get_zone_components($zone)) {

            $fs = get_file_storage();

            list($component, $filearea) = $parts;
            $zonefiles = $DB->get_records_select('files', " component = '$component' AND filearea = '$filearea' AND timecreated < ? AND filesize > 0 ", array($olderthan), 'id, id');
            if ($zonefiles) {
                foreach ($zonefiles as $fid => $fid) {
                    $storedfile = $fs->get_file_by_id($fid);
                    $storedfile->delete();
                }
            }
        }
    }
    
    /**
     * Preprocesses the olderthan parameter depending on format
     * @return int the olderthan in months (multiple of 30 days period)
     */
    public static function resolve_olderthan() {
        $olderthan = optional_param('olderthan', 2, PARAM_INT);
        if ($olderthan > 1000) {
            // We got a timestamp, so get the shift in month
            $olderthan = floor((time() - $olderthan) / (30 * DAYSECS));
        }
        return $olderthan;
    }

    protected static function purge_get_zone_components($zone) {

        switch($zone) {
            case 'user':
                $component = 'user';
                $filearea = 'backup';
                break;

            case 'course':
                $component = 'backup';
                $filearea = 'course';
                break;

            case 'automated':
                $component = 'backup';
                $filearea = 'automated';
                break;

            case 'publishflow':
                $component = 'backup';
                $filearea = 'publishflow';
                break;

            default:
                return null;
        }

        return (array($component, $filearea));
    }
}