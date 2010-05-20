<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/
 
/**********************************
	INITIALIZATION METHODS
*********************************/
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');
require_once(bm_baseDir.'/inc/lib.import.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();


// Maximum File Size (in MB) 
$max_file_size = 2;
$smarty->assign('maxSize',$max_file_size * 1024 * 1024);

// Filename (in $_FILES array)
$fname = "csvfile";


// if file is uploaded, validate & re-direct.
if (!empty($_FILES[$fname]['tmp_name'])) {
	
	$csvArray =& csvPrepareFile($_FILES[$fname]['tmp_name']);
	
	if (is_array($csvArray)) {
		$sessionArray['csvArray'] =& $csvArray;
		$poMMo->set($sessionArray);
		bmRedirect('subscribers_import2.php');
	}
}

$smarty->display('admin/subscribers/subscribers_import.tpl');
bmKill();
?>