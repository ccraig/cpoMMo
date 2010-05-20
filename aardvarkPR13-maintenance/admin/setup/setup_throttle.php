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
require_once (bm_baseDir . '/inc/db_procedures.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();
$smarty->assign('returnStr',_T('Configure'));



// Read user requested changes	
if (!empty($_POST['throttle-restore'])) {
	$input = array ('throttle_MPS' => 3, 'throttle_BPS' => 0, 'throttle_DP' => 10, 'throttle_DBPP' => 0,'throttle_DMPP' => 0);
	dbUpdateConfig($dbo,$input,TRUE);
}
elseif(!empty($_POST['throttle-submit'])) {
	$input = array ('throttle_MPS' => str2db($_POST['mps']), 'throttle_BPS' => str2db($_POST['kbps']), 'throttle_DP' => str2db($_POST['dp']), 'throttle_DBPP' => str2db($_POST['dbpp']),'throttle_DMPP' => str2db($_POST['dmpp']));
	dbUpdateConfig($dbo,$input,TRUE);
}

$config= $poMMo->getConfig(array('throttle_MPS', 'throttle_BPS', 'throttle_DP', 'throttle_DBPP','throttle_DMPP'));

$smarty->assign($config);
$smarty->display('admin/setup/setup_throttle.tpl');
bmKill();
?>