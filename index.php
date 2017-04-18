<?php
// This file is part of Moodle http://moodle.org/
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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

$event = \mod_thesis\event\course_module_instance_list_viewed::create(array(
    'context' => context_course::instance($course->id)
));
$event->add_record_snapshot('course', $course);
$event->trigger();

$PAGE->set_url('/mod/thesis/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname . ': Thesis');
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add('Thesis');
echo $OUTPUT->header();
echo $OUTPUT->heading('Thesis');

if (!$theses = get_all_instances_in_course('thesis', $course)) {
    notice(get_string('thereareno', 'moodle', 'Thesis'), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$strname = get_string('name');
$strintro = get_string('moduleintro');
if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_' . $course->format);
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($theses as $thesis) {
    $cm = $modinfo->cms[$thesis->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($thesis->section !== $currentsection) {
            if ($thesis->section) {
                $printsection = get_section_name($course, $thesis->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $thesis->section;
        }
    } else {
        $printsection = '<span class="smallinfo">' . userdate($thesis->timemodified) . '</span>';
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    $icon = '';
    if (!empty($cm->icon)) {
        // Each url has an icon in 2.0.
        $icon = '<img src="' . $OUTPUT->pix_url($cm->icon) . '" class="activityicon" alt="' . get_string('modulename', $cm->modname) . '" /> ';
    }

    $class = $thesis->visible ? '' : 'class="dimmed"'; // Hidden modules are dimmed.
    $table->data[] = array (
        $printsection,
        "<a $class $extra href=\"view.php?id=$cm->id\">" . $icon . format_string($thesis->name) . '</a>',
        format_module_intro('url', $thesis, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();
