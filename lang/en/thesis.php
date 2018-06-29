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
$string['emailupdatedbody'] = 'Please be aware that a deposit has been made for module {$a->coursename} to the Moodle Theisis Deposit Page <a href = "{$a->depositurl}">here</a>.
Please login and check the details of the thesis then select "Save changes and publish to KAR".';
$string['emailupdatedsmall'] = 'A user has submitted a thesis in {$a->coursename}';
$string['emailpublishedsubject'] = 'Thesis {$a->name} submitted to KAR';

$string['emailpublishedbody'] = 'Thank you for uploading your completed thesis to Moodle. It will soon be published in the Kent Academic Repository

Please contact researchsupport@kent.ac.uk if:

Your thesis has not appeared in the repository within 5 working days.
You have any accompanying files to upload.
You wish to change an embargo.
Once your thesis is available in KAR it is possible to access download statistics, if it is Open Access. This information is located below your thesis details in the Repository, or via the KAR <a href="https://kar.kent.ac.uk/cgi/stats/report">Dashboard</a>.';

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
$string['notification_email_help'] = 'Please provide your school’s generic postgraduate administration email address eg. biopgadmin@kent.ac.uk. A notification will be sent to this address when a student has submitted a thesis ready for checking and approval.';
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
$string['email_help'] = 'Please do not use your Kent email address - use a personal address that will be active for the long term.';
$string['note'] = 'Additional information';
$string['note_help'] = 'Additional information';
$string['quals'] = 'Qualification level';
$string['quals_masters'] = 'Masters';
$string['quals_doctoral'] = 'Doctoral';
$string['quals_unspecified'] = 'Unspecified';
$string['quals_req'] = 'You must enter your qualification level';
$string['contactemail_req'] = 'You must enter an additional contact email that is different to your Kent address';
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
$string['license'] = 'Which license?';
$string['license_req'] = 'You must select a creative commons license';
$string['license_wording'] = 'Creative Commons licenses tell users what they are allowed to do with your work (eg. share, copy adapt). See <a href="https://www.kent.ac.uk/library/research/open-access/copyright.html" target="_blank">https://www.kent.ac.uk/library/research/open-access/copyright.html</a>.';
$string['accompany_files'] = 'If you have accompanying files with your final thesis they will need to be uploaded separately. Please follow the Accompanying files guidance <a href="https://www-test.kent.ac.uk/library/research/your-thesis/index.html" target="_blank">here</a>.';
$string['staff_comments'] = 'Staff comments';
$string['restricted'] = 'Restricted';
$string['restricted_help'] = 'Restricted material';
$string['choose_btns_intro'] = '<p>Please select one of these options.</p>';
$string['delete_info'] = 'To delete this entry you must reset to draft.';

$string['thesis:addinstance'] = 'Add a new thesis';

$string['thesis_normal'] = 'I am depositing one copy of my thesis for public access with/without embargo';
$string['thesis_redacted'] = 'I am depositing two copies of my thesis, one redacted copy for public access, and one full text copy to be permanently restricted. For information about how to redact your thesis please see the guidance <a href="https://www-test.kent.ac.uk/library/research/your-thesis/index.html" target="_blank">(link)</a>.';
$string['thesis_restricted'] = 'I am depositing one copy of my thesis to be permanently restricted';

$string['thesis_restricted_info'] = '<p>Files placed here will be made publicly available after 3 years. If you wish to make special arrangements, please indicate this in the information box below.</p>';

$string['form_heading'] = 'Add or amend details and upload document';
$string['page_title_view'] = 'Thesis deposit point';
$string['submitted_school'] = 'Submitted to School Administrator';
$string['published'] = 'Published';
$string['save_state_message_1'] = 'Thesis deposit successfully saved - you are welcome to make further changes and amendments, and at this stage your Thesis deposit has not been fully submitted.';
$string['save_state_message_publish'] = 'Thesis and details are now uploaded.  An administrator will now check and approve your deposit.  No further updates can now be made.';
$string['save_state_message_kar'] = 'Thesis deposit published to kar.  Further updates can not be made.';
$string['return_submissions_list'] = 'View my thesis deposit status';
$string['view_page_title'] = 'Introduction';
$string['create_submission'] = '(Make a new deposit on behalf of a student)';

$string['form_publish_info'] = "<p>You are responsible for respecting other's copyright. Learn more about conditions <a href='https://www.kent.ac.uk/library/research/your-thesis/third-party-copyright.html target=\"_blank\"'>here</a>.</p><p>This version of your Thesis deposit will be made available publicly via the Kent Academic Repository. Please upload your Thesis deposit in PDF format. If the thesis is in multiple .pdf files, then please upload all files below.</p>
<p>Please see the Preparing your document guidance <a href='https://www-test.kent.ac.uk/library/research/your-thesis/index.html' target='_blank'>here</a> for options for reducing the size of your thesis.</p>";
$string['form_pa_td'] = 'Publicly-available Thesis deposit';
$string['form_res_td'] = 'Restricted Thesis deposit';
$string['form_res_perm_td'] = 'Permanently restricted Thesis deposit';
$string['form_res_perm_help'] = 'This is the full version of your thesis, which will not be made publicly available because it contains sections you do not wish to be visible.';
$string['form_red_td'] = 'Redacted Thesis deposit';
$string['form_red_td_warning'] = 'You are responsible for respecting others’ copyright. Learn more about conditions <a href="https://www.kent.ac.uk/library/research/your-thesis/third-party-copyright.html" target="_blank">here</a>';
$string['form_embargo_info'] = 'If you place an embargo this restricts the full text from view, and only information about the thesis will be visible. For information regarding embargoes please see the <a href="https://www-test.kent.ac.uk/library/research/your-thesis/index.html" target="_blank">guidance here</a>';
$string['form_red_td_help'] = 'This version of your thesis will be made publicly available in the Kent Academic Repository, but you will have removed the sections you do not wish to be visible to the public. For information about how to redact your thesis please see the <a href="https://www-test.kent.ac.uk/library/research/your-thesis/index.html" target="_blank">guidance here.</a>Please upload your Thesis deposit in PDF format. If the thesis is in multiple .pdf files, then please upload all files below.';
$string['form_pdf_format'] = '<p>Please upload your Thesis deposit in PDF format.</p>';
$string['form_embargo_date'] = 'Please include the date on which your restricted Thesis deposit can become publicly available via the Kent Academic Repository.';
$string['form_res_info'] = 'Restricted Thesis deposit Information';

$string['form_buttons_save'] = 'Save for later';
$string['form_buttons_submit'] = 'Submit to School Administrator';
$string['form_buttons_reset'] = 'Reset to draft';
$string['form_buttons_publish'] = 'Save changes and publish to Kar';
$string['form_buttons_delete'] = 'Delete entry';

$string['form_add_sup'] = 'Add another supervisor';
$string['invalid_characters_error'] = 'Title cannot contain the following characters: ;';

$string['kar:username'] = 'Kar admin username for sword api submissions.';
$string['kar:password'] = 'Kar admin password for sword api submissions.';
$string['kar:server'] = 'Kar server for sword api submissions.';
