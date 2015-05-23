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
 * Defines the renderer for the backup central manager helper plugin.
 *
 * @package    tool_backupmanager
 * @copyright  2015 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define ('KILOBYTE', 1024);
define ('MEGABYTE', 1048576);
define ('GIGABYTE', 1073741824);
define ('TERABYTE', 1099511627776);

/**
 * Renderer for the assignment upgrade helper plugin.
 *
 * @package    tool_assignmentupgrade
 * @copyright  2015 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_backupmanager_renderer extends plugin_renderer_base {

    function globals($globals) {
        global $CFG, $OUTPUT;

        $haspublishflow = is_dir($CFG->dirroot.'/blocks/publishflow');

        $str = '';

        $str .= '<table width="100%" class="tool-backupmanager generaltable">';

        $str .= '<tr>';
        $str .= '<th>'.get_string('allbackups', 'tool_backupmanager').'</th>';
        $str .= '<th>'.$globals->allbackups.'</th>';
        $str .= '<th>'.$this->format_size($globals->allbackupsize).'</th>';
        $str .= '</tr>';

        $str .= '<tr>';
        $str .= '<th>'.get_string('coursebackups', 'tool_backupmanager').'</th>';
        $str .= '<td>'.$globals->coursebackups.'</td>';
        $str .= '<td>'.$this->format_size($globals->coursebackupsize).'</td>';
        $str .= '</tr>';

        $str .= '<tr>';
        $str .= '<th>'.get_string('automatedbackups', 'tool_backupmanager').'</th>';
        $str .= '<td>'.$globals->automatedbackups.'</td>';
        $str .= '<td>'.$this->format_size($globals->automatedbackupsize).'</td>';
        $str .= '</tr>';

        $str .= '<tr>';
        $str .= '<th>'.get_string('userbackups', 'tool_backupmanager').'</th>';
        $str .= '<td>'.$globals->userbackups.'</td>';
        $str .= '<td>'.$this->format_size($globals->userbackupsize).'</td>';
        $str .= '</tr>';

        if ($haspublishflow) {
            $str .= '<tr>';
            $str .= '<th>'.get_string('publishflowbackups', 'tool_backupmanager').'</th>';
            $str .= '<td>'.$globals->publishflowbackups.'</td>';
            $str .= '<td>'.$this->format_size($globals->publishflowbackupsize).'</td>';
            $str .= '</tr>';
        }

        $str .= '<tr>';
        $str .= '<td colspan="3" class="tool-backupmaanger-controls">';
        $str .= get_string('remove', 'tool_backupmanager');
        $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deleteall'));
        $str .= '<a href="'.$deleteurl.'">'.get_string('deleteall', 'tool_backupmanager').'</a>';
        $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deletezone', 'zone' => 'course'));
        $str .= ' - <a href="'.$deleteurl.'">'.get_string('deletecourse', 'tool_backupmanager').'</a>';
        $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deletezone', 'zone' => 'automated'));
        $str .= ' - <a href="'.$deleteurl.'">'.get_string('deleteautomated', 'tool_backupmanager').'</a>';
        $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deletezone', 'zone' => 'user'));
        $str .= ' - <a href="'.$deleteurl.'">'.get_string('deleteuser', 'tool_backupmanager').'</a>';
        if ($haspublishflow) {
            $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deletezone', 'zone' => 'publishflow'));
            $str .= ' - <a href="'.$deleteurl.'">'.get_string('deletepublishflow', 'tool_backupmanager').'</a>';
        }
        $str .= '</td>';
        $str .= '</tr>';

        $str .= '<tr>';
        $str .= '<th colspan="3">'.$OUTPUT->heading(get_string('olderthan', 'tool_backupmanager', $globals->olderthan),3).'</th>';
        $str .= '</tr>';

        $str .= '<tr>';
        $str .= '<th>'.get_string('allbackups', 'tool_backupmanager').'</th>';
        $str .= '<th>'.$globals->oldallbackups.'</th>';
        $str .= '<th>'.$this->format_size($globals->oldallbackupsize).'</th>';
        $str .= '</tr>';

        $str .= '<tr>';
        $str .= '<th>'.get_string('coursebackups', 'tool_backupmanager').'</th>';
        $str .= '<td>'.$globals->oldcoursebackups.'</td>';
        $str .= '<td>'.$this->format_size($globals->oldcoursebackupsize).'</td>';
        $str .= '</tr>';

        $str .= '<tr>';
        $str .= '<th>'.get_string('automatedbackups', 'tool_backupmanager').'</th>';
        $str .= '<td>'.$globals->oldautomatedbackups.'</td>';
        $str .= '<td>'.$this->format_size($globals->oldautomatedbackupsize).'</td>';
        $str .= '</tr>';

        $str .= '<tr>';
        $str .= '<th>'.get_string('userbackups', 'tool_backupmanager').'</th>';
        $str .= '<td>'.$globals->olduserbackups.'</td>';
        $str .= '<td>'.$this->format_size($globals->olduserbackupsize).'</td>';
        $str .= '</tr>';

        if ($haspublishflow) {
            $str .= '<tr>';
            $str .= '<th>'.get_string('publishflowbackups', 'tool_backupmanager').'</th>';
            $str .= '<td>'.$globals->oldpublishflowbackups.'</td>';
            $str .= '<td>'.$this->format_size($globals->oldpublishflowbackupsize).'</td>';
            $str .= '</tr>';
        }

        $str .= '<tr>';
        $str .= '<td colspan="3" class="tool-backupmaanger-controls">';
        $str .= get_string('remove', 'tool_backupmanager');
        $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deleteall', 'olderthan' => $globals->olderthan));
        $str .= '<a href="'.$deleteurl.'">'.get_string('deleteoldall', 'tool_backupmanager').'</a>';
        $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deletezone', 'zone' => 'course', 'olderthan' => $globals->olderthan));
        $str .= ' - <a href="'.$deleteurl.'">'.get_string('deleteoldcourse', 'tool_backupmanager').'</a>';
        $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deletezone', 'zone' => 'automated', 'olderthan' => $globals->olderthan));
        $str .= ' - <a href="'.$deleteurl.'">'.get_string('deleteoldautomated', 'tool_backupmanager').'</a>';
        $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deletezone', 'zone' => 'user', 'olderthan' => $globals->olderthan));
        $str .= ' - <a href="'.$deleteurl.'">'.get_string('deleteolduser', 'tool_backupmanager').'</a>';
        if ($haspublishflow) {
            $deleteurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'deletezone', 'zone' => 'publishflow', 'olderthan' => $globals->olderthan));
            $str .= ' - <a href="'.$deleteurl.'">'.get_string('deleteoldpublishflow', 'tool_backupmanager').'</a>';
        }
        $str .= '</td>';
        $str .= '</tr>';

        $str .= '<tr>';
        $str .= '<th colspan="3">'.$OUTPUT->heading(get_string('trashdir', 'tool_backupmanager'), 3).'</th>';
        $str .= '</tr>';

        $cmd = 'du -s -b '.$CFG->dataroot.'/trashdir | cut -f1';
        $result = exec($cmd, $trashsize);

        $cmd = 'find '.$CFG->dataroot.'/trashdir -type f | wc -l';
        $result = exec($cmd, $trashcount);

        $params = array('what' => 'cleartrash', 'olderthan' => $globals->olderthan);
        $cleartrashbutton = $OUTPUT->single_button(new moodle_url('/admin/tool/backupmanager/index.php', $params), get_string('clear', 'tool_backupmanager'));

        $str .= '<tr>';
        $str .= '<th>'.get_string('trashfiles', 'tool_backupmanager').'</th>';
        $str .= '<td>'.implode('<br/>', $trashcount).'</td>';
        $str .= '<td>'.$this->format_size(0 + implode('<br/>', $trashsize)).'</td>';
        $str .= '<td style="text-align:right">'.$cleartrashbutton.'</td>';
        $str .= '</tr>';

        $str .= '</table>';

        return $str;
    }

    function coursebackups(&$coursebackups, $filearea = 'course', $olderthantime) {
        $str = '';

        $courseshortstr = get_string('shortname');
        $coursestr = get_string('name');
        $countstr = get_string('count', 'tool_backupmanager');
        $filesizestr = get_string('size');
        $countoldstr = get_string('countold', 'tool_backupmanager');
        $filesizeoldstr = get_string('sizeold', 'tool_backupmanager');
        $purgestr = get_string('purge', 'tool_backupmanager');

        $table = new html_table();
        $table->head = array('', $courseshortstr, $coursestr, $countstr, $filesizestr, $countoldstr, $filesizeoldstr, $purgestr);
        $table->size = array('2%', '8%', '40%', '10%', '10%', '10%', '10%', '10%');
        $table->width = '100%';
        $table->align = array('left', 'left', 'left', 'center', 'left', 'center', 'left', 'right');

        foreach($coursebackups as $c) {
            $select = '<input class="'.$filearea.'sel" type="checkbox" name="contextid[]" value="'.$c->contextid.'" onclick="purge_checkstate(\''.$filearea.'\')" />';
            $courseurl = new moodle_url('/backup/restorefile.php', array('contextid' => $c->contextid));
            $courselink = html_writer::tag('a', $c->shortname, array('href' => $courseurl));
            $purgeurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'purgezone', 'zone' => $filearea, 'contextid' => $c->contextid));
            $purgeoldurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'purgezone', 'zone' => $filearea, 'contextid' => $c->contextid, 'olderthan' => $olderthantime));
            if ($c->count) {
                $commands = '<a class="smallink" href="'.$purgeurl.'">'.get_string('all', 'tool_backupmanager').'</a>';
            } else {
                $commands = '<span class="dimmed-text">'.get_string('all', 'tool_backupmanager').'</span>';
            }
            if ($c->oldcount) {
                $commands .= ' / <a class="smallink" href="'.$purgeoldurl.'">'.get_string('old', 'tool_backupmanager').'</a>';
            } else {
                $commands .= ' / <span class="dimmed-text">'.get_string('old', 'tool_backupmanager').'</span>';
            }
            $class = ($c->count != $c->oldcount) ? 'different' : '';
            $table->data[] = array($select, $courselink, $c->fullname, '<span class="'.$class.'">'.$c->count.'</span>', $this->format_size($c->filesize).$this->size_bar($c->filesize), '<span class="'.$class.'">'.$c->oldcount.'</span>', $this->format_size($c->oldfilesize).$this->size_bar($c->oldfilesize), $commands);
        }

        $str .= '<form name="'.$filearea.'backupdeleteform">';
        $str .= '<input type="hidden" name="what" value="bulkdelete" />';
        $str .= '<input type="hidden" name="zone" value="'.$filearea.'" />';
        $str .= '<input type="hidden" name="olderthan" value="'.$olderthantime.'" />';
        $str .= html_writer::table($table, true);
        $str .= '<div class="tool-backupmanager-controls">';
        $str .= '<a href="javascript:purge_selectall(\''.$filearea.'\')">'.get_string('selectall').'</a>';
        $str .= ' - <a href="javascript:purge_deselectall(\''.$filearea.'\')">'.get_string('deselectall').'</a>';
        $str .= ' - <input id="'.$filearea.'-submit" disabled="disabled" type="submit" name="go" value="'.get_string('deleteselection', 'tool_backupmanager').'" />';
        $str .= ' <input id="'.$filearea.'-old-submit" disabled="disabled" type="submit" name="go_old" value="'.get_string('deleteselectionold', 'tool_backupmanager').'" />';
        $str .= '</div>'; 
        $str .= '</form>'; 

        return $str;
    }

    function userbackups(&$userbackups, $olderthantime) {
        $str = '';

        $lastnamestr = get_string('lastname');
        $firstnamestr = get_string('firstname');
        $countstr = get_string('count', 'tool_backupmanager');
        $filesizestr = get_string('size');
        $countoldstr = get_string('countold', 'tool_backupmanager');
        $filesizeoldstr = get_string('sizeold', 'tool_backupmanager');
        $purgestr = get_string('purge', 'tool_backupmanager');

        $table = new html_table();
        $table->head = array('', $lastnamestr, $firstnamestr, $countstr, $filesizestr, $countoldstr, $filesizeoldstr, $purgestr);
        $table->size = array('2%', '8%', '40%', '10%', '10%', '10%', '10%', '10%');
        $table->width = '100%';
        $table->align = array('left', 'left', 'left', 'center', 'left', 'center', 'left', 'right');

        foreach($userbackups as $ub) {
            $select = '<input class="usersel" type="checkbox" name="contextid[]" value="'.$ub->contextid.'" onclick="purge_checkstate(\'user\')" />';
            $purgeurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'purgezone', 'zone' => 'user', 'contextid' => $ub->contextid));
            $purgeoldurl = new moodle_url('/admin/tool/backupmanager/index.php', array('what' => 'purgezone', 'zone' => 'user', 'contextid' => $ub->contextid, 'olderthan' => $olderthantime));
            if ($ub->count) {
                $commands = '<a class="smallink" href="'.$purgeurl.'">'.get_string('all', 'tool_backupmanager').'</a>';
            } else {
                $commands = '<span class="dimmed-text">'.get_string('all', 'tool_backupmanager').'</span>';
            }
            if ($ub->oldcount) {
                $commands .= ' / <a class="smallink" href="'.$purgeoldurl.'">'.get_string('old', 'tool_backupmanager').'</a>';
            } else {
                $commands .= ' / <span class="dimmed-text">'.get_string('old', 'tool_backupmanager').'</span>';
            }
            $table->data[] = array($select, $ub->lastname, $ub->firstname, $ub->count, $this->format_size($ub->filesize).$this->size_bar($ub->filesize), $ub->oldcount, $this->format_size($ub->oldfilesize).$this->size_bar($ub->oldfilesize), $commands);
        }

        $str .= '<form name="userbackupdeleteform">';
        $str .= '<input type="hidden" name="what" value="bulkdelete" />';
        $str .= '<input type="hidden" name="zone" value="user" />';
        $str .= '<input type="hidden" name="olderthan" value="'.$olderthantime.'" />';
        $str .= html_writer::table($table, true);
        $str .= '<div class="tool-backupmanager-controls">';
        $str .= '<a href="javascript:purge_selectall(\'user\')">'.get_string('selectall').'</a>';
        $str .= ' - <a href="javascript:purge_deselectall(\'user\')">'.get_string('deselectall').'</a>';
        $str .= ' - <input id="user-submit" disabled="disabled" type="submit" name="go" value="'.get_string('deleteselection', 'tool_backupmanager').'" />';
        $str .= ' <input id="user-old-submit" disabled="disabled" type="submit" name="go_old" value="'.get_string('deleteselectionold', 'tool_backupmanager').'" />';
        $str .= '</div>';
        $str .= '</form>';

        return $str;
    }

    private function format_size($size) {
        if ($size < 100) {
            return $size;
        }
        if ($size < MEGABYTE) {
            return sprintf('%0.1fk', $size / KILOBYTE);
        }
        if ($size < GIGABYTE) {
            return sprintf('%0.2fM', $size / MEGABYTE);
        }
        if ($size < TERABYTE) {
            return sprintf('%0.2fG', $size / GIGABYTE);
        }
        return sprintf('%0.3fT', $size / TERABYTE);
    }

    function olderthanform($olderthan) {
        $str = '';

        $url = new moodle_url('/admin/tool/backupmanager/index.php');

        $monthstr = get_string('month');
        $str .= '<form name="olderthanform" action="'.$url.'">';
        $str .= get_string('olderthanform', 'tool_backupmanager');
        $options = array('1' => '1 '.$monthstr, '2' => '2 '.$monthstr, '3' => '3 '.$monthstr, '6' => '6 '.$monthstr, '12' => '12 '.$monthstr);
        $str .= html_writer::select($options, 'olderthan', $olderthan, '', array('onchange' => 'this.form.submit()'));
        $str .= '</form>';
        
        return $str;
    }

    function size_bar($size) {
        $str = '<br/>';

        if ($size == 0) return '';

        if ($size >= KILOBYTE) {
            $str .= '<div class="kilo size-bar"></div>';
        }
        if ($size >= MEGABYTE) {
            $str .= '<div class="mega size-bar"></div>';
        }
        if ($size >= GIGABYTE) {
            $str .= '<div class="giga size-bar"></div>';
        }
        if ($size >= TERABYTE) {
            $str .= '<div class="tera size-bar"></div>';
        }

        return $str;
    }
}