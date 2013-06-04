<?php
require_once($CFG->libdir.'/formslib.php');

function thesis_create_or_update($data,$thesis) {
  global $DB, $USER;

  if(null == $data->submission_id) {
    $data->thesis_id = $thesis->id;
    $data->user_id = $USER->id;
    $data->submission_id = $DB->insert_record('thesis_submissions',$data);
  } else {
    $data->id = $data->submission_id;
    $DB->update_record('thesis_submissions',$data);
  }
}

function thesis_list_submissions($cmid,$tid,$coursecontext) {
  global $DB,$USER;

  $submissions = array();
  if( has_capability('moodle/course:update', $coursecontext) ) {
    $submissions = $DB->get_records('thesis_submissions',array('thesis_id'=>$tid));
  } else {
    $submissions = $DB->get_records('thesis_submissions',array('thesis_id'=>$tid,'user_id'=>$USER->id));
  }

  $row = '<tr><td><a href="edit.php?id=%s&amp;submission_id=%s">%s</a></td><td>%s</td><td><a href="mailto:%5$s">%5$s</a></td><td>%6$s</td></tr>';
  $out = '';
  foreach( $submissions as $s ) {

    $pushed = 'draft';
    if($s->submitted_for_publishing == 1) $pushed = 'submitted';
    if(isset($s->publish)) $pushed = 'published';

    $user = $DB->get_record('user',array('id'=>$s->user_id));
    $name = join(' ', array($user->firstname, $user->lastname));
    $out .= sprintf( $row, $cmid, $s->id, $s->title, $name, $user->email, $pushed );
  }

  $message = '';
  if( empty($submissions) ) {
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

  protected function definition() {
    global $CFG;

    $mform =& $this->_form;

    $mform->addElement('hidden', 'id');
    $mform->setType('id', PARAM_INT);

    $mform->addElement('hidden', 'submission_id');
    $mform->setType('submission_id', PARAM_INT);

    $mform->addElement('text','title', get_string('title', 'thesis'));
    $mform->setType('title', PARAM_TEXT);
    $mform->addHelpButton('title', 'title', 'thesis');
    $mform->addRule('title', get_string('title_req', 'thesis'),'required');

    $mform->addElement('textarea','abstract', get_string('abstract', 'thesis'));
    $mform->setType('abstract', PARAM_TEXT);
    $mform->addRule('abstract', get_string('abstract_req', 'thesis'),'required');

    $typeoptions = array();
    $typeoptions['PHD'] = get_string('type_phd','thesis');
    $typeoptions['Masters'] = get_string('type_masters','thesis');
    $typeoptions['Other'] = get_string('type_other','thesis');
    $mform->addElement('select', 'thesis_type', get_string("thesis_type", "thesis"), $typeoptions);
    $mform->addRule('thesis_type', get_string('thesis_type_req', 'thesis'),'required');

    $mform->addElement('textarea','keywords', get_string('keywords', 'thesis'));
    $mform->setType('keywords', PARAM_TEXT);
    $mform->addHelpButton('keywords', 'keywords', 'thesis');

    $mform->addElement('textarea','corporate_acknowledgement', get_string('corp_acknowl', 'thesis'));
    $mform->setType('corporate_acknowledgement', PARAM_TEXT);

    $mform->addElement('text','identification_number', get_string('ident_no', 'thesis'));
    $mform->setType('identification_number', PARAM_TEXT);

    $mform->addElement('text','department', get_string('department', 'thesis'));
    $mform->setType('department', PARAM_TEXT);
    $mform->addRule('department', get_string('department_req', 'thesis'),'required');

    $mform->addElement('text','number_of_pages', get_string('no_pages', 'thesis'));
    $mform->setType('number_of_pages', PARAM_TEXT);

    $mform->addElement('date_selector','publishdate', get_string('publishdate', 'thesis'));
    $mform->addHelpButton('publishdate', 'publishdate', 'thesis');
    $mform->addRule('publishdate', get_string('publishdate_req', 'thesis'),'required');

    $mform->addElement('textarea','funding', get_string('funding', 'thesis'));
    $mform->setType('funding', PARAM_TEXT);

    $mform->addElement('text','contactemail', get_string('email', 'thesis'));
    $mform->setType('contactemail', PARAM_TEXT);
    $mform->addHelpButton('contactemail', 'email', 'thesis');

    $qualoptions = array();
    $qualoptions['Masters'] = get_string('quals_masters','thesis');
    $qualoptions['Doctoral'] = get_string('quals_doctoral','thesis');
    $qualoptions['Taught masters'] = get_string('quals_taughtmasters','thesis');
    $qualoptions['Unspecified'] = get_string('quals_unspecified','thesis');
    $mform->addElement('select', 'qualification_level', get_string("quals", "thesis"), $qualoptions);
    $mform->addRule('qualification_level', get_string('quals_req', 'thesis'),'required');

    $mform->addElement('text','qualification_name', get_string('qual_name','thesis'));
    $mform->setType('qualification_name', PARAM_TEXT);

    $mform->addElement('text', 'supervisor_fname', get_string('sup_fname', 'thesis'));
    $mform->setType('supervisor_fname', PARAM_TEXT);
    $mform->addElement('text', 'supervisor_sname', get_string('sup_sname', 'thesis'));
    $mform->setType('supervisor_sname', PARAM_TEXT);
    $mform->addElement('text', 'supervisor_email', get_string('sup_email', 'thesis'));
    $mform->setType('supervisor_email', PARAM_TEXT);

    // $mform->addElement('text','superviser', get_string('superviser','thesis'));
    // $mform->setType('superviser', PARAM_TEXT);

    //not needed
    //$mform->addElement('checkbox', 'metadata',  get_string('metadata_vis','thesis'), ' ');

    $mform->addElement('filemanager','publish_filemanager','Publish');
    $mform->closeHeaderBefore('restricted_info');
    $mform->addElement('static','restricted_info','Restricted Thesis/Dissertation','This version of the thesis/dissertation will not be made available publicly via the Kent Academic Repository. If you wish the thesis/dissertation to become available publicly on a certain date, please indicate this in the Restricted Thesis/Dissertation Information box below.');
    $mform->addElement('filemanager','private_filemanager','');

    $mform->addElement('static','additional_information_info', 'Restricted Thesis/Dissertation Information', 'Please include the date on which your restricted thesis/dissertation can become publicly available via the Kent Academic Repository');
    $mform->addElement('textarea','additional_information', '');
    $mform->setType('additional_information', PARAM_TEXT);

    $isadmin = isset($this->_customdata['isadmin']) && true === $this->_customdata['isadmin'];
    $submitted_for_publishing = isset($this->_customdata['submitted_for_publishing']) && true === $this->_customdata['submitted_for_publishing'];


    $buttonarray=array();

    if(!$submitted_for_publishing || $isadmin) {
      $buttonarray[] = $mform->createElement('submit', 'submitbutton', 'Save');
      $buttonarray[] = $mform->createElement('cancel');
    }

    //$mform->addGroup($buttonarray, 'buttona', '', array(' '), false);
    //$buttonarray=array();

    if(!$submitted_for_publishing) {
      $buttonarray[] = $mform->createElement('submit', 'submitpublish', 'Submit to School Administrator');
    }

    if($isadmin && $submitted_for_publishing) {
      $buttonarray[] = $mform->createElement('submit', 'submitdraft', 'Reset to draft');
    }

    $mform->addGroup($buttonarray, 'buttonb', '', array(' '), false);

    if($isadmin) {
      $mform->addElement('submit', 'publish_kar', 'Save changes and publish to Kar');
    }

    $mform->closeHeaderBefore('buttonb');
  }
}

