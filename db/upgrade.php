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

  if($oldversion<2013060104) {
    $table = new xmldb_table('thesis_submissions');
    $field = new xmldb_field('submitted_for_publishing', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

    if (!$dbman->field_exists($table, $field)) {
      $dbman->add_field($table, $field);
    }

    upgrade_mod_savepoint(true, 2013060104, 'thesis');
  }

  if($oldversion<2013060401) {
    $table = new xmldb_table('thesis_submissions');
    $field = new xmldb_field('number_of_pages', XMLDB_TYPE_TEXT, 'small');

    $dbman->change_field_type($table, $field);

    upgrade_mod_savepoint(true, 2013060401, 'thesis');
  }

  if($oldversion<2013060402) {
    $table = new xmldb_table('thesis_submissions');
    $field = new xmldb_field('terms_accepted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

    if (!$dbman->field_exists($table, $field)) {
      $dbman->add_field($table, $field);
    }

    upgrade_mod_savepoint(true, 2013060402, 'thesis');
  }

  if($oldversion<2013061001) {
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

  return true;
}

