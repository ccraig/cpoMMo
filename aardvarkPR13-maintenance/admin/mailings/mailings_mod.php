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

//(ct)

/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_IS_VALID', TRUE);
 
require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_history.php');

$poMMo =& fireup("secure");
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/

// default key/value pairs of this page's state
$pmState = array(
	'mailid' => NULL,
	'action' => NULL
);
$poMMo->stateInit('mailings_mod',$pmState);

$action = $poMMo->stateVar('action',$_REQUEST['action']);
$mailid = $poMMo->stateVar('mailid',$_REQUEST['mailid']);

// if mailid or action are empty - redirect
// TODO -> perhaps perform better validation of action/mailID here
//  e.g. have a validType($var,'rule') function? i.e. validType($mailid,numeirc)
if (empty($action) || empty($mailid)) {
	var_dump($action,$mailid,$_REQUEST);
	die();
	bmRedirect('mailings_history.php');
}

$smarty = & bmSmartyInit();
$smarty->assign('returnStr', _T('Mailing History'));
$smarty->assign('mailid',$mailid);
$smarty->assign('action',$action);

// perform deletions if requested
if (!empty($_REQUEST['deleteMailings']) && !empty($_REQUEST['delid'])) {
	if (dbRemoveMailFromHistory($dbo, $_REQUEST['delid']))
		bmRedirect('mailings_history.php');
	else
		$logger->addErr(_T('Trouble deleteing mailgs'));
}

// ACTIONS -> choose what we want to do.
switch ($action) {

	case 'view': 
		$smarty->assign('actionStr', _T('Mailing View'));
		$noassign = TRUE;					
	case 'delete': 
		$mailings = dbGetMailingInfo($dbo, $mailid);
		if (!isset($noassign))
			$smarty->assign('actionStr', _T('Mailing Delete'));
		$smarty->assign('mailings',$mailings);
		
		// assign body to session mailing_data
		foreach ($mailings as $key=>$mailing) {
			if ($mailing['ishtml'] == 'on')
				$poMMo->set(array(
					'mailingData'.$key => array (
						'body' => $mailing['body']
						)
					));
		}
		
		break;

	case 'reload': 
			//Mailid can only be numeric because reloading of multiple Mailings doesn't make sense
			if (is_numeric($mailid)) {
				// Get Mail Data and put in the $pommo variable for the send procedure in mailings_send1,2,3,4.php
				$mailing = current(dbGetMailingInfo($dbo, $mailid));
				$poMMo->set(array(
					'mailingData' => array (
						'fromname' => $mailing['fromname'],
						'fromemail' => $mailing['fromemail'],
						'frombounce' => $mailing['frombounce'],
						'subject' => $mailing['subject'],
						'ishtml' => $mailing['ishtml'],
						'charset' => $mailing['charset'],
						'mailgroup' => ($mailing['mailgroup'] == 'all')? 'all' :
							getGroupId($dbo,$mailing['mailgroup']),
						'altbody' => $mailing['altbody'],
						'body' => $mailing['body']
						)
					));
				bmRedirect('mailings_send.php');
			} else {
				bmRedirect('mailings_history.php');
			}
			
			break;
	default:
		bmKill('Error; unknown action.');
} //switch
	
$smarty->display('admin/mailings/mailings_mod.tpl');
bmKill();
?>