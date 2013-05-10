<?php

class restore_thesis_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

    $paths = array();
    $paths[] = new restore_path_element('thesis', '/activity/thesis');

    // Return the paths wrapped into standard activity structure
    return $this->prepare_activity_structure($paths);
    }

    protected function process_thesis($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course_id = $this->get_courseid();

        // insert the streamingvideo record
        $newitemid = $DB->insert_record('thesis', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }
}