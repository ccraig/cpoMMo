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

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_procedures.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
//$smarty->assign('title', $poMMo->_config['site_name'] . ' - ' . _T('subscriber logon'));
$smarty->prepareForForm();
$smarty->assign('returnStr',_T('Configure'));


// Check if user requested to restore defaults
// TODO: better optimize this.. a lot of DB querying is going on... check w/ debug on!
if (isset($_POST['restore'])) {
	switch (key($_POST['restore'])) {
		case 'subscribe' : dbResetMessageDefaults('subscribe'); break;
		case 'unsubscribe' : dbResetMessageDefaults('unsubscribe'); break;
		case 'password' : dbResetMessageDefaults('password'); break;
		case 'update' : dbResetMessageDefaults('update'); break;
	}
	// reset _POST.
	$_POST = array(); 
}

if (!SmartyValidate::is_registered_form() || empty($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	
	SmartyValidate :: connect($smarty, true); 
	
	SmartyValidate :: register_validator('subscribe_sub', 'Subscribe_sub', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('subscribe_msg', 'Subscribe_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim');
	SmartyValidate :: register_validator('subscribe_suc', 'Subscribe_suc', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('unsubscribe_sub', 'Unsubscribe_sub', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('unsubscribe_msg', 'Unsubscribe_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim');
	SmartyValidate :: register_validator('unsubscribe_suc', 'Unsubscribe_suc', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('update_sub', 'Update_sub', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('update_msg', 'Update_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim');
	SmartyValidate :: register_validator('update_suc', 'Update_suc', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('password_sub', 'Password_sub', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('password_msg', 'Password_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim');
	SmartyValidate :: register_validator('password_suc', 'Password_suc', 'notEmpty', false, false, 'trim');
	
	$formError = array();
	$formError['subscribe_sub'] = $formError['subscribe_suc'] =
	$formError['unsubscribe_sub'] = $formError['unsubscribe_suc'] =
	$formError['update_sub'] = $formError['update_suc'] =
	$formError['password_sub'] = $formError['password_suc'] =
	 _T('Cannot be empty.');
	 
	$formError['subscribe_msg'] =
	$formError['unsubscribe_msg'] =
	$formError['update_msg'] =
	$formError['password_msg'] =
	 _T('You must include "[[URL]]" for the confirm link');
	$smarty->assign('formError',$formError);
	
	// populate _POST with info from database (fills in form values...)
	$dbvalues = $poMMo->getConfig(array('messages'));
	
	if (empty($dbvalues['messages'])) 
		$messages = dbResetMessageDefaults(); 
	else
		$messages = unserialize($dbvalues['messages']);

	if (isset($messages['subscribe'])) {
		$_POST['Subscribe_msg'] = $messages['subscribe']['msg'];
		$_POST['Subscribe_sub'] = $messages['subscribe']['sub'];
		$_POST['Subscribe_suc'] = $messages['subscribe']['suc'];
	}
	if (isset($messages['unsubscribe'])) {
		$_POST['Unsubscribe_msg'] = $messages['unsubscribe']['msg'];
		$_POST['Unsubscribe_sub'] = $messages['unsubscribe']['sub'];
		$_POST['Unsubscribe_suc'] = $messages['unsubscribe']['suc'];
	}
	if (isset($messages['password'])) {
		$_POST['Password_msg'] = $messages['password']['msg'];
		$_POST['Password_sub'] = $messages['password']['sub'];
		$_POST['Password_suc'] = $messages['password']['suc'];
	}
	if (isset($messages['update'])) {
		$_POST['Update_msg'] = $messages['update']['msg'];
		$_POST['Update_sub'] = $messages['update']['sub'];
		$_POST['Update_suc'] = $messages['update']['suc'];
	}
	
}
else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
	// __ FORM IS VALID
		$messages = array();
		
		$messages['subscribe'] = array();
		$messages['subscribe']['msg'] = $_POST['Subscribe_msg'];
		$messages['subscribe']['sub'] = $_POST['Subscribe_sub'];
		$messages['subscribe']['suc'] = $_POST['Subscribe_suc']; 
		
		$messages['unsubscribe'] = array();
		$messages['unsubscribe']['msg'] = $_POST['Unsubscribe_msg']; 
		$messages['unsubscribe']['sub'] = $_POST['Unsubscribe_sub']; 
		$messages['unsubscribe']['suc'] = $_POST['Unsubscribe_suc']; 
		
		$messages['password'] = array();
		$messages['password']['msg'] = $_POST['Password_msg']; 
		$messages['password']['sub'] = $_POST['Password_sub']; 
		$messages['password']['suc'] = $_POST['Password_suc']; 
		
		$messages['update'] = array();
		$messages['update']['msg'] = $_POST['Update_msg']; 
		$messages['update']['sub'] = $_POST['Update_sub']; 
		$messages['update']['suc'] = $_POST['Update_suc']; 
		
		$input = array('messages' => serialize($messages));
		dbUpdateConfig($dbo, $input, TRUE);
		
		$logger->addMsg(_T('Settings updated.'));
	} 
	else {
		// __ FORM NOT VALID
		$logger->addMsg(_T('Please review and correct errors with your submission.'));
	}
}
$smarty->assign($_POST);
$smarty->display('admin/setup/setup_messages.tpl');
bmKill();
?>