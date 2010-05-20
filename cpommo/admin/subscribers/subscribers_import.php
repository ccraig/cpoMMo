<?php
/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 * 
 * This file is part of poMMo (http://www.pommo.org)
 * 
 * poMMo is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published 
 * by the Free Software Foundation; either version 2, or any later version.
 * 
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */
 
/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/import.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();


//Pommo::kill('Importing and Exporting is temporarily disabled until PR15');

// Maximum File Size (in MB) 
$max_file_size = 2;
$smarty->assign('maxSize',$max_file_size * 1024 * 1024);

// Filename (in $_FILES array)
$fname = "csvfile";

if(isset($_POST['submit'])) {

	// POST exists -- set pointer to content
	$fp = false;
	$box = false;
	
	if (!empty($_FILES[$fname]['tmp_name']))
		$fp =& fopen($_FILES[$fname]['tmp_name'], "r");
	elseif (!empty($_POST['box'])) {
		$str =& $_POST['box']; 
		
		// wrap $c as a file stream -- requires PHP 4.3.2
		//  for early versions investigate using tmpfile() -- efficient?
		stream_wrapper_register("pommoCSV", "PommoCSVStream")
			or Pommo::kill('Failed to register pommoCSV');
		$fp = fopen("pommoCSV://str", "r+"); 
		
		$box = true;
	}
	
	if(is_resource($fp)) {
		
		if($_POST['type'] == 'txt') { // list of emails 
			$a = array(); 
			while (($data = fgetcsv($fp,2048,',','"')) !== FALSE) {
				foreach($data as $email)
					if(PommoHelper::isEmail($email))
						array_push($a,$email);
			}
			
			// remove dupes
			$includeUnsubscribed = isset($_REQUEST['excludeUnsubscribed']) ? false : true;
			$dupes =& PommoHelper::isDupe($a,$includeUnsubscribed);
			
			if (!$dupes)
				$dupes = array();
			$emails = array_diff($a, $dupes);
			
			$pommo->set(array(
				'emails' => $emails,
				'dupes' => (count($dupes))));
			Pommo::redirect('import_txt.php');
		}
		elseif($_POST['type'] == 'csv') { // csv of subscriber data, store first 10 for preview
			$a = array(); $i = 1;
			while (($data = fgetcsv($fp,2048,',','"')) !== FALSE) {
				array_push($a,$data);
					
				if($i > 9) // only get first 10 lines -- move file
					break;
				$i++;
			}
			
			// save file for access after assignments
			if ($box) {
				// when PHP5 is widespread, switch to file_put_contents()  && use the $fp stream
				if (!$handle = fopen($pommo->_workDir.'/import.csv', 'w'))
					Pommo::kill('Could not write to temp CSV file ('.$pommo->_workDir.'/import.csv)');
				
				if (fwrite($handle, $_POST['box']) === FALSE)
      				Pommo::kill('Could not write to temp CSV file ('.$pommo->_workDir.'/import.csv)');
				
				 fclose($handle);
			}
			else {
				move_uploaded_file($_FILES[$fname]['tmp_name'], $pommo->_workDir.'/import.csv')
					or Pommo::kill('Could not write to temp CSV file ('.$pommo->_workDir.'/import.csv)');
			}
			
			$pommo->set(array('preview' => $a));
			Pommo::redirect('import_csv.php'.((isset($_REQUEST['excludeUnsubscribed']))?'?excludeUnsubscribed=true' : ''));
		}
		else {
			$logger->addErr('Unknown Import Type');
		}
	}
}

$smarty->display('admin/subscribers/subscribers_import.tpl');
Pommo::kill();
?>