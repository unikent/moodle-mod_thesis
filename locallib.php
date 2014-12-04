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

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/coursecatlib.php';
require_once $CFG->libdir.'/formslib.php';


/**
 *
 *
 * @param unknown $data
 * @param unknown $thesis
 */
function thesis_create_or_update($data, $thesis, $context, $isadmin) {
    global $DB, $CFG, $USER;

    $data->publish_month = $data->publishdate['mon'];
    $data->publish_year = $data->publishdate['year'];

    // if submitted for publishing by student
    if (isset($data->submitted_for_publishing) && !$isadmin) {
        // notify admin of submission
        thesis_notification_updated($data, $thesis, $context, $isadmin);
    }

    // send notification to student if admin has set comments and not published
    if ($isadmin && !empty($data->comments) && isset($data->submitdraft)) {
        thesis_notification_notes($data, $thesis, $context, $isadmin);
    }

    if (null == $data->submission_id) {
        $data->thesis_id = $thesis->id;
        $data->user_id = $USER->id;
        $data->timecreated = time();
        $data->timemodified = time();
        $data->submission_id = $DB->insert_record('thesis_submissions', $data);
    } else {
        if (!$isadmin) {
            $data->timemodified = time();
        }

        $data->id = $data->submission_id;
        $DB->update_record('thesis_submissions', $data);
    }
}

/* Send notification email from admin to student with comments */
function thesis_notification_notes($data, $thesis, $context, $isadmin) {
    global $DB, $CFG, $USER;

    $data->timemodified = time();

    $eventdata = new object();
    $eventdata->component = 'mod_thesis';
    $eventdata->name = 'notes';

    // from user who made the change
    $eventdata->userfrom = $USER;

    // get user to notify
    $submission = $DB->get_record('thesis_submissions', array(
        'id' => $data->submission_id,
        'thesis_id' => $thesis->id
    ), 'user_id');

    $utn = $DB->get_record('user', array('id' => $submission->user_id));

    // setup data for email template
    $a = new stdClass();
    $a->name = $data->title;
    $a->depositurl = $CFG->wwwroot . '/mod/thesis/edit.php?id=' . $data->id . '&submission_id=' . $data->submission_id;
    $a->depositurllink = '<a href="' . $a->depositurl . '">' . $thesis->name . '</a>';
    $a->timemodified = date("Y-m-d H:i:s", $data->timemodified);
    $course = $DB->get_record('course', array('id' => $thesis->course), 'id, fullname');

    // set some user specific email template stuff
    $a->username = $utn->username;
    $a->coursename = $course->fullname;

    // set message data
    $eventdata->subject           = get_string('emailnotessubject', 'thesis', $a);
    $eventdata->fullmessage       = get_string('emailnotesbody', 'thesis', $a);
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';
    $eventdata->smallmessage      = get_string('emailnotessmall', 'thesis', $a);

    // set user to send to
    $eventdata->userto = $utn;
    
    // send message
    $result = message_send($eventdata);
}

/* Send notification email from student that submitted to admin */
function thesis_notification_updated($data, $thesis, $context, $isadmin) {
    global $DB, $CFG, $USER;

    $data->timemodified = time();

    $eventdata = new object();
    $eventdata->component = 'mod_thesis';
    $eventdata->name = 'updated';

    // from user who made the change
    $eventdata->userfrom = $USER;

    $recipients = array();

    // check if a custom notification email is set
    if (!empty($thesis->notification_email)) {
        $user = get_admin();
        $user->email = $thesis->notification_email;
        $recipients[] = $user;
    } else {
        // get the convenor for the module
        $connectroleconvenor = \local_connect\role::get_by('name', 'convenor');
        $course = $DB->get_record('course', array('id' => $thesis->course), 'id');
        $courseobjects = \local_connect\course::get_by('mid', $course->id, true);

        if (!empty($courseobjects)) {
            $courseobj = array_pop($courseobjects);

            $convenor = \local_connect\enrolment::get_for_course_and_role($courseobj, $connectroleconvenor);
            if ($convenor) {
                $recipient = $DB->get_record('user', array(
                    'id' => $convenor->user->mid
                ));

                if ($recipient) {
                    $recipients[] = $recipient;
                }
            }
        }
    }

    // TODO: check for duplicates in recipients.

    // setup data for email template
    $a = new stdClass();
    $a->name = $data->title;
    $a->depositurl = $CFG->wwwroot . '/mod/thesis/edit.php?id=' . $data->id . '&submission_id=' . $data->submission_id;
    $a->depositurllink = '<a href="' . $a->depositurl . '">' . $thesis->name . '</a>';
    $a->timemodified = date("Y-m-d H:i:s", $data->timemodified);
    $course = $DB->get_record('course', array('id' => $thesis->course), 'id, fullname');

    foreach ($recipients as $utn) {
        // set some user specific email template stuff
        $a->username = $utn->username;
        $a->coursename = $course->fullname;

        // set message data
        $eventdata->subject           = get_string('emailupdatedsubject', 'thesis', $a);
        $eventdata->fullmessage       = get_string('emailupdatedbody', 'thesis', $a);
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = get_string('emailupdatedsmall', 'thesis', $a);

        // set user to send to
        $eventdata->userto = $utn;
        
        // send message
        $result = message_send($eventdata);
    }
}

/**
 *
 *
 * @param unknown $cmid
 * @param unknown $tid
 * @param unknown $coursecontext
 * @return unknown
 */
function thesis_list_submissions($cmid, $tid, $coursecontext) {
    global $DB, $USER;

    $submissions = array();
    if (has_capability('moodle/course:update', $coursecontext)) {
        $submissions = $DB->get_records('thesis_submissions', array(
            'thesis_id' => $tid
        ));
    } else {
        $submissions = $DB->get_records('thesis_submissions', array(
            'thesis_id' => $tid,
            'user_id' => $USER->id
        ));
    }

    $row = '<tr><td><a href="edit.php?id=%s&amp;submission_id=%s">%s</a></td><td>%s</td><td><a href="mailto:%5$s">%5$s</a></td><td>%6$s</td><td>%7$s</td><td>%8$s</td></tr>';
    $out = '';
    foreach ($submissions as $s) {

        $pushed = 'draft';
        if ($s->submitted_for_publishing == 1) {
            $pushed = 'submitted';
        }
        if (isset($s->publish)) {
            $pushed = 'published';
        }

        $user = $DB->get_record('user', array(
            'id' => $s->user_id
        ));

        $name = join(' ', array($user->firstname, $user->lastname));
        $out .= sprintf($row, $cmid, $s->id, $s->title, $name, $user->email, date("Y-m-d H:i:s", $s->timecreated), date("Y-m-d H:i:s", $s->timemodified), $pushed);
    }

    $message = '';
    if (empty($submissions)) {
        return '<p>You currently have no submissions, <a href="edit.php?id='.$cmid.'">create one?</a></p>';
    } else {
        return <<<HTML
    <table class="thesis">
      <thead><tr><th>Title</th><th>Submitted by</th><th>Contact email</th><th>Time created</th><th>Time modified</th><th>Status</th></tr></thead>
      <tbody>$out</tbody>
    </table>
HTML;
    }
}


class mod_thesis_submit_form extends moodleform {

    /**
     *
     */
    protected function definition() {
        global $CFG;

        $isadmin = isset($this->_customdata['isadmin']) && true === $this->_customdata['isadmin'];

        $mform =& $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'submission_id');
        $mform->setType('submission_id', PARAM_INT);

        $mform->addElement('hidden', 'terms_accepted');
        $mform->setType('terms_accepted', PARAM_INT);

        // student comments area
        if (!$isadmin && !empty($this->_customdata['submission_comments'])) {
            $comments_html = <<<HTML
    <fieldset class="hidden staff_comments">
        <div>
            <div class="fitem">
                <div class="fitemtitle">
                    <div class="fstaticlabel">
                        <label>%s</label>
                    </div>
                </div>

                <div class="felement fstatic">
                    %s
                </div>
            </div>
        </div>
    </fieldset>
HTML;
            $mform->addElement('html', sprintf($comments_html, get_string('staff_comments', 'thesis'), $this->_customdata['submission_comments']));
            $mform->closeHeaderBefore('title_info');
        }

        $mform->addElement('static', 'title_info', '', get_string('title_help', 'thesis'));
        $mform->addElement('text', 'title', get_string('title', 'thesis'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('title_req', 'thesis'), 'required');

        $mform->addElement('textarea', 'abstract', get_string('abstract', 'thesis'));
        $mform->setType('abstract', PARAM_TEXT);
        $mform->addRule('abstract', get_string('abstract_req', 'thesis'), 'required');

        $mform->addElement('static', 'family_name_info', '', get_string('family_name_info', 'thesis'));
        $mform->addElement('text', 'given_name', get_string('given_name', 'thesis'));
        $mform->setType('given_name', PARAM_TEXT);
        $mform->addRule('given_name', get_string('given_name_req', 'thesis'), 'required');

        $mform->addElement('text', 'family_name', get_string('family_name', 'thesis'));
        $mform->setType('family_name', PARAM_TEXT);
        $mform->addRule('family_name', get_string('family_name_req', 'thesis'), 'required');

        $typeoptions = array(null => "Choose type");
        $typeoptions['engd'] = 'Doctor of Engineering (Eng.Doc)';
        $typeoptions['sportd'] = 'Professional Doctorate in Sport, Exercise and Health Science (Sport.D.)';
        $typeoptions['mphil'] = 'Master of Philosophy (M.Phil.)';
        $typeoptions['phd'] = 'Doctor of Philosophy (Ph.D.)';
        $typeoptions['dcs'] = 'Doctor of Clinical Science (D.Clin.Sci.)';
        $typeoptions['mscres'] = 'Master of Science by Research (M.Sc.)';
        $typeoptions['mares'] = 'Master of Arts by Research (M.A.)';
        $typeoptions['llmres'] = 'Master of Law by Research (LL.M.)';
        $typeoptions['md'] = 'Doctor of Medicine (M.D.)';
        $typeoptions['msg'] = 'Master of Surgery (M.Surg.)';
        $typeoptions['pd'] = 'Professional Doctorate (P.D.)';
        $typeoptions['mres'] = 'Master of Research (M.Res.)';
        $typeoptions['ded'] = 'Doctor of Education (D.Ed.)';
        $typeoptions['dd'] = 'Doctor of Divinity (D.D.)';
        $typeoptions['dlitt'] = 'Doctor of Letters (D.Litt.)';
        $typeoptions['lld'] = 'Doctor of Laws (LL.D.)';
        $typeoptions['dsc'] = 'Doctor of Science (D.Sc.)';
        $typeoptions['pdip'] = 'Postgraduate Diploma by Research (P.Dip.)';

        // Sort the list of schools.
        asort($typeoptions);

        $mform->addElement('select', 'thesis_type', 'Thesis/Dissertation type', $typeoptions);
        $mform->addRule('thesis_type', get_string('thesis_type_req', 'thesis'), 'required');

        $mform->addElement('textarea', 'keywords', 'Subject Keywords');
        $mform->setType('keywords', PARAM_TEXT);
        $mform->addHelpButton('keywords', 'keywords', 'thesis');
        $mform->addRule('keywords', 'Subject keywords are required', 'required');

        $mform->addElement('textarea', 'corporate_acknowledgement', get_string('corp_acknowl', 'thesis'));
        $mform->setType('corporate_acknowledgement', PARAM_TEXT);

        $category = \coursecat::make_categories_list('', 58, '!!!');
        $extracategories = array(
            'Centre for Professional Practice'
        );

        // We need to just get the second level from the categories list.
        $options = array();
        foreach ($category as $id => $cat) {
            $split = explode('!!!', $cat);
            // Don't use the anything other than 2 level deep, others have the wrong ids.
            if (count($split) == 2 && (strpos($split[1], "School") !== false || in_array($split[1], $extracategories))) {
                $options[$id] = $split[1];
            }
        }

        // Sort the list of schools.
        asort($options);
        $options = array(null => "Choose school") + $options;

        $mform->addElement('select', 'department', get_string('department', 'thesis'), $options);
        $mform->setType('department', PARAM_TEXT);
        $mform->addRule('department', get_string('department_req', 'thesis'), 'required');

        $mform->addElement('text', 'institution', get_string('institution', 'thesis'), $options);
        $mform->setType('institution', PARAM_TEXT);

        $mform->addElement('text', 'number_of_pages', get_string('no_pages', 'thesis'));
        $mform->setType('number_of_pages', PARAM_TEXT);

        $date_group = array();
        $months = array();
        for ($i = 0; $i < 12; $i++) {
            $months[$i+1] = date('F', mktime(0, 0, 0, $i + 1));
        }
        $date_group []= $mform->createElement('select', 'mon', '', $months);

        $range = range(1980, 2020);
        $years = array_combine($range, $range);
        $date_group []= $mform->createElement('select', 'year', '', $years);

        $mform->addElement('static', 'publishdate_info', '', get_string('publishdate_help', 'thesis'));
        $mform->addGroup($date_group, 'publishdate', get_string('publishdate', 'thesis'));
        $mform->addRule('publishdate', get_string('publishdate_req', 'thesis'), 'required');

        $mform->addElement('textarea', 'funding', get_string('funding', 'thesis'));
        $mform->setType('funding', PARAM_TEXT);

        $mform->addElement('static', 'title_info', '', get_string('email_help', 'thesis'));
        $mform->addElement('text', 'contactemail', get_string('email', 'thesis'));
        $mform->setType('contactemail', PARAM_TEXT);

        $qualoptions = array();
        $qualoptions['doctorial'] = get_string('quals_doctoral', 'thesis');
        $qualoptions['masters'] = get_string('quals_masters', 'thesis');
        $qualoptions['unspecified'] = get_string('quals_unspecified', 'thesis');
        $mform->addElement('select', 'qualification_level', get_string("quals", "thesis"), $qualoptions);
        $mform->addRule('qualification_level', get_string('quals_req', 'thesis'), 'required');

        foreach (array('', 'second_', 'third_') as $i) {
            $mform->addElement('text', $i . 'supervisor_fname', get_string($i . 'sup_fname', 'thesis'));
            $mform->setType($i . 'supervisor_fname', PARAM_TEXT);
            $mform->addElement('text', $i . 'supervisor_sname', get_string($i . 'sup_sname', 'thesis'));
            $mform->setType($i . 'supervisor_sname', PARAM_TEXT);
            $mform->addElement('text', $i . 'supervisor_email', get_string($i . 'sup_email', 'thesis'));
            $mform->setType($i . 'supervisor_email', PARAM_TEXT);
        }

        $mform->addElement('button', 'more_supervisors', get_string('form_add_sup', 'thesis'), array('onclick' => 'thesis_more_supervisors();'));

        $choice = isset($_SESSION['thesis_terms']) ? $_SESSION['thesis_terms'] : 0;

        $embargo_options = array();
        $embargo_options[0] = 'Immediate open access - no embargo';
        $embargo_options[1] = 'One year embargo';
        $embargo_options[3] = 'Three year embargo';

        if ($choice == 1) {
            // public
            $mform->closeHeaderBefore('publish_info');

            $mform->addElement('static', 'publish_info', '', get_string('form_publish_info', 'thesis'));
            $mform->addElement('filemanager', 'publish_filemanager', get_string('form_pa_td', 'thesis'), '', array('accepted_types' => 'application/pdf'));

            $mform->addElement('select', 'embargo', get_string('embargo', 'thesis'), $embargo_options);
            $mform->setDefault('embargo', 3);
        }

        if ($choice == 2) {
            // restricted
            $mform->closeHeaderBefore('private_filemanager');

            $mform->addElement('filemanager', 'private_filemanager', get_string('form_red_td', 'thesis'), '', array('accepted_types' => 'application/pdf'));
            $mform->addElement('static', 'format_info', '', get_string('form_pdf_format', 'thesis'));

            $mform->addElement('select', 'embargo', get_string('embargo', 'thesis'), $embargo_options);
            $mform->setDefault('embargo', 3);

            $mform->closeHeaderBefore('permanent_filemanager');

            // perm restricted form
            $mform->addElement('filemanager', 'permanent_filemanager', get_string('form_res_perm_td', 'thesis'), '', array('accepted_types' => 'application/pdf'));
            $mform->addElement('static', 'format_info', '', get_string('form_pdf_format', 'thesis'));

            //$mform->addElement('static', 'additional_information_info', '', get_string('form_embargo_date', 'thesis'));
            //$mform->addElement('textarea', 'additional_information', get_string('form_res_info', 'thesis'));
            //$mform->setType('additional_information', PARAM_TEXT);
        }

        if ($choice == 3) {
            // permantently restricted
            $mform->closeHeaderBefore('private_filemanager');

            $mform->addElement('filemanager', 'permanent_filemanager', get_string('form_res_perm_td', 'thesis'), '', array('accepted_types' => 'application/pdf'));
            $mform->addElement('static', 'format_info', '', get_string('form_pdf_format', 'thesis'));
        }

        $submitted_for_publishing = isset($this->_customdata['submitted_for_publishing']) && true === $this->_customdata['submitted_for_publishing'];

        // admin comments area
        if ($isadmin) {
            $mform->closeHeaderBefore('comments');

            $mform->addElement('textarea', 'comments', get_string('staff_comments', 'thesis'));
            $mform->setType('comments', PARAM_TEXT);
        }

        $buttonarray = array();

        if (!$submitted_for_publishing || $isadmin) {
            $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('form_buttons_save', 'thesis'));
            $buttonarray[] = $mform->createElement('cancel');
        }

        if (!$submitted_for_publishing) {
            $buttonarray[] = $mform->createElement('submit', 'submitpublish', get_string('form_buttons_submit', 'thesis'));
        }

        if ($isadmin && $submitted_for_publishing) {
            $buttonarray[] = $mform->createElement('submit', 'submitdraft', get_string('form_buttons_reset', 'thesis'));
        }

        $mform->addGroup($buttonarray, 'buttonb', '', array(' '), false);

        if ($isadmin) {
            $mform->addElement('submit', 'publish_kar', get_string('form_buttons_publish', 'thesis'));
        }

        $mform->closeHeaderBefore('buttonb');
    }


    /**
     *
     *
     * @return unknown
     */
    function terms_accepted() {
        $ta = $this->_form->getSubmitValue('terms_accepted');
        return isset($ta) && ($ta > 0);
    }


}
