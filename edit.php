<?php

require_once('../../config.php');
require_once('../../course/moodleform_mod.php');
require_once('lib.php');
require_once('locallib.php');
require_once($CFG->libdir.'/formslib.php');

$id = optional_param('id', 0, PARAM_INT);
$submission_id = optional_param('submission_id', null, PARAM_INT);
$f = optional_param('f', null, PARAM_TEXT);

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/mod/thesis/edit.php', array('id'=>$id,'submission_id'=>$submission_id));

if (! $cm = get_coursemodule_from_id('thesis', $id)) {
  print_error('invalidcoursemodule');
}
if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
  print_error('coursemisconf');
}
if (! $thesis = $DB->get_record("thesis", array("id" => $cm->instance))) {
  print_error('invalidthesisid', 'thesis');
}
if($submission_id) {
  $submission = $DB->get_record('thesis_submissions',array('id'=>$submission_id));
  $published = $submission->publish != 0 ? true : false;
} else {
  $published = false;
}



require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$PAGE->set_context($context);

$output = '';

if(!is_enrolled($context) && !has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $cm->course))) {
  throw new moodle_exception('You are not enrolled on this module');
  exit;
}

if($published) {
  $heading = 'View thesis submission';
  $PAGE->set_title($heading);
  $PAGE->set_heading($heading);

  $hidden = array('id', 'thesis_id', 'user_id', 'institution', 'metadata', 'publish', 'published_by');
  $output .= '<table class="thesis">';

  foreach ($submission as $field => $fdata) {

    if(in_array($field, $hidden)) { continue;}

    $output .= '<tr>';
      $output .= '<th class="thesis_table_head">' . ucwords(str_replace('_',' ',$field)) . '</th>';

      if($field === 'publishdate') {
        $fdata = date('d-m-Y', $fdata);
      }

      $output .= '<td class="thesis_table_data">' . $fdata . '</td>';
    $output .= '</tr>';
  }

  $fs = get_file_storage();
  if($pubfs = $fs->get_area_files($context->id, 'mod_thesis', 'publish', $submission->id, '', false)) {
    $output .= thesis_listfiles($pubfs, 'Public files');
  }

  if($prifs = $fs->get_area_files($context->id, 'mod_thesis', 'private', $submission->id, '', false)) {
    $output .= thesis_listfiles($prifs, 'Private files');
  }

  $output .= '</table>';
} else {

  $heading = 'Create/update thesis';

  $isadmin = has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $cm->course));
  $form = new mod_thesis_submit_form(null,array('isadmin'=>$isadmin),'post','',array('class'=>'thesis_form'));

  //Has the form been submited?
  if( $entry = $form->get_data() ) {

    //Are we updating? if so do you have access to update this
    if(isset($submission)) {
      if(!$isadmin && $submission->user_id != $USER->id) {
        throw new moodle_exception('Unauthorized access to resource');
        exit;
      }
    }

    if(isset($entry->publish_kar)) {
      $entry->publish = 1;
      $entry->published_by = $USER->id;
    }

    thesis_create_or_update($entry,$thesis);
    file_postupdate_standard_filemanager($entry, 'publish', array(), $context, 'mod_thesis', 'publish', $entry->submission_id);
    file_postupdate_standard_filemanager($entry, 'private', array(), $context, 'mod_thesis', 'private', $entry->submission_id);
    redirect('edit.php?id='.$id.'&amp;submission_id='.$entry->submission_id.'&amp;f=ok');
    die;

  } else {


    // Are we updating a record and do you have access?
    if(isset($submission)) {
      if( !has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $cm->course)) && $submission->user_id != $USER->id ) {
        throw new moodle_exception('Unauthorized access to resource');
        exit;
      }
    } else {
      $submission = new stdClass;
    }

    $submission->submission_id = $submission_id;
    $submission->id = $id;

    file_prepare_standard_filemanager($submission, 'publish', array(), $context, 'mod_thesis', 'publish', $submission_id);
    file_prepare_standard_filemanager($submission, 'private', array(), $context, 'mod_thesis', 'private', $submission_id);
    $form->set_data($submission);
  }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);
if(null != $f) {
  echo '<div class="thesis_ok notifysuccess">Content changes saved ok. <a href="view.php?id='.$id.'">Return to submissions list</a></div>';
}

echo '<a class="thesis_back" href="view.php?id='.$id.'">Return to submissions list</a>';

if($published) {echo $output;} else {$form->display();}

echo $OUTPUT->footer();
