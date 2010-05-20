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

// Progress bar modified from the works of Juha Suni <juha.suni@ilmiantajat.fi>


/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');

$poMMo = & fireup('secure','keep');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();

$sql = 'SELECT subscriberCount FROM ' . $dbo->table['mailing_current'];
$sc = $dbo->query($sql,0);
$subscriberCount = ($sc) ? $sc : 0;
$smarty->assign('subscriberCount', $subscriberCount);


$smarty->display('admin/mailings/mailing_status.tpl');
bmKill();
?>