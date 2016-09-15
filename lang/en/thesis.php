<?php

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Thesis';
$string['modulenameplural'] = 'Thesis';
$string['modulenamehelp'] = 'The Thesis module allows teachers to setup an area for students to drop their thesis for submission into KAR.';
$string['pluginadministration'] = '';
$string['pluginname'] = 'Thesis';

$string['search:activity'] = 'Thesis activities';
$string['search:submission'] = 'Thesis submissions';

// message provider strings
$string['messageprovider:notes'] = 'Notification of updated thesis deposit notes';
$string['messageprovider:updated'] = 'Notification of updated thesis deposit';

// email strings
$string['thesis:emailnotes'] = 'Receive eThesis notes by email';
$string['thesis:emailupdated'] = 'Receive eThesis update notifications by email';

// updated by student
$string['emailupdatedsubject'] = 'Thesis submitted to {$a->coursename} - {$a->name}';
$string['emailupdatedbody'] = 'Dear {$a->username},

A user has submitted their thesis:
\'{$a->name}\'
in {$a->coursename} - {$a->name}
at {$a->timemodified}.

You can access this deposit at \'{$a->depositurl}\'.';
$string['emailupdatedsmall'] = 'A user has submitted a thesis in {$a->coursename}';

// staff updated notes
$string['emailnotessubject'] = 'Thesis notes updated: {$a->coursename} - {$a->name}';
$string['emailnotesbody'] = 'Dear {$a->username},

Your deposit has been reset to draft and notes added:
\'{$a->name}\'
in {$a->coursename} - {$a->name}
at {$a->timemodified}.

You can access this deposit at \'{$a->depositurl}\'.';
$string['emailnotessmall'] = 'Notes have been added to your thesis in {$a->coursename}';

//Fomr strings
$string['notification_email'] = 'Submission Notification Email';
$string['notification_email_help'] = 'By default notifications are sent to the convenor of the module when a submission is ready for approval, simply leave the email field blank. Optionally you can provide an email address to be notified instead by completing the field.';
$string['title'] = 'Title';
$string['title_help'] = 'This title needs to be exactly the same as the title on your thesis/dissertation';
$string['title_req'] = 'You must enter a title';
$string['abstract_req'] = 'You must enter an abstract';
$string['abstract'] = 'Abstract';
$string['family_name_info'] = 'Name as it appears on your thesis/dissertation';
$string['family_name'] = 'Last Name';
$string['family_name_req'] = 'You must enter your family name';
$string['given_name'] = 'First Name(s)';
$string['given_name_req'] = 'You must enter your given name';
$string['thesis_type'] = 'Thesis/Dissertation type';
$string['thesis_type_req'] = 'You must enter a thesis/dissertation type';
$string['keywords'] = 'Subject Keywords';
$string['keywords_help'] = 'Select subject keywords or phrases that best describe the content of your thesis/dissertation. Separate keywords with spaces.';
$string['corp_acknowl'] = 'Corporate Sponsor(s)';
$string['ident_no'] = 'Identification Number';
$string['department'] = 'School';
$string['department_req'] = 'You must enter a school';
$string['institution'] = 'Additional Awarding Institution';
$string['no_pages'] = 'Number of pages';
$string['publishdate'] = 'Thesis/Dissertation date';
$string['publishdate_help'] = 'Date appearing on the title page of your thesis/dissertation';
$string['publishdate_req'] = 'You must enter a thesis/dissertation date';
$string['funding'] = 'Funding Body';
$string['email'] = 'Contact email';
$string['email_help'] = 'This should be an alternate to your Kent email address';
$string['note'] = 'Additional information';
$string['note_help'] = 'Additional information';
$string['quals'] = 'Qualification level';
$string['quals_masters'] = 'Masters';
$string['quals_doctoral'] = 'Doctoral';
$string['quals_unspecified'] = 'Unspecified';
$string['quals_req'] = 'You must enter your qualification level';
$string['qual_name'] = 'Qualification name';
$string['sup_fname'] = 'Supervisor first name';
$string['sup_sname'] = 'Supervisor last name';
$string['sup_email'] = 'Supervisor email';
$string['second_sup_fname'] = 'Second supervisor first name';
$string['second_sup_sname'] = 'Second supervisor last name';
$string['second_sup_email'] = 'Second supervisor email';
$string['third_sup_fname'] = 'Third supervisor first name';
$string['third_sup_sname'] = 'Third supervisor last name';
$string['third_sup_email'] = 'Third supervisor email';
$string['metadata_vis'] = 'Metadata visible?';
$string['embargo'] = 'With embargo?';
$string['staff_comments'] = 'Staff comments';
$string['restricted'] = 'Restricted';
$string['restricted_help'] = 'Restricted material';
$string['choose_btns_intro'] = '<p>Please select the type of thesis you are wishing to deposit for display of correct terms and conditions.</p><p>Before proceeding please ensure you have read the <a href="http://www.kent.ac.uk/library/research/docs/digital-deposition-of-theses-advice-to-candidates.pdf" target="_blank">Guidance Advice to Candidates</a>.</p>';
$string['delete_info'] = 'To delete this entry you must reset to draft.';

$string['thesis:addinstance'] = 'Add a new thesis';

$string['thesis_normal'] = 'I am depositing one copy of my thesis for public access with/without embargo';
$string['thesis_redacted'] = 'I am depositing two copies of my thesis, one redacted copy for public access, with/without embargo, and one full text copy to be permanently restricted';
$string['thesis_restricted'] = 'I am depositing one copy of my thesis to be permanently restricted';

$string['thesis_restricted_info'] = '<p>Files placed here will be made publicly available after 3 years. If you wish to make special arrangements, please indicate this in the information box below.</p>';

$string['form_heading'] = 'Create/update thesis/dissertation';
$string['page_title_view'] = 'View thesis/dissertation deposit';
$string['submitted_school'] = 'Submitted to School Administrator';
$string['published'] = 'Published';
$string['save_state_message_1'] = 'Thesis/dissertation deposit successfully saved - you are welcome to make further changes and amendments, and at this stage your Thesis/dissertation has not been fully submitted.';
$string['save_state_message_publish'] = 'Thesis/dissertation deposit published.  An administrator will now check and approve your deposit.  No further updates can now be made.';
$string['save_state_message_kar'] = 'Thesis/dissertation deposit published to kar.  Further updates can not be made.';
$string['return_submissions_list'] = 'Return to deposits list';
$string['view_page_title'] = 'Thesis/Dissertation Deposits';
$string['create_submission'] = '(Make a new deposit)';

$string['form_publish_info'] = 'This version of your thesis/dissertation will be made available publicly via the Kent Academic Repository. Please upload your thesis/dissertation in PDF format.';
$string['form_pa_td'] = 'Publicly-available Thesis/Dissertation';
$string['form_res_td'] = 'Restricted Thesis/Dissertation';
$string['form_res_perm_td'] = 'Permanently restricted Thesis/Dissertation';
$string['form_red_td'] = 'Redacted Thesis/Dissertation';
$string['form_pdf_format'] = '<p>Please upload your thesis/dissertation in PDF format.</p>';
$string['form_embargo_date'] = 'Please include the date on which your restricted thesis/dissertation can become publicly available via the Kent Academic Repository.';
$string['form_res_info'] = 'Restricted Thesis/Dissertation Information';

$string['form_buttons_save'] = 'Save for later';
$string['form_buttons_submit'] = 'Submit to School Administrator';
$string['form_buttons_reset'] = 'Reset to draft';
$string['form_buttons_publish'] = 'Save changes and publish to Kar';
$string['form_buttons_delete'] = 'Delete entry';

$string['form_add_sup'] = 'Add another supervisor';
$string['invalid_characters_error'] = 'Title cannot contain the following characters: ;';
