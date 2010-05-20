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

// TODO -> page needs to be re-written. It has only been re-worked to fit new demo/subs system.
 /**********************************
	INITIALIZATION METHODS
 *********************************/
define('_IS_VALID', TRUE);

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_subscribers.php');
require_once (bm_baseDir.'/inc/db_fields.php');
require_once (bm_baseDir.'/inc/lib.txt.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->assign('returnStr', _T('Subscribers Manage'));

// sanity check
if ($_REQUEST['table'] != 'subscribers' && $_REQUEST['table'] != 'pending' || empty ($_REQUEST['sid']) || empty ($_REQUEST['action']))
	bmRedirect('subscribers_manage');

$table = $_REQUEST['table'];
	
$appendUrl = "limit=".$_REQUEST['limit']."&order=".$_REQUEST['order']."&orderType=".$_REQUEST['orderType']."&group_id=".$_REQUEST['group_id']."&table=".$_REQUEST['table'];

// CHECK REQUESTS
if (!empty ($_POST['deleteEmails'])) {
	// deleteion confirmation recieved...
	($table == 'pending')? dbPendingDel($dbo, $_POST['deleteEmails']) : dbSubscriberRemove($dbo, $_POST['deleteEmails']);

	bmRedirect('subscribers_manage.php?'.$appendUrl);
}
elseif (!empty ($_POST['addEmails'])) {
	// add to subscribers recieved (pending -> subscribers)
	foreach ($_REQUEST['addEmails'] as $email) {
		dbSubscriberAdd($dbo,$email);
	}
	bmRedirect('subscribers_manage.php?'.$appendUrl);
}
elseif (!empty ($_REQUEST['editId'])) {
	// edit update was recieved...
	$updates = array();

	// create dbGetSubscriber compatible array
	foreach ($_REQUEST['editId'] as $key) {
		
		// make sure email is valid.. TODO: employ all other validation rules here (as in subscribe process.php)
		if (!isEmail($_REQUEST['email'][$key]))
			$_REQUEST['email'][$key] = $_REQUEST['oldEmail'][$key];
				
		$a = array ('email' => $_REQUEST['email'][$key], 'date' => $_REQUEST['date'][$key], 'data' => array ());
		if ($a['email'] != $_REQUEST['oldEmail'][$key])
			$a['oldEmail'] = $_REQUEST['oldEmail'][$key];
		foreach (array_keys($_REQUEST['d'][$key]) as $field_id) {
			$subVal = & $_REQUEST['d'][$key][$field_id];
			if (!empty ($subVal))
				$a['data'][$field_id] = $subVal;
		}
		$updates[] = $a;		
	}

	foreach ($updates as $subscriber) {
		dbSubscriberUpdate($dbo,$subscriber);
	}

	bmRedirect('subscribers_manage.php?'.$appendUrl);
}



// BEGIN MAIN PAGE
$fields = dbGetFields($dbo);


switch ($_REQUEST['action']) {
	case "edit" :

	if (is_array($_REQUEST['sid']) && count($_REQUEST['sid']) > 15) {
		$_REQUEST['sid'] = array_slice($_REQUEST['sid'], 0, 15);
		$subCount = 15;
		$smarty->assign('cropped', TRUE);
	}
	$subscribers = dbGetSubscriber($dbo, $_REQUEST['sid'], 'detailed', $table);
	$smarty->assign('subscribers',$subscribers);
		break;

	case "delete" :
	
	$emails = dbGetSubscriber($dbo, $_REQUEST['sid'], 'email', $table);
	$smarty->assign('emails',$emails);
		break;

	case "add" :
	
	$emails = dbGetSubscriber($dbo, $_REQUEST['sid'], 'email', $table);
	$smarty->assign('emails',$emails);
		break;
		
}


$smarty->assign('fields',$fields);
$smarty->assign('sid',$_REQUEST['sid']);
$smarty->assign('action',$_REQUEST['action']);

$smarty->assign('table',$table);
$smarty->assign('group_id',$group_id);
$smarty->assign('limit',$limit);
$smarty->assign('order',$order);
$smarty->assign('orderType',$orderType);

$smarty->display('admin/subscribers/subscribers_mod.tpl');
bmKill();
?>