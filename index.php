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
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/admin/tool/backupmanager/classes/tool_backupmanager.php');
$PAGE->requires->js('/admin/tool/backupmanager/js/backupmanager.js');

// page parameters

admin_externalpage_setup('toolbackupmanager');

$fs = get_file_storage();

$action = optional_param('what', '', PARAM_TEXT);
if ($action) {
    include($CFG->dirroot.'/admin/tool/backupmanager/index.controller.php');
}

$haspublishflow = is_dir($CFG->dirroot.'/blocks/publishflow');

$globals = new StdClass();
$globals->olderthan = tool_backupmanager::resolve_olderthan();
$olderthantime = $globals->olderthantime = time() - DAYSECS * 30 * $globals->olderthan;

$coursebackups = tool_backupmanager::get_by_course('course', $olderthantime);
$automatedbackups = tool_backupmanager::get_by_course('automated', $olderthantime);
$userbackups = tool_backupmanager::get_by_user($olderthantime);

$globals->automatedbackups = 0 + $DB->count_records_select('files', " component = 'backup' AND filearea = 'automated' AND filesize > 0 ", array());
$globals->coursebackups = 0 + $DB->count_records_select('files', " component = 'backup' AND filearea = 'course' AND filesize > 0 ", array());
$globals->userbackups = 0 + $DB->count_records_select('files', " component = 'user' AND filearea = 'backup' AND filesize > 0 ", array());
$globals->allbackups = $globals->coursebackups + $globals->userbackups + $globals->automatedbackups;

$globals->automatedbackupsize = 0 + $DB->get_field('files', 'SUM(filesize)', array('component' => 'backup', 'filearea' => 'automated'));
$globals->coursebackupsize = 0 + $DB->get_field('files', 'SUM(filesize)', array('component' => 'backup', 'filearea' => 'course'));
$globals->userbackupsize = 0 + $DB->get_field('files', 'SUM(filesize)', array('component' => 'user', 'filearea' => 'backup'));
$globals->allbackupsize = $globals->coursebackupsize + $globals->userbackupsize + $globals->automatedbackupsize;

// Manage non stanard publishflow backeup repo
if ($haspublishflow) {
    $globals->publishflowbackups = 0 + $DB->count_records_select('files', " component = 'backup' AND filearea = 'publishflow' AND filesize > 0 ", array());
    $globals->publishflowbackupsize = 0 + $DB->get_field('files', 'SUM(filesize)', array('component' => 'backup', 'filearea' => 'publishflow'));
    $globals->allbackups += $globals->publishflowbackups;
    $globals->allbackupsize += $globals->publishflowbackupsize;
}

$globals->oldautomatedbackups = 0 + $DB->count_records_select('files', " component = 'backup' AND filearea = 'automated' AND timecreated < ? AND filesize > 0 ", array($olderthantime));
$globals->oldcoursebackups = 0 + $DB->count_records_select('files', " component = 'backup' AND filearea = 'course' AND timecreated < ? AND filesize > 0 ", array($olderthantime));
$globals->olduserbackups = 0 + $DB->count_records_select('files', " component = 'user' AND filearea = 'backup' AND timecreated < ?  AND filesize > 0 ", array($olderthantime));
$globals->oldautomatedbackupsize = 0 + $DB->get_field_select('files', 'SUM(filesize)', " component = 'backup' AND filearea = 'automated' AND timecreated < ? ", array($olderthantime));
$globals->oldcoursebackupsize = 0 + $DB->get_field_select('files', 'SUM(filesize)', " component = 'backup' AND filearea = 'course' AND timecreated < ? ", array($olderthantime));
$globals->olduserbackupsize = 0 + $DB->get_field_select('files', 'SUM(filesize)', " component = 'user' AND filearea = 'backup' AND timecreated < ? ", array($olderthantime));

$globals->oldallbackups = $globals->oldcoursebackups + $globals->olduserbackups + $globals->oldautomatedbackups;
$globals->oldallbackupsize = $globals->oldcoursebackupsize + $globals->oldautomatedbackupsize;

if ($haspublishflow) {
    $globals->oldpublishflowbackups = 0 + $DB->count_records_select('files', " component = 'backup' AND filearea = 'publishflow' AND timecreated < ? AND filesize > 0 ", array($olderthantime));
    $globals->oldpublishflowbackupsize = 0 + $DB->get_field_select('files', 'SUM(filesize)', " component = 'backup' AND filearea = 'publishflow' AND timecreated < ? ", array($olderthantime));
    $globals->oldallbackups += $globals->oldpublishflowbackups;
    $globals->oldallbackupsize += $globals->oldpublishflowbackupsize;
}

$renderer = $PAGE->get_renderer('tool_backupmanager');

// Header
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('backupmanagement', 'tool_backupmanager'));

echo $renderer->olderthanform($globals->olderthan);

echo $OUTPUT->heading(get_string('general', 'tool_backupmanager'), 3);

echo $renderer->globals($globals);

echo $OUTPUT->heading(get_string('coursedetail', 'tool_backupmanager'), 3);

echo $renderer->coursebackups($coursebackups, 'course', $olderthantime);

echo $OUTPUT->heading(get_string('userdetail', 'tool_backupmanager'), 3);

echo $renderer->userbackups($userbackups, $olderthantime);

echo $OUTPUT->heading(get_string('automateddetail', 'tool_backupmanager'), 3);

echo $renderer->coursebackups($automatedbackups, 'automated', $olderthantime);

if ($haspublishflow) {
    echo $OUTPUT->heading(get_string('otherdetail', 'tool_backupmanager'), 2);
    
    echo $OUTPUT->heading(get_string('publishflowdetail', 'tool_backupmanager'), 3);
    $publishflowbackups = tool_backupmanager::get_by_course('publishflow', $olderthantime);
    echo $renderer->coursebackups($publishflowbackups, 'publishflow', $olderthantime);
}

// Footer.
echo $OUTPUT->footer();

