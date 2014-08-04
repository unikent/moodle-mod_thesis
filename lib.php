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


/**
 *
 *
 * @param unknown $feature
 * @return unknown
 */
function thesis_supports($feature) {
    $isadmin = has_capability('moodle/site:config', context_system::instance());

    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return false;
        default:
            return null;
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
    $output .= '<th rowspan= "'. count($files) .'">'.$title.'</th>' ;

    $first_key = key($files);
    foreach ($files as $key => $f) {
        $output .= $key === $first_key ? '' : '<tr>';
        $output .= '<td>' . $f->get_filename() .'</td>';
        $output .= '</tr>';
    }

    return $output;
}
