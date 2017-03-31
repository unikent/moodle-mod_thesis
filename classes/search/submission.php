<?php
// This file is part of Moodle - http://moodle.org/
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

/**
 * Search area for mod_thesis activities.
 *
 * @package    mod_thesis
 * @copyright  2016 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_thesis\search;

defined('MOODLE_INTERNAL') || die();

/**
 * Search area for mod_thesis submissions.
 *
 * @package    mod_thesis
 * @copyright  2016 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission extends \core_search\base_mod {
    /**
     * @var array Internal quick static cache.
     */
    protected $submissionsdata = array();

    /**
     * Returns recordset containing required data for indexing thesis submissions.
     *
     * @param int $modifiedfrom timestamp
     * @return moodle_recordset
     */
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        global $DB;

        $sql = 'SELECT ts.*, t.course
                FROM {thesis_submissions} ts
                INNER JOIN {thesis} t ON t.id = ts.thesis_id
                WHERE ts.timemodified >= ?';

        return $DB->get_recordset_sql($sql, array($modifiedfrom));
    }

    /**
     * Returns the document associated with this submissions id.
     *
     * @param stdClass $record Submission info.
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {
        try {
            $cm = $this->get_cm('thesis', $record->thesis_id, $record->course);
            $context = \context_module::instance($cm->id);
        } catch (\dml_missing_record_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document, not all required data is available: ' .
                $ex->getMessage(), DEBUG_DEVELOPER);

            return false;
        } catch (\dml_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document: ' . $ex->getMessage(), DEBUG_DEVELOPER);

            return false;
        }

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', $record->title);
        $doc->set('content', $record->abstract);
        $doc->set('contextid', $context->id);
        $doc->set('type', \core_search\manager::TYPE_TEXT);
        $doc->set('courseid', $record->course);
        $doc->set('userid', $record->user_id);
        $doc->set('owneruserid', $record->user_id);
        $doc->set('modified', $record->timemodified);
        $doc->set('description1', $record->keywords);

        return $doc;
    }

    /**
     * Whether the user can access the document or not.
     *
     * @throws \dml_missing_record_exception
     * @throws \dml_exception
     * @param int $id Forum post id
     * @return bool
     */
    public function check_access($id) {
        global $USER;

        try {
            $submission = $this->get_submission($id);
            $coursecontext = \context_course::instance($submission->course);
            $cminfo = $this->get_cm('thesis', $submission->thesis_id, $submission->course);
            $cm = $cminfo->get_course_module_record();
        } catch (\dml_missing_record_exception $ex) {
            return \core_search\manager::ACCESS_DELETED;
        } catch (\dml_exception $ex) {
            return \core_search\manager::ACCESS_DENIED;
        }

        // Recheck uservisible although it should have already been checked in core_search.
        if ($cminfo->uservisible === false) {
            return \core_search\manager::ACCESS_DENIED;
        }

        // If we are the submitter, grant access.
        if ($submission->user_id == $USER->id) {
            return \core_search\manager::ACCESS_GRANTED;
        }

        // If we can see the course, it's fine.
        if (has_capability('moodle/course:update', $coursecontext)) {
            return \core_search\manager::ACCESS_GRANTED;
        }

        return \core_search\manager::ACCESS_DENIED;
    }

    /**
     * Returns the specified thesis submission from its internal cache.
     *
     * @throws \dml_missing_record_exception
     * @param int $submissionid
     * @return stdClass
     */
    protected function get_submission($submissionid) {
        global $DB;

        if (empty($this->submissionsdata[$submissionid])) {
            $sql = 'SELECT ts.*, t.course
                    FROM {thesis_submissions} ts
                    INNER JOIN {thesis} t ON t.id = ts.thesis_id
                    WHERE ts.id = ?';
            $this->submissionsdata[$submissionid] = $DB->get_record_sql($sql, array($submissionid));
            if (!$this->submissionsdata[$submissionid]) {
                throw new \dml_missing_record_exception('thesis_submissions');
            }
        }

        return $this->submissionsdata[$submissionid];
    }

    /**
     * Link to the thesis submission.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        // The submission is already in static cache, we fetch it in self::search_access.
        $submission = $this->get_submission($doc->get('itemid'));
        $contextmodule = \context::instance_by_id($doc->get('contextid'));

        return new \moodle_url('/mod/thesis/edit.php', array('id' => $contextmodule->instanceid, 'submission_id' => $submission->id));
    }

    /**
     * Link to the thesis activity.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        $contextmodule = \context::instance_by_id($doc->get('contextid'));

        return new \moodle_url('/mod/thesis/view.php', array('id' => $contextmodule->instanceid));
    }
}
