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

require_once('../../config.php');
require_once('../../course/moodleform_mod.php');
require_once('lib.php');
require_once('locallib.php');
require_once($CFG->libdir . '/formslib.php');

$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url('/mod/thesis/view.php', array('id' => $id));

if (!$cm = get_coursemodule_from_id('thesis', $id)) {
    print_error('invalidcoursemodule');
}

if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}

if (!$thesis = $DB->get_record('thesis', array('id' => $cm->instance))) {
    print_error('invalidthesisid', 'thesis');
}

require_course_login($course, true, $cm);

$isadmin = has_capability('moodle/course:update', context_course::instance($cm->course));

$context = context_module::instance($cm->id);
$PAGE->set_context($context);

$heading = get_string('page_title_view', 'mod_thesis');
$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->set_activity_record($thesis);

$event = \mod_thesis\event\course_module_viewed::create(array(
    'objectid' => $thesis->id,
    'context' => $context
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('thesis', $thesis);
$event->trigger();

// Update 'viewed' state if required by completion system.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Reset terms session variable.
unset($_SESSION['thesis_terms']);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('view_page_title', 'mod_thesis'));

if (!empty($thesis->intro)) {
    echo $OUTPUT->box(format_module_intro('thesis', $thesis, $cm->id), 'generalbox', 'intro');
}

echo '<div class="thesis_list">';

if ($isadmin) {
    // Only admins can upload multiple thesis - students should upload multi-part thesis in the one deposit.
    echo '<a class="thesis_new" href="edit.php?id=' . $id . '">' . get_string('create_submission', 'mod_thesis') . '</a>';
}
echo thesis_list_submissions($id, $thesis->id, context_course::instance($cm->course));
echo '<br /><br /><br /><p>&nbsp;</p></div>';
echo $OUTPUT->footer();
