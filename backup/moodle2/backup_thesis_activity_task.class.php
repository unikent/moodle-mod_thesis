<?php

defined('MOODLE_INTERNAL') || die;

 // This activity has not particular settings but the inherited from the generic
 // backup_activity_task so here there isn't any class definition, like the ones
 // existing in /backup/moodle2/backup_settingslib.php (activities section)

require_once($CFG->dirroot . '/mod/thesis/backup/moodle2/backup_thesis_stepslib.php'); // Because it exists (must)

/**
 * URL backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_thesis_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        $this->add_step(new backup_thesis_activity_structure_step('thesis_structure', 'thesis.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot . '/mod/thesis', '#');

        //Access a list of all links in a course
        $pattern = '#(' . $base . '/index\.php\?id=)([0-9]+)#';
        $replacement = '$@THESISINDEX*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        //Access the link supplying a course module id
        $pattern = '#(' . $base . '/view\.php\?id=)([0-9]+)#';
        $replacement = '$@THESISBYID*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        return $content;
    }
}
