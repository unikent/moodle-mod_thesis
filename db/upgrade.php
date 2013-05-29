<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_thesis_upgrade($oldversion=0) {

  global $CFG, $DB, $OUTPUT;

  $dbman = $DB->get_manager();

  if($oldversion<2013052904) {
    $table = new xmldb_table('thesis');
    $field = new xmldb_field('course_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'name');

    $dbman->rename_field($table, $field, 'course');

    $index = new xmldb_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
    if (!$dbman->index_exists($table, $index)) {
      $dbman->add_index($table, $index);
    }

    upgrade_mod_savepoint(true, 2013052904, 'thesis');
  }

  return true;
}

