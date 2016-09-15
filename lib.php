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

function thesis_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 *
 *
 * @param unknown $data
 * @param unknown $mform
 * @return unknown
 */
function thesis_add_instance($data, $mform) {
    global $DB;

    $data->timemodified = time();

    return $DB->insert_record('thesis', $data);
}

/**
 * Thesis cleanup.
 */
function thesis_delete_instance($id) {
    global $DB;

    $DB->delete_records('thesis', array('id' => $id));
    $DB->delete_records('thesis_submissions', array('thesis_id' => $id));

    return true;
}

/**
 *
 *
 * @param unknown $data
 * @param unknown $mform
 * @return unknown
 */
function thesis_update_instance($data, $mform) {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();
    $DB->update_record('thesis', $data);

    return true;
}

/**
 *
 *
 * @param unknown $files
 * @param unknown $title
 * @return unknown
 */
function thesis_listfiles($files, $title) {
    $output = '';
    $output .= '<tr>';
    $output .= '<th rowspan= "' . count($files) . '">' . $title . '</th>';

    $first_key = key($files);
    foreach ($files as $key => $f) {
        $output .= $key === $first_key ? '' : '<tr>';
        $output .= '<td>' . $f->get_filename() . '</td>';
        $output .= '</tr>';
    }

    return $output;
}
