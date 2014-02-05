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

require_once('../../config.php');
require_once('../../course/moodleform_mod.php');
require_once('lib.php');
require_once('locallib.php');
require_once($CFG->libdir.'/formslib.php');

$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url('/mod/thesis/view.php', array('id'=>$id));

if (! $cm = get_coursemodule_from_id('thesis', $id)) {
  print_error('invalidcoursemodule');
}
if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
  print_error('coursemisconf');
}
if (! $thesis = $DB->get_record("thesis", array("id" => $cm->instance))) {
  print_error('invalidthesisid', 'thesis');
}
require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_context($context);

echo $OUTPUT->header();
echo '<div class="thesis_list">';
echo $OUTPUT->heading(get_string('view_page_title', 'mod_thesis'), 2);
echo '<a class="thesis_new" href="edit.php?id='.$id.'">'.get_string('create_submission', 'mod_thesis').'</a>';
echo thesis_list_submissions($id,$thesis->id, context_course::instance($cm->course));
echo '</div>';
echo $OUTPUT->footer();
