<?php 

require_once('../../config.php');

$id = optional_param('id', 0, PARAM_INT);
$submission_id = optional_param('submission_id', null, PARAM_INT);
$choice = optional_param('kent_thesis_choose_btn', null, PARAM_TEXT);
$accepted = optional_param('kent_thesis_tcs_accepted', 0, PARAM_INT);

$PAGE->set_url('/mod/thesis/terms.php', array('id'=>$id));

if (! $cm = get_coursemodule_from_id('thesis', $id)) {
  print_error('invalidcoursemodule');
}
if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
  print_error('coursemisconf');
}
if (! $thesis = $DB->get_record("thesis", array("id" => $cm->instance))) {
  print_error('invalidthesisid', 'thesis');
}

require_course_login($course, true, $cm);


$context = context_module::instance($cm->id);
$PAGE->set_context($context);

$content = '<h2>Terms and Conditions</h2>';

$suburl = isset($submission_id) ? "&submission_id={$submission_id}" : "";


$normal_tcs = <<<NTCS
	<div class="kent_thesis_tcs" id="kent_normal_thesis_tcs">
		<h3>The University of Kent eThesis Deposit Agreement for doctoral theses</h3>
		<h4>Covered Work</h4>
		<p>I would like to deposit my thesis and abstract in the University of Kent 
		digital repository, currently Kent Academic Repository. Research referred 
		to below as “Work” is covered by this agreement and when I deposit my Work, 
		whether personally or through an assistant or other agent, I agree to the 
		following:</p>
		<h4>Non-exclusive Rights</h4>
		<p>Rights granted to the University of Kent through this agreement are 
		entirely non-exclusive. I am free to publish the Work in its present 
		version or future versions elsewhere. I agree that the University of Kent 
		or any third party with whom the University of Kent has an agreement to do 
		so may, without changing content, translate the Work to any medium or format 
		for the purpose of future preservation and accessibility.</p>
		<h4>Deposit In The University Of Kent Digital Repository (currently Kent Academic Repository)</h4>
		<p>I understand that work deposited in the University of Kent digital repository 
		will be accessible to a wide variety of people and institutions – including 
		automated agents – via the World Wide Web. An electronic copy of my thesis may 
		also be included in the British Library Electronic Theses On-line Service (EThOS).</p>
		<p>I understand that once the Work is deposited, metadata will be incorporated into 
		public access catalogues. This citation to the Work will always remain visible. 
		Removal of the Work can be made after discussion with University and School 
		administrators.</p>
		<h4>I Agree As Follows</h4>
		<ol>
			<li>That I am the author of the Work and have the authority to make this agreement 
			and to hereby give the University of Kent the right to make available the Work in 
			the way described above.</li>
			<li>That the Work is the final version on which the examiners based their recommendation 
			for the award of the degree</li>
			<li>That I have obtained permission from copyright holders (authors/publishers) in 
			respect of any substantial extracts of third party copyright material that have already 
			been published, which is included within the thesis. Permission obtained specifically 
			includes the right to publish digitally. (Please contact Information Services if you need 
			further help and advice)</li>
			<li>That I have exercised reasonable care to ensure that the Work is original and does 
			not, to the best of my knowledge, break any UK law or infringe any third party’s 
			copyright or other Intellectual Property Right. (Please contact Information Services 
			if you need further help and advice)</li>
			<li>The University of Kent does not hold any obligation to take legal action on behalf of 
			the Depositor, or other rights holders, in the event of breach of intellectual property 
			rights, or any other right, in the material deposited</li>
		</ol>
	</div>
NTCS;

$redacted_tcs = <<<NTCS
	<div class="kent_thesis_tcs" id="kent_normal_thesis_tcs">
		<h3>The University of Kent eThesis Deposit Agreement for doctoral theses</h3>
		<h4>Covered Work</h4>
		<p>I would like to deposit my thesis and abstract in the University of Kent 
		digital repository, currently Kent Academic Repository. Research referred 
		to below as “Work” is covered by this agreement and when I deposit my Work, 
		whether personally or through an assistant or other agent, I agree to the 
		following:</p>
		<h4>Non-exclusive Rights</h4>
		<p>Rights granted to the University of Kent through this agreement are 
		entirely non-exclusive. I am free to publish the Work in its present 
		version or future versions elsewhere. I agree that the University of Kent 
		or any third party with whom the University of Kent has an agreement to do 
		so may, without changing content, translate the Work to any medium or format 
		for the purpose of future preservation and accessibility.</p>
		<h4>Deposit In The University Of Kent Digital Repository (currently Kent Academic Repository)</h4>
		<p>I understand that work deposited in the University of Kent digital repository 
		will be accessible to a wide variety of people and institutions – including 
		automated agents – via the World Wide Web. An electronic copy of my thesis may 
		also be included in the British Library Electronic Theses On-line Service (EThOS).</p>
		<p>I understand that once the Work is deposited, metadata will be incorporated into 
		public access catalogues. This citation to the Work will always remain visible. 
		Removal of the Work can be made after discussion with University and School 
		administrators.</p>
		<h4>I Agree As Follows</h4>
		<ol>
			<li>That I am the author of the Work and have the authority to make this agreement 
			and to hereby give the University of Kent the right to make available the Work in 
			the way described above.</li>
			<li>I have provided a copy of the Work, with third party copyright material removed, 
			for sharing and also the original for preservation </li>
			<li>That I have exercised reasonable care to ensure that the Work is original and does 
			not, to the best of my knowledge, break any UK law or infringe any third party’s 
			copyright or other Intellectual Property Right. (Please contact Information Services 
			if you need further help and advice)</li>
			<li>The University of Kent does not hold any obligation to take legal action on behalf of 
			the Depositor, or other rights holders, in the event of breach of intellectual property 
			rights, or any other right, in the material deposited</li>
		</ol>
	</div>
NTCS;

$restricted_tcs = <<<NTCS
	<div class="kent_thesis_tcs" id="kent_normal_thesis_tcs">
		<h3>The University of Kent eThesis Deposit Agreement for doctoral theses</h3>
		<h4>Covered Work</h4>
		<p>I would like to deposit my thesis and abstract in the University of Kent 
		digital repository, currently Kent Academic Repository. Research referred 
		to below as “Work” is covered by this agreement and when I deposit my Work, 
		whether personally or through an assistant or other agent, I agree to the 
		following:</p>
		<h4>Non-exclusive Rights</h4>
		<p>Rights granted to the University of Kent through this agreement are 
		entirely non-exclusive. I am free to publish the Work in its present 
		version or future versions elsewhere. I agree that the University of Kent 
		or any third party with whom the University of Kent has an agreement to do 
		so may, without changing content, translate the Work to any medium or format 
		for the purpose of future preservation and accessibility.</p>
		<h4>Deposit In The University Of Kent Digital Repository (currently Kent Academic Repository)</h4>
		<p>I understand that work deposited in the University of Kent digital repository 
		will be accessible to a wide variety of people and institutions – including 
		automated agents – via the World Wide Web. An electronic copy of my thesis may 
		also be included in the British Library Electronic Theses On-line Service (EThOS).</p>
		<p>I understand that once the Work is deposited, metadata will be incorporated into 
		public access catalogues. This citation to the Work will always remain visible. 
		Removal of the Work can be made after discussion with University and School 
		administrators.</p>
		<h4>I Do Not Agree Because:</h4>
		<p>My School has approved permanent restricted access.</p>
	</div>
NTCS;

$choose_btns = <<<CBTNS
	<form class="kent_thesis_choose" action="terms.php?id={$id}{$suburl}" method="post">
		<div class="kent_thesis_radio_grp">
			<input type="radio" name="kent_thesis_choose_btn" id="kent_thesis_normal" value="normal">
			<label for="kent_thesis_normal">Normal thesis</label>
		</div>
		<div class="kent_thesis_radio_grp">
			<input type="radio" name="kent_thesis_choose_btn" id="kent_thesis_redacted" value="redacted">
			<label for="kent_thesis_redacted">Redacted thesis</label>
		</div>
		<div class="kent_thesis_radio_grp">
			<input type="radio" name="kent_thesis_choose_btn" id="kent_thesis_restricted" value="restricted">
			<label for="kent_thesis_restricted">Permantly restricted thesis</label>
		</div>
		<input class="form-submit" type="submit" value="Choose">
	</form>

CBTNS;

$choose_btns_intro = get_string("choose_btns_intro", "thesis");

if($accepted > 0) {
	$_SESSION['thesis_terms'] = $accepted;
	redirect($CFG->wwwroot . "/mod/thesis/edit.php?id={$id}{$suburl}");
} elseif(empty($choice)) {
	$content .= $choose_btns_intro;
	$content .= $choose_btns;
} else {
	$type = 0;
	switch ($choice) {
		case 'normal':
			$type = 1;
			$content .= $normal_tcs;
			break;
		case 'redacted':
			$type = 2;
			$content .= $redacted_tcs;
			break;
		case 'restricted':
			$type = 3;
			$content .= $restricted_tcs;
			break;
		default:
			$content .= 'Incorrect choice';
			break;
	}

	if($type > 0) {
		$content .= "<a href='/course/view.php?id=$course->id' class='btn form-button kent_thesis_tcs_back'>Back</a>
					<form class='kent_thesis_tcs_sub' action='terms.php?id={$id}{$suburl}' method='post'>
						<input type='hidden' value='$type' name='kent_thesis_tcs_accepted' />
						<input class='form-submit' type='submit' value='Accept'>
					</form>";
	}
}

echo $OUTPUT->header();
echo $content;

echo $OUTPUT->footer();



