<?php

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

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$PAGE->set_context($context);

thesis_cron();

echo $OUTPUT->header();
echo '<div class="thesis_list">';
echo '<h2>Thesis/Dissertation Submissions</h2>';
echo '<a class="thesis_new" href="edit.php?id='.$id.'">(Create new submission)</a>';
echo thesis_list_submissions($id,$thesis->id, context_course::instance($cm->course));
echo '</div>';
echo $OUTPUT->footer();
