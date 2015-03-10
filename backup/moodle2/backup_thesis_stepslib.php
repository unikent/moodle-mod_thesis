<?php

defined('MOODLE_INTERNAL') || die;

 /**
 * Define the complete url structure for backup, with file and id annotations
 */
class backup_thesis_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        //the URL module stores no user info

        // Define each element separated
        $thesis = new backup_nested_element('thesis', array('id'), array(
            'name',
            'notification_email',
            'intro',
            'introformat',
            'timemodified'
        ));

        // Build the tree
        //nothing here for URLs

        // Define sources
        $thesis->set_source_table('thesis', array('id' => backup::VAR_ACTIVITYID));

        // Return the root element (url), wrapped into standard activity structure
        return $this->prepare_activity_structure($thesis);

    }
}
