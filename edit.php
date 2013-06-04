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
$published = false;
$submitted_for_publishing = false;
if($submission_id) {
  $submission = $DB->get_record('thesis_submissions',array('id'=>$submission_id));
  $published = $submission->publish != 0;
  $submitted_for_publishing = $submission->submitted_for_publishing != 0;
}

$isadmin = has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $cm->course));

$show_as_published = $published || ($submitted_for_publishing && !$isadmin);


require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$PAGE->set_context($context);

$output = '';

if(!is_enrolled($context) && !has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $cm->course))) {
  throw new moodle_exception('You are not enrolled on this module');
  exit;
}

if($show_as_published) {
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

  $form = new mod_thesis_submit_form(null,array('isadmin'=>$isadmin,'submitted_for_publishing'=>$submitted_for_publishing),'post','',array('class'=>'thesis_form'));

  //Has the form been submited?
  if( $entry = $form->get_data() ) {

    $f = 'ok';

    //Are we updating? if so do you have access to update this
    if(isset($submission)) {
      if(!$isadmin && $submission->user_id != $USER->id) {
        throw new moodle_exception('Unauthorized access to resource');
        exit;
      }
    }

    if(empty($entry->terms_accepted)) {
      redirect($CFG->wwwroot . "/mod/thesis/terms.php?id=$id");
    }

    if(isset($entry->submitpublish)) {
      $entry->submitted_for_publishing = 1;
      $f = 'publish';
    }
    if(isset($entry->submitdraft)) {
      $entry->submitted_for_publishing = 0;
    }

    if(isset($entry->publish_kar)) {
      $entry->submitted_for_publishing = 1;
      $entry->publish = 1;
      $entry->published_by = $USER->id;
    }

    thesis_create_or_update($entry,$thesis);
    file_postupdate_standard_filemanager($entry, 'publish', array(), $context, 'mod_thesis', 'publish', $entry->submission_id);
    file_postupdate_standard_filemanager($entry, 'private', array(), $context, 'mod_thesis', 'private', $entry->submission_id);
    redirect('edit.php?id='.$id.'&amp;submission_id='.$entry->submission_id.'&amp;f='.$f);
    die;

  } else {

    if(empty($submission->terms_accepted) && empty($_SESSION['thesis_terms'])) {
      $suburl = isset($submission) ? "&submission_id={$submission->id}" : "";
      redirect($CFG->wwwroot . "/mod/thesis/terms.php?id={$id}{$suburl}");
    }

    // Are we updating a record and do you have access?
    if(isset($submission)) {
      if( !has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $cm->course)) && $submission->user_id != $USER->id ) {
        throw new moodle_exception('Unauthorized access to resource');
        exit;
      }
    } else {
      $submission = new stdClass;
    }

    if(empty($submission->terms_accepted)) {
      $submission->terms_accepted = $_SESSION['thesis_terms'];
      unset($_SESSION['thesis_terms']);
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
  $message = 'Content changes saved ok.';
  if('publish' == $f) {
    $message = 'Submission queued for publishing approval.';
  }
  echo '<div class="thesis_ok notifysuccess">'.$message.' <a href="view.php?id='.$id.'">Return to submissions list</a></div>';
}

echo '<a class="thesis_back" href="view.php?id='.$id.'">Return to submissions list</a>';

if($show_as_published) {echo $output;} else {$form->display();}

echo $OUTPUT->footer();
