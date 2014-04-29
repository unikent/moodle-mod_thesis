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

require_once '../../config.php';
require_once '../../course/moodleform_mod.php';
require_once 'lib.php';
require_once 'locallib.php';
require_once $CFG->libdir.'/formslib.php';
require_once $CFG->dirroot . '/repository/lib.php';

$id = optional_param('id', 0, PARAM_INT);
$submission_id = optional_param('submission_id', null, PARAM_INT);
$f = optional_param('f', null, PARAM_TEXT);

$PAGE->set_pagelayout('admin');
$PAGE->requires->js('/mod/thesis/javascript/form.js');
$PAGE->set_url('/mod/thesis/edit.php', array('id'=>$id, 'submission_id'=>$submission_id));

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
$submission_comments = '';

if ($submission_id) {
    $submission = $DB->get_record('thesis_submissions', array('id'=>$submission_id));
    $submission->publishdate = array(
        'mon' => $submission->publish_month,
        'year' => $submission->publish_year
    );
    $published = $submission->publish != 0;
    $submitted_for_publishing = $submission->submitted_for_publishing != 0;
    $submission_comments = $submission->comments;
    $_SESSION['thesis_terms'] = $submission->terms_accepted;
}

$isadmin = has_capability('moodle/course:update', context_course::instance($cm->course));

$show_as_published = $published || ($submitted_for_publishing && !$isadmin);


require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_context($context);

$output = '';

if (!is_enrolled($context) && !has_capability('moodle/course:update', context_course::instance($cm->course))) {
    throw new moodle_exception('You are not enrolled on this module');
    exit;
}

if ($show_as_published) {

    $status = $submission->submitted_for_publishing == 1 ? ' <span class="thesis_status">('.get_string('submitted_school', 'mod_thesis').')</span>' : '';
    $status = isset($submission->publish) ? ' <span class="thesis_status">('.get_string('published', 'mod_thesis').')</span>' : $status;

    $heading = get_string('page_title_view', 'mod_thesis') . $status;
    $PAGE->set_title($heading);
    $PAGE->set_heading($heading);

    $hidden = array('id', 'thesis_id', 'user_id', 'institution', 'metadata', 'publish', 'published_by', 'submitted_for_publishing', 'publish_month', 'publish_year');
    $output .= '<table class="thesis">';

    foreach ($submission as $field => $fdata) {

        if (in_array($field, $hidden)) {
            continue;
        }

        $output .= '<tr>';

        $field = str_replace('fname', 'firstname', $field);
        $field = str_replace('sname', 'surname', $field);

        $flabel = ucwords(str_replace('_', ' ', $field));
        if ($field == 'thesis_type') {
            $flabel = get_string('thesis_type', 'mod_thesis');
        }
        $output .=   '<th class="thesis_table_head">' . $flabel . '</th>';

        if ($field === 'publishdate') {
            $fdata = sprintf("%02d", $submission->publishdate['mon']) . '/' . $submission->publishdate['year'];
        }

        if ($field == 'department') {
            $r = $DB->get_record('course_categories', array('id'=>$fdata));
            $fdata = $r != null ? $r->name : $fdata;
        }

        $output .= '<td class="thesis_table_data">' . $fdata . '</td>';
        $output .= '</tr>';
    }

    $fs = get_file_storage();
    if ($pubfs = $fs->get_area_files($context->id, 'mod_thesis', 'publish', $submission->id, '', false)) {
        $output .= thesis_listfiles($pubfs, 'Public files');
    }

    if ($prifs = $fs->get_area_files($context->id, 'mod_thesis', 'private', $submission->id, '', false)) {
        $output .= thesis_listfiles($prifs, 'Private files');
    }

    if ($prifs = $fs->get_area_files($context->id, 'mod_thesis', 'permanent', $submission->id, '', false)) {
        $output .= thesis_listfiles($prifs, 'Permanently restricted files');
    }

    $output .= '</table>';
} else {

    $heading = get_string('form_heading', 'mod_thesis');

    $form = new mod_thesis_submit_form(null, array('isadmin'=>$isadmin, 'submitted_for_publishing'=>$submitted_for_publishing, 'submission_comments'=>$submission_comments), 'post', '', array('class'=>'thesis_form'));
    $terms_accepted_by_form = $form->terms_accepted();
    $terms_accepted_already = isset($submission->terms_accepted) && $submission->terms_accepted > 0;
    $terms_accepted_in_session = !empty($_SESSION['thesis_terms']);
    $terms_accepted = ($terms_accepted_by_form || $terms_accepted_already || $terms_accepted_in_session);

    $file_options = array('subdirs'=>0, 'maxfiles'=>-1, 'accepted_types'=>'pdf', 'return_types'=>FILE_INTERNAL);

    //Has the form been submited?
    if ($form->is_cancelled()) {
        unset($_SESSION['thesis_terms']);
        redirect($CFG->wwwroot . "/mod/thesis/view.php?id=$id");
        die;
    } else if ($entry = $form->get_data()) {

        $f = 'ok';

        //Are we updating? if so do you have access to update this
        if (isset($submission)) {
            if (!$isadmin && $submission->user_id != $USER->id) {
                throw new moodle_exception('Unauthorized access to resource');
                exit;
            }
        }

        if (!$terms_accepted) {
            redirect($CFG->wwwroot . "/mod/thesis/terms.php?id=$id");
            die;
        }

        if (isset($entry->submitpublish)) {
            $entry->submitted_for_publishing = 1;
            $f = 'publish';
        }
        if (isset($entry->submitdraft)) {
            $entry->submitted_for_publishing = 0;
        }

        if (isset($entry->publish_kar)) {
            $entry->submitted_for_publishing = 1;
            $entry->publish = 1;
            $entry->published_by = $USER->id;
            $f = 'kar';
        }

        thesis_create_or_update($entry, $thesis, $isadmin);
        file_postupdate_standard_filemanager($entry, 'publish', $file_options, $context, 'mod_thesis', 'publish', $entry->submission_id);
        file_postupdate_standard_filemanager($entry, 'private', $file_options, $context, 'mod_thesis', 'private', $entry->submission_id);
        file_postupdate_standard_filemanager($entry, 'permanent', $file_options, $context, 'mod_thesis', 'permanent', $entry->submission_id);
        redirect('edit.php?id='.$id.'&amp;submission_id='.$entry->submission_id.'&amp;f='.$f);
        die;

    } else {

        if (!$terms_accepted) {
            $suburl = isset($submission) ? "&submission_id={$submission->id}" : "";
            redirect($CFG->wwwroot . "/mod/thesis/terms.php?id={$id}{$suburl}");
            die;
        }

        // Are we updating a record and do you have access?
        if (isset($submission)) {
            if (!has_capability('moodle/course:update', context_course::instance($cm->course)) && $submission->user_id != $USER->id) {
                throw new moodle_exception('Unauthorized access to resource');
                exit;
            }
        } else { // new record, init
            $submission = new stdClass;
            $submission->publishdate = array('mon' => date('n'), 'year' => date('Y'));
            $submission->terms_accepted = $_SESSION['thesis_terms'];
            //unset($_SESSION['thesis_terms']);
        }

        $submission->submission_id = $submission_id;
        $submission->id = $id;

        file_prepare_standard_filemanager($submission, 'publish', $file_options, $context, 'mod_thesis', 'publish', $submission_id);
        file_prepare_standard_filemanager($submission, 'private', $file_options, $context, 'mod_thesis', 'private', $submission_id);
        file_prepare_standard_filemanager($submission, 'permanent', $file_options, $context, 'mod_thesis', 'permanent', $submission_id);
        $form->set_data($submission);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);
if (null != $f) {
    $message = get_string('save_state_message_1', 'mod_thesis') . '<br/>';
    if ('publish' == $f) {
        $message = get_string('save_state_message_publish', 'mod_thesis') . '<br/>';
    }
    if ('kar' == $f) {
        $message = get_string('save_state_message_kar', 'mod_thesis') . '<br/>';
    }
    echo '<div class="thesis_ok notifysuccess">'.$message.' <a href="view.php?id='.$id.'">'.get_string('return_submissions_list', 'mod_thesis').'</a></div>';
}

if ($show_as_published) {
    echo $output;
} else {
    $form->display();
}

echo '<a class="thesis_back" href="view.php?id='.$id.'">'.get_string('return_submissions_list', 'mod_thesis').'</a>';

echo $OUTPUT->footer();
