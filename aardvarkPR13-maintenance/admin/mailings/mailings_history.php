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

require_once (bm_baseDir . '/inc/db_history.php'); // Mailing History Database Handling
require_once (bm_baseDir . '/inc/class.pager.php');

$poMMo = & fireup("secure");
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->assign('returnStr', _T('Mailings Page'));

/* SET PAGE STATE
 * limit		- Nr. of Mailings displayed per Pager-Site
 * mailcount	- Nr. of Mailings in mailing_history table
 */
 
// default key/value pairs of this page's state
// default maybe the last sent mail on top? and not ASC id
$pmState = array(
	'limit' => '10',
	'sortOrder' => 'DESC',
	'sortBy' => 'started'
);
$poMMo->stateInit('mailings_history',$pmState);

$limit = $poMMo->stateVar('limit',$_REQUEST['limit']);
$sortOrder = $poMMo->stateVar('sortOrder',$_REQUEST['sortOrder']);
$sortBy = $poMMo->stateVar('sortBy',$_REQUEST['sortBy']);

$smarty->assign('state',$poMMo->_state);

$mailcount = dbGetMailingCount($dbo); // func in inc/db_history.php

/* Instantiate Pager class (Using modified template from author) */
$p = new Pager();
if ($p->findStart($limit) > $mailcount) $_GET['page'] = '1';
$pages = $p->findPages($mailcount, $limit);
$start = $p->findStart($limit); 
$pagelist = $p->pageList($_GET['page'], $pages);


// Fetch Mailings
$mailings = & dbGetMailingHistory($dbo, $start, $limit, $sortBy, $sortOrder); // func in inc/db_history.php

// If there are mailings display them
$smarty->assign('mailings', $mailings);
$smarty->assign('pagelist', $pagelist);
$smarty->assign('rowsinset', $mailcount);

$smarty->display('admin/mailings/mailings_history.tpl');

bmKill();
?>