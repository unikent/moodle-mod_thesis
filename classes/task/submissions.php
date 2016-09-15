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

namespace mod_thesis\task;

global $CFG;
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/mod/thesis/lib.php');
require_once($CFG->dirroot . '/mod/thesis/locallib.php');

/**
 * Thesis submissions task.
 */
class submissions extends \core\task\scheduled_task
{
    public function get_name() {
        return 'Thesis Submissions';
    }

    /*
     * Shorten filename to under 100 characters
     */
    public static function shorten_filename($longfilename) {
        if (mb_strlen($longfilename) > 80) {
            $extension = mb_substr(mb_strrchr($longfilename, '.'), 1);
            $shortfilename = mb_substr($longfilename, 0, 80) . '.' . $extension;
        } else {
            $shortfilename = $longfilename;
        }

        return $shortfilename;
    }

    /**
     * Do submissions.
     */
    public function execute() {
        global $CFG, $DB;

        // Main xml bits.
        $submissions = $DB->get_records('thesis_submissions', array('publish' => 1));
        if (!$submissions) {
            return false;
        }

        $module = $DB->get_record('modules', array('name' => 'thesis'));

        foreach ($submissions as $sub) {
            $thesis = $DB->get_record('thesis', array(
                'id' => $sub->thesis_id
            ));

            $params = array(
                'module' => $module->id,
                'course' => $thesis->course,
                'instance' => $sub->thesis_id
            );
            if (!$cm = $DB->get_record('course_modules', $params)) {
                print_error('invalidcoursemodule');
            }

            $context = \context_module::instance($cm->id);
            $foldername = md5($sub->title) . time();
            $filepath = $CFG->tempdir . '/' . $foldername;
            check_dir_exists($filepath);

            // Create the final location for the gzipped files.
            $finalpath = $CFG->dataroot . '/thesis/';
            check_dir_exists($finalpath);

            // Create public and private folders.
            $publicpath = $filepath . '/public/';
            check_dir_exists($publicpath);
            $privatepath = $filepath . '/private/';
            check_dir_exists($privatepath);

            $fs = get_file_storage();

            // Start of xml generation.
            $xml = new SimpleXMLElementExtended('<?xml version="1.0" encoding="utf-8" ?><eprints></eprints>');
            $xml->addAttribute('xmlns', 'http://eprints.org/ep2/data/2.0');
            $eprint = $xml->addChild('eprint');

            // Adding eprint documents.
            $docs = $eprint->addChild('documents');

            // Set the pos as 1 so it can be incremented for each file.
            $pos = 1;
            if ($publishfiles = $fs->get_area_files($context->id, 'mod_thesis', 'publish', $sub->id, '', false)) {
                foreach ($publishfiles as $f) {
                    $shortfilename = self::shorten_filename($f->get_itemid() . $f->get_filename());

                    if (!$f->copy_content_to($publicpath . $shortfilename)) {
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
                    $file->addChild('filename', $shortfilename);
                    $file->addChild('url', 'public/' . $shortfilename);

                    $doc->addChild('format', $f->get_mimetype());
                    $doc->addChild('language', 'en');

                    // Set security depending on embargo.
                    if ($sub->embargo == 0) {
                        $doc->addChild('security', 'public');
                    } else {
                        $doc->addChild('security', 'staffonly');
                    }

                    $doc->addChild('main', $shortfilename);

                    $embargodate = '';
                    if ($sub->embargo != 0) {
                        $embargodate = ($sub->publish_year + $sub->embargo) . '-' . sprintf('%02d', $sub->publish_month);
                    }

                    $doc->addChild('date_embargo', $embargodate);

                    $doc->addChild('content', 'submitted');
                }
            }

            // Restricted.
            if ($restrictfiles = $fs->get_area_files($context->id, 'mod_thesis', 'private', $sub->id, '', false)) {
                foreach ($restrictfiles as $f) {
                    $shortfilename = self::shorten_filename($f->get_itemid() . $f->get_filename());

                    if (!$f->copy_content_to($privatepath . $shortfilename)) {
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
                    $file->addChild('filename', $shortfilename);
                    $file->addChild('url', 'private/' . $shortfilename);

                    $doc->addChild('format', $f->get_mimetype());
                    $doc->addChild('language', 'en');

                    // Set security depending on embargo.
                    if ($sub->emargo == 0) {
                        $doc->addChild('security', 'public');
                    } else {
                        $doc->addChild('security', 'staffonly');
                    }

                    $doc->addChild('main', $shortfilename);

                    $embargodate = '';
                    if ($sub->embargo != 0) {
                        $embargodate = ($sub->publish_year + $sub->embargo) . '-' . sprintf('%02d', $sub->publish_month);
                    }

                    $doc->addChild('date_embargo', $embargodate);

                    $doc->addChild('content', 'submitted');
                }
            }

            // Permanently restriced.
            if ($permanentfiles = $fs->get_area_files($context->id, 'mod_thesis', 'permanent', $sub->id, '', false)) {
                foreach ($permanentfiles as $f) {
                    $shortfilename = self::shorten_filename($f->get_itemid() . $f->get_filename());

                    if (!$f->copy_content_to($privatepath . $shortfilename)) {
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
                    $file->addChild('filename', $shortfilename);
                    $file->addChild('url', 'private/' . $shortfilename);

                    $doc->addChild('format', $f->get_mimetype());
                    $doc->addChild('language', 'en');
                    $doc->addChild('security', 'staffonly');
                    $doc->addChild('main', $shortfilename);
                    $doc->addChild('content', 'submitted');
                }
            }

            $eprint->addChild('eprint_status', 'archive');

            // Eprints username.
            $euser = $DB->get_record('user', array('id' => $sub->published_by));
            $eprint->addChild('userid', !empty($euser) ? $euser->username : 'admin');

            $eprint->addChild('type', 'thesis');

            // Always show.
            $eprint->addChild('metadata_visibility', 'show');

            // Thesis creator data.
            $user = $DB->get_record('user', array('id' => $sub->user_id));
            $creators = $eprint->addChild('creators');
            $creator = $creators->addChild('item');
            $cname = $creator->addChild('name');
            $cname->addChild('family', $sub->family_name);
            $cname->addChild('given', $sub->given_name);
            $creator->addChild('id', $user->email);

            $contributors = $eprint->addChild('contributors');
            $this->add_contributor(
                $contributors,
                $sub->supervisor_sname,
                $sub->supervisor_fname,
                $sub->supervisor_email
            );
            $this->add_contributor(
                $contributors,
                $sub->second_supervisor_sname,
                $sub->second_supervisor_fname,
                $sub->second_supervisor_email
            );
            $this->add_contributor(
                $contributors,
                $sub->third_supervisor_sname,
                $sub->third_supervisor_fname,
                $sub->third_supervisor_email
            );

            $corpcreators = $eprint->addChild('corp_creators');
            $corpcreators->addChild('item', $sub->corporate_acknowledgement);
            $eprint->addChild('title', $sub->title);
            $eprint->addChild('ispublished', 'pub');
            $eprint->addChildWithCDATA('keywords', $sub->keywords);
            $eprint->addChild('pages', $sub->number_of_pages);
            $eprint->addChild('note', $sub->additional_information);
            $eprint->addChildWithCDATA('abstract', $sub->abstract);
            $eprint->addChild('date', $sub->publish_year . '-' . sprintf('%02d', $sub->publish_month));
            $eprint->addChild('date_type', 'published');
            $eprint->addChild('id_number', '');

            // If additional awarding institution is set.
            if (isset($sub->institution)) {
                $eprint->addChild('institution', 'University of Kent, ' . trim($sub->institution));
            } else {
                $eprint->addChild('institution', 'University of Kent');
            }

            $dept = $DB->get_record('course_categories', array('id' => $sub->department));
            $dept = null == $dept ? 'Unknown' : $dept->name;
            $eprint->addChild('department', $dept);

            $eprint->addChild('thesis_type', mb_strtolower($sub->thesis_type));

            $eprint->addChild('contact_email', $sub->contactemail);
            $eprint->addChild('submit_hardcopy', 'FALSE');

            $eprint->addChild('qual_level', $sub->qualification_level);

            $funders = $eprint->addChild('org_units');
            $funder = $funders->addChild('item');
            $funder->addChild('title', $sub->funding);

            if (!$xml->asXml($filepath . '/import.xml')) {
                mtrace('Errors whilst trying to write xml document to temp dir.');

                return false;
            }

            $archive = array(
                'import.xml' => $filepath . '/import.xml',
                'public' => $publicpath,
                'private' => $privatepath
            );

            $zippacker = get_file_packer('application/x-gzip');
            if (!$zippacker->archive_to_pathname($archive, $filepath . '.tgz')) {
                mtrace('Errors whilst trying to tar xml and files.');

                return false;
            }

            // Delete the tmp directory (not the gzipped file).
            fulldelete($filepath);

            if (copy($filepath . '.tgz', "$finalpath/$foldername.tgz")) {
                unlink($filepath . '.tgz');
            } else {
                mtrace('Errors whilst trying to copy thesis tgz into the thesis dir.');

                return false;
            }

            $DB->update_record('thesis_submissions', array(
                'id' => $sub->id,
                'publish' => 2
            ));
        }

        return true;
    }

    private function add_contributor($contributors, $sname = null, $fname = null, $email = null) {
        if (!$email) {
            return;
        }

        $contributor = $contributors->addChild('item');
        $contributor->addChild('type', 'http://www.loc.gov/loc.terms/relators/THS');
        $conname = $contributor->addChild('name');
        $conname->addChild('family', $sname);
        $conname->addChild('given', $fname);
        $contributor->addChild('id', $email);
    }
}

class SimpleXMLElementExtended extends \SimpleXMLElement {

    /**
     * Adds a child with $value inside CDATA
     * @param unknown $name
     * @param unknown $value
     */
    public function addChildWithCDATA($name, $value = null) {
        $new_child = $this->addChild($name);

        if ($new_child !== null) {
            $node = dom_import_simplexml($new_child);
            $no   = $node->ownerDocument;
            $node->appendChild($no->createCDATASection($value));
        }

        return $new_child;
    }
}
