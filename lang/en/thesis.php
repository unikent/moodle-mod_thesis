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
$string['email_help'] = 'This should be different to your Kent email address';
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
$string['license_wording'] = 'Creative Commons licenses tell users what they are allowed to do with your work (eg. share, copy adapt). See <a href="https://www.kent.ac.uk/library/research/open-access/copyright.html">https://www.kent.ac.uk/library/research/open-access/copyright.html</a>.';
$string['accompany_files'] = 'If you have accompanying files with your final thesis please email them individually to <a href="mailto:researchsupport@kent.ac.uk">researchsupport@kent.ac.uk</a> with your full name and thesis title.';
$string['staff_comments'] = 'Staff comments';
$string['restricted'] = 'Restricted';
$string['restricted_help'] = 'Restricted material';
$string['choose_btns_intro'] = '<p>Please select one of these options.</p><p>Before proceeding please ensure you have read the <a href="http://www.kent.ac.uk/library/research/docs/digital-deposition-of-theses-advice-to-candidates.pdf" target="_blank">Guidance Advice to Candidates</a>.</p>';
$string['delete_info'] = 'To delete this entry you must reset to draft.';

$string['thesis:addinstance'] = 'Add a new thesis';

$string['thesis_normal'] = 'I am depositing one copy of my thesis for public access with/without embargo';
$string['thesis_redacted'] = 'I am depositing two copies of my thesis, one redacted copy for public access, and one full text copy to be permanently restricted. For information about how to redact your thesis please see the guidance <a href="https://www.kent.ac.uk/library/research/thesis-deposit/index.html">(link)</a>.';
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
$string['view_page_title'] = 'Thesis/Dissertation Upload';
$string['create_submission'] = '(Make a new deposit on behalf of a student)';

$string['form_publish_info'] = "<p>You are responsible for respecting other's copyright. Learn more about conditions <a href='https://www.kent.ac.uk/library/research/thesis-deposit/index.html'>here</a>.</p><p>This version of your thesis/dissertation will be made available publicly via the Kent Academic Repository. Please upload your thesis/dissertation in PDF format.</p>";
$string['form_pa_td'] = 'Publicly-available Thesis/Dissertation';
$string['form_res_td'] = 'Restricted Thesis/Dissertation';
$string['form_res_perm_td'] = 'Permanently restricted Thesis/Dissertation';
$string['form_res_perm_help'] = 'This is the full version of your thesis, which will not be made publicly available because it contains sections you do not wish to be visible.';
$string['form_red_td'] = 'Redacted Thesis/Dissertation';
$string['form_red_td_warning'] = 'You are responsible for respecting others’ copyright. Learn more about conditions <a href="https://www.kent.ac.uk/library/research/thesis-deposit/index.html?tab=copyright" target="_blank">here</a>';
$string['form_embargo_info'] = 'If you place an embargo this restricts the full text from view, and only information about the thesis will be visible. For information regarding embargoes please see the <a href="https://www.kent.ac.uk/library/research/docs/digital-deposition-of-theses-advice-to-candidates.pdf" target="_blank">guidance here</a>';
$string['form_red_td_help'] = 'This version of your thesis will be made publicly available in the Kent Academic Repository, but you will have removed the sections you do not wish to be visible to the public. For information about how to redact your thesis please see the <a href="https://www.kent.ac.uk/library/research/thesis-deposit/">guidance here</a>.';
$string['form_pdf_format'] = '<p>Please upload your thesis/dissertation in PDF format.</p><p>If the thesis is in multiple .pdf files, then please upload all files here.</p>';
$string['form_embargo_date'] = 'Please include the date on which your restricted thesis/dissertation can become publicly available via the Kent Academic Repository.';
$string['form_res_info'] = 'Restricted Thesis/Dissertation Information';

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
