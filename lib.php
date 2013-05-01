<?php

defined('MOODLE_INTERNAL') || die();

function thesis_supports($feature) {
  switch($feature) {
  case FEATURE_MOD_INTRO: return false;
  default: return null;
  }
}

function thesis_add_instance($data, $mform) {
  global $DB;

  $data->course_id = $data->course;
  $id = $DB->insert_record('thesis', $data);

  return $id;
}

function thesis_update_instance($data, $mform) {
  global $DB;

  $data->id = $data->instance;
  $DB->update_record('thesis', $data);

  return true;
}

function thesis_cron () {
  global $CFG, $DB;

  require_once($CFG->libdir . '/filelib.php');
  require_once($CFG->dirroot.'/mod/thesis/locallib.php');

  //Main xml bits

  $submissions = $DB->get_records('thesis_submissions', array('publish'=>1));

  if(!$submissions) {
    return false;
  }

  $tmpdir = $CFG->tempdir;

  $module = $DB->get_record('modules',array('name'=>'thesis'));

  foreach ($submissions as $sub) {

    $thesis = $DB->get_record('thesis',array('id'=>$sub->thesis_id));
    if( !$cm = $DB->get_record('course_modules',array('module'=>$module->id,'course'=>$thesis->course_id,'instance'=>$sub->thesis_id)) ) {
      print_error('invalidcoursemodule');
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $folder_name = md5($sub->title) . time();
    $filepath = $tmpdir . '/' . $folder_name;
    check_dir_exists($filepath);
    $fs = get_file_storage();

    // Start of xml generation
    $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><eprints></eprints>");
    $xml->addAttribute('xmlns', 'http://eprints.org/ep2/data/2.0');
    $eprint = $xml->addChild('eprint');

    // Adding eprint documents
    $docs = $eprint->addChild('documents');

    // Set the pos as 1 so it can be incremented for each file.
    $pos = 1;
    if($publish_files = $fs->get_area_files($context->id, 'mod_thesis', 'publish', $sub->id, '', false)) {
      foreach ($publish_files as $f) {
        $fp = $filepath . '/public/';
        check_dir_exists($fp);
        if(!$f->copy_content_to($fp . $f->get_filename())) {
          mtrace('Errors whilst trying to copy thesis files to temp dir.');
          return false;
        }

        $doc = $docs->addChild('document');
          $doc->addChild('pos', $pos);
          $doc->addChild('placement', $pos);
          $pos++;
            $files = $doc->addChild('files');
              $file = $files->addChild('file');
                $file->addChild('datasetid', 'document');
                $file->addChild('filename', $f->get_filename());
                $file->addChild('url', 'public/' . $f->get_filename());

            $doc->addChild('format', $f->get_mimetype());
            $doc->addChild('language', 'en');
            $doc->addChild('security', 'public');
            $doc->addChild('main', $f->get_filename());
            $doc->addChild('date_embargo', '');
            $doc->addChild('content', 'submitted');
      }
    }


    if($restrict_files = $fs->get_area_files($context->id, 'mod_thesis', 'private', $sub->id, '', false)) {
      foreach ($restrict_files as $f) {

        $fp = $filepath . '/private/';
        check_dir_exists($fp);
        if(!$f->copy_content_to($fp . $f->get_filename())) {
          mtrace('Errors whilst trying to copy thesis files to temp dir.');
          return false;
        }

        $doc = $docs->addChild('document');
          $doc->addChild('pos', $pos);
          $doc->addChild('placement', $pos);
          $pos++;
            $files = $doc->addChild('files');
              $file = $files->addChild('file');
                $file->addChild('datasetid', 'document');
                $file->addChild('filename', $f->get_filename());
                $file->addChild('url', 'private/' . $f->get_filename());

            $doc->addChild('format', $f->get_mimetype());
            $doc->addChild('language', 'en');
            $doc->addChild('security', 'staffonly');
            $doc->addChild('main', $f->get_filename());
            $doc->addChild('date_embargo', '');
            $doc->addChild('content', 'submitted');
      }
    }

    $eprint->addChild('eprint_status', 'buffer');

    // Eprints username
    $e_user = $DB->get_record('user', array('id'=>$sub->published_by));
    $eprint->addChild('userid', $e_user->username);

    $eprint->addChild('type', 'thesis');
    $eprint->addChild('metadata_visibility', $sub->metadata === '1' ? 'show' : 'hide');

    // Thesis creator data
    $user = $DB->get_record('user', array('id' => $sub->user_id));
    $creators = $eprint->addChild('creators');
      $creator = $creators->addChild('item');
        $c_name = $creator->addChild('name');
          $c_name->addChild('family', $user->lastname);
          $c_name->addChild('given', $user->firstname);
        $creator->addChild('id', $user->email);


    $contributors = $eprint->addChild('contributors');
      $contributor = $contributors->addChild('item');
        $contributor->addChild('type', 'http://www.loc.gov/loc.terms/relators/THS');
        $con_name = $contributor->addChild('name');
          $con_name->addChild('family', $sub->supervisor_sname);
          $con_name->addChild('given', $sub->supervisor_fname);
        $contributor->addChild('id', $sub->supervisor_email);

    $eprint->addChild('corp_creators', $sub->corporate_acknowledgement);
    $eprint->addChild('title', $sub->title);
    $eprint->addChild('ispublished', 'unpub');
    $eprint->addChild('keywords', $sub->keywords);
    $eprint->addChild('pages', $sub->number_of_pages);
    $eprint->addChild('note', $sub->additional_information);
    $eprint->addChild('abstract', $sub->abstract);
    $eprint->addChild('date', date('Y-m-d', $sub->publishdate));
    $eprint->addChild('date_type', 'submitted');
    $eprint->addChild('id_number', $sub->identification_number);
    $eprint->addChild('institution', 'University of Kent');
    $eprint->addChild('department', $sub->department);
    $eprint->addChild('thesis_type', strtolower($sub->thesis_type));


    $eprint->addChild('contact_email', $sub->contactemail);
    $eprint->addChild('submit_hardcopy', 'FALSE');

    $eprint->addChild('qualification_level', $sub->qualification_level);

    $eprint->addChild('qualification_name', $sub->qualification_name);

    $funders = $eprint->addChild('org_units');
      $funder = $funders->addChild('item');
      $funder->addChild('title', $sub->funding);

    if(!$xml->asXml($filepath . '/import.xml')) {
      mtrace('Errors whilst trying to write xml document to temp dir.');
      return false;
    }

    $archive =array(
      'import.xml' => $filepath . '/import.xml',
      'public' => $filepath . '/public',
      'private' => $filepath . '/private'
    );

    $zippacker = get_file_packer('application/zip');
    if(!$zippacker->archive_to_pathname($archive, $filepath .'.zip')) {
      mtrace('Errors whilst trying to zip up xml and files.');
      return false;
    }

    rrmdir($filepath);

    check_dir_exists($tmpdir.'/thesis/');
    if (copy($filepath .'.zip', $tmpdir . '/thesis/' . $folder_name . '.zip')) {
      unlink($filepath .'.zip');
    } else {
      mtrace('Errors whilst trying to copy thesis zip into the thesis dir.');
      return false;
    }

    $DB->update_record('thesis_submissions',array('id'=>$sub->id,'publish'=>2));

    // //formatting stuff for test printing
    // $dom = dom_import_simplexml($xml)->ownerDocument;
    // $dom->formatOutput = true;
    // $scrn = htmlspecialchars($dom->saveXml(), ENT_QUOTES);

    // echo '<pre>';
    // var_dump($scrn);
    // echo '</pre>';

  }
}

function rrmdir($dir) { 
  if (is_dir($dir)) { 
    $objects = scandir($dir); 
    foreach ($objects as $object) { 
      if ($object != "." && $object != "..") { 
        if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
      } 
    } 
    reset($objects); 
    rmdir($dir); 
  } 
}

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
