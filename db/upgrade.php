<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_thesis_upgrade($oldversion = 0) {

    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    if($oldversion < 2013052904) {
        $table = new xmldb_table('thesis');
        $field = new xmldb_field('course_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'name');

        $dbman->rename_field($table, $field, 'course');

        $index = new xmldb_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2013052904, 'thesis');
    }

    if($oldversion < 2013060104) {
        $table = new xmldb_table('thesis_submissions');
        $field = new xmldb_field('submitted_for_publishing', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2013060104, 'thesis');
    }

    if($oldversion < 2013060401) {
        $table = new xmldb_table('thesis_submissions');
        $field = new xmldb_field('number_of_pages', XMLDB_TYPE_TEXT, 'small');

        $dbman->change_field_type($table, $field);

        upgrade_mod_savepoint(true, 2013060401, 'thesis');
    }

    if($oldversion < 2013060402) {
        $table = new xmldb_table('thesis_submissions');
        $field = new xmldb_field('terms_accepted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2013060402, 'thesis');
    }

    if($oldversion < 2013061001) {
        $table = new xmldb_table('thesis_submissions');
        $month = new xmldb_field('publish_month', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $year = new xmldb_field('publish_year', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $publishdate = new xmldb_field('publishdate');

        if (!$dbman->field_exists($table, $month)) {
            $dbman->add_field($table, $month);
        }
        if (!$dbman->field_exists($table, $year)) {
            $dbman->add_field($table, $year);
        }
        if ($dbman->field_exists($table, $publishdate)) {
            $dbman->drop_field($table, $publishdate);
        }

        upgrade_mod_savepoint(true, 2013061001, 'thesis');
    }

    if($oldversion < 2013061003) {
        $table = new xmldb_table('thesis_submissions');

        $fields = array(
            'second_supervisor_fname', 'second_supervisor_sname', 'second_supervisor_email',
            'third_supervisor_fname', 'third_supervisor_sname', 'third_supervisor_email'
        );

        foreach ($fields as $i) {
            $field = new xmldb_field($i, XMLDB_TYPE_TEXT, 'small');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2013061003, 'thesis');
    }

    if ($oldversion < 2014032101) {
        // Define field family_name to be added to thesis_submissions.
        $table = new xmldb_table('thesis_submissions');
        $field = new xmldb_field('family_name', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'user_id');

        // Conditionally launch add field family_name.
        if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('given_name', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'family_name');

        // Conditionally launch add field given_name.
        if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
        }

        // Newmodule savepoint reached.
        upgrade_mod_savepoint(true, 2014032101, 'thesis');
    }

    if ($oldversion < 2014032500) {
        $table = new xmldb_table('thesis_submissions');
        $field = new xmldb_field('embargo', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'metadata');

        if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2014032500, 'thesis');
    }

    if ($oldversion < 2014032600) {
        $table = new xmldb_table('thesis_submissions');
        $field = new xmldb_field('comments', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'embargo');

        if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2014032600, 'thesis');
    }

    if ($oldversion < 2014042300) {
        $table = new xmldb_table('thesis_submissions');

        $created = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'terms_accepted');
        $modified = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');

        if (!$dbman->field_exists($table, $created)) {
            $dbman->add_field($table, $created);
        }
        if (!$dbman->field_exists($table, $modified)) {
            $dbman->add_field($table, $modified);
        }

        upgrade_mod_savepoint(true, 2014042300, 'thesis');
    }

    if ($oldversion < 2014060900) {
        $moduleid = $DB->get_field('modules', 'id', array(
            'name' => 'thesis'
        ), MUST_EXIST);

        // Delete all Thesis entries that do not have a corresponding course module.
        $DB->execute('
            DELETE t.* FROM {thesis} t
            LEFT OUTER JOIN {course_modules} cm ON cm.instance=t.id AND cm.module=:moduleid
            WHERE cm.id IS NULL
        ', array(
            'moduleid' => $moduleid
        ));

        // Delete all Thesis Submissions that do not have a corresponding thesis entry.
        $DB->execute('
            DELETE ts.* FROM {thesis_submissions} ts
            LEFT OUTER JOIN {thesis} t ON t.id=ts.thesis_id
            WHERE t.id IS NULL
        ');

        upgrade_mod_savepoint(true, 2014060900, 'thesis');
    }

    if ($oldversion < 2014120400) {
        $table = new xmldb_table('thesis');

        $notification = new xmldb_field('notification_email', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'name');

        if (!$dbman->field_exists($table, $notification)) {
            $dbman->add_field($table, $notification);
        }

        upgrade_mod_savepoint(true, 2014120400, 'thesis');
    }

    if ($oldversion < 2015031000) {
        $table = new xmldb_table('thesis');

        // Define field intro to be added to thesis.
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null, 'notification_email');
        // Conditionally launch add field intro.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field introformat to be added to thesis.
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, null, null, '0', 'intro');
        // Conditionally launch add field introformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field timemodified to be added to thesis.
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'introformat');
        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2015031000, 'thesis');
    }

    return true;
}
