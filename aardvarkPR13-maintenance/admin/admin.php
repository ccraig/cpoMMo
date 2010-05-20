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

require('../bootstrap.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();

$smarty->assign('header',array(
	'main' => 'poMMo '.$poMMo->_config['version'],
	'sub' => _T('Powerful mailing list software for').' '.$poMMo->_config['list_name'] 
	));
	
$smarty->display('admin/admin.tpl');
bmKill();
	