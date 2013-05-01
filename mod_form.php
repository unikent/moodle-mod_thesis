<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_thesis_mod_form extends moodleform_mod {

  function definition() {
    global $CFG, $DB, $PAGE;
    $mform =& $this->_form;

    $mform->addElement('header', 'general', 'Submission details');
    $mform->addElement('text', 'name', 'Link text', array('size'=>'64'));
    $mform->setType('name', PARAM_TEXT);
    $mform->addRule('name', null, 'required', null, 'client');
    $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

    $this->standard_coursemodule_elements();
    $this->add_action_buttons();
  }
}

