<?php

//Main xml bits
$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><eprints></eprints>");
$xml->addAttribute('xmlns', 'http://eprints.org/ep2/data/2.0');

// A record
$eprint = $xml->addChild('eprint');

// Adding eprint documents
$docs = $eprint->addChild('documents');
// $doc = new Array();


// Adding creators
$creators = $eprint->addChild('creators');

// info
$status = $eprint->addChild('status', 'archive');
$ui = $eprint->addChild('userid', 3333);
$type = $eprint->addChild('type', 'thesis');
$mdv = $eprint->addChild('metadata_visibility', 'show');
$cemal = $eprint->addChild('contact_email', 'Contact email address');
$title = $eprint->addChild('title', 'Title');
$ip = $eprint->addChild('ispublished', 'pub');
$fts = $eprint->addChild('full_text_status', 'restricted');
$keywords = $eprint->addChild('keywords', 'Uncontrolled keywords');
$note = $eprint->addChild('note', 'Additional information');
$abstract = $eprint->addChild('abstract', 'Abstract');
$date = $eprint->addChild('date', '2013-02-26');
$date_type = $eprint->addChild('date_type', 'published');
$id_number = $eprint->addChild('id_number', 'Thesis number');
$institution = $eprint->addChild('institution', 'Institution');
$department = $eprint->addChild('department', 'Department');
$thesis_type = $eprint->addChild('thesis_type', 'phd');
$referencetext = $eprint->addChild('referencetext', 'References');
$is_edited = $eprint->addChild('is_edited', 'FALSE');
$submit_hardcopy = $eprint->addChild('submit_hardcopy', 'FALSE');
$deposit_to = $eprint->addChild('deposit_to', 'archive');



//formatting stuff for test printing
$dom = dom_import_simplexml($xml)->ownerDocument;
$dom->formatOutput = true;
echo $dom->saveXml();