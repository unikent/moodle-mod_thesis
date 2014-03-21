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
function thesis_create_or_update($data, $thesis) {
    global $DB, $USER;

    $data->publish_month = $data->publishdate['mon'];
    $data->publish_year = $data->publishdate['year'];

    if (null == $data->submission_id) {
        $data->thesis_id = $thesis->id;
        $data->user_id = $USER->id;
        $data->submission_id = $DB->insert_record('thesis_submissions', $data);
    } else {
        $data->id = $data->submission_id;
        $DB->update_record('thesis_submissions', $data);
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

    $row = '<tr><td><a href="edit.php?id=%s&amp;submission_id=%s">%s</a></td><td>%s</td><td><a href="mailto:%5$s">%5$s</a></td><td>%6$s</td></tr>';
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
        $out .= sprintf($row, $cmid, $s->id, $s->title, $name, $user->email, $pushed);
    }

    $message = '';
    if (empty($submissions)) {
        return '<p>You currently have no submissions, <a href="edit.php?id='.$cmid.'">create one?</a></p>';
    } else {
        return <<<HTML
    <table class="thesis">
      <thead><tr><th>Title</th><th>Submitted by</th><th>Contact email</th><th>Status</th></tr></thead>
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

        $mform =& $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'submission_id');
        $mform->setType('submission_id', PARAM_INT);

        $mform->addElement('hidden', 'terms_accepted');
        $mform->setType('terms_accepted', PARAM_INT);

        $mform->addElement('static', 'title_info', '', get_string('title_help', 'thesis'));
        $mform->addElement('text', 'title', get_string('title', 'thesis'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('title_req', 'thesis'), 'required');

        $mform->addElement('textarea', 'abstract', get_string('abstract', 'thesis'));
        $mform->setType('abstract', PARAM_TEXT);
        $mform->addRule('abstract', get_string('abstract_req', 'thesis'), 'required');

        $typeoptions = array();
        $typeoptions['deng'] = 'Doctor of Engineering (D.Eng.)';
        $typeoptions['sportd'] = 'Doctor of Sport, Exercise and Health Science (Sport.D.)';
        $typeoptions['mphil'] = 'Master of Philosophy (M.Phil.)';
        $typeoptions['phd'] = 'Doctor of Philosophy (Ph.D.)';
        $typeoptions['dcs'] = 'Doctor of Clinical Science (D.Sc.)';
        $typeoptions['mscres'] = 'Master of Science by Research (MScRes)';
        $typeoptions['mares'] = 'Master of Arts by Research (MARes)';
        $typeoptions['llmres'] = 'Master of Law by Research (LLMRes)';
        $typeoptions['md'] = 'Doctor of Medicine (M.D.)';
        $typeoptions['msg'] = 'Master of Surgery (M.Sg.)';
        $typeoptions['pd'] = 'Professional Doctorate (P.D.)';
        $typeoptions['mres'] = 'Master of Research (M.Res.)';
        $typeoptions['ded'] = 'Doctor of Education (D.Ed.)';        
        
        $mform->addElement('select', 'thesis_type', 'Thesis/Dissertation type', $typeoptions);
        $mform->addRule('thesis_type', get_string('thesis_type_req', 'thesis'), 'required');

        $mform->addElement('textarea', 'keywords', 'Subject Keywords');
        $mform->setType('keywords', PARAM_TEXT);
        $mform->addHelpButton('keywords', 'keywords', 'thesis');
        $mform->addRule('keywords', 'Subject keywords are required', 'required');

        $mform->addElement('textarea', 'corporate_acknowledgement', get_string('corp_acknowl', 'thesis'));
        $mform->setType('corporate_acknowledgement', PARAM_TEXT);

        $category = \coursecat::make_categories_list('', 58, '!!!');

        // we need to just get the second level from the categories list
        $options = array();
        foreach($category as $id => $cat) {
            $split = explode('!!!', $cat);
            // don't use the anything other than 2 level deep, others have the wrong ids
            if(count($split) == 2 && strpos($split[1], "School") !== false) {
                $options[$id] = $split[1];
            }
        }

        $mform->addElement('select', 'department', get_string('department', 'thesis'), $options);
        $mform->setType('department', PARAM_TEXT);
        $mform->addRule('department', get_string('department_req', 'thesis'), 'required');

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
        $qualoptions['doctoral'] = get_string('quals_doctoral', 'thesis');
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

        $mform->closeHeaderBefore('publish_info');

        $choice = isset($_SESSION['thesis_terms']) ? $_SESSION['thesis_terms'] : 0;

        if ($choice != 3) {
            $mform->addElement('static', 'publish_info', '', get_string('form_publish_info', 'thesis'));
            $mform->addElement('filemanager', 'publish_filemanager', get_string('form_pa_td', 'thesis'), '', array('accepted_types' => 'application/pdf'));
        }

        $mform->closeHeaderBefore('restricted_info');

        if ($choice != 3) {
            $private_lang = get_string('thesis_restricted_info', 'mod_thesis');
            $mform->addElement('static', 'restricted_info', '', $private_lang);
        }

        $mform->addElement('filemanager', 'private_filemanager', get_string('form_res_td', 'thesis'));
        $mform->addElement('static', 'format_info', '', get_string('form_pdf_format', 'thesis'));

        if ($choice != 3) {
            $mform->addElement('static', 'additional_information_info', '', get_string('form_embargo_date', 'thesis'));
            $mform->addElement('textarea', 'additional_information', get_string('form_res_info', 'thesis'));
            $mform->setType('additional_information', PARAM_TEXT);
        }

        $isadmin = isset($this->_customdata['isadmin']) && true === $this->_customdata['isadmin'];
        $submitted_for_publishing = isset($this->_customdata['submitted_for_publishing']) && true === $this->_customdata['submitted_for_publishing'];

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
