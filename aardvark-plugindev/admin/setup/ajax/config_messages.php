<?php
/**
 * Copyright (C) 2005, 2006, 2007  Brice Burgess <bhb@iceburg.net>
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
require ('../../../bootstrap.php');
$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

// Check if user requested to restore defaults
if (isset($_POST['restore'])) {
	Pommo::requireOnce($pommo->_baseDir.'inc/helpers/messages.php');
	switch (key($_POST['restore'])) {
		case 'subscribe' : $messages = PommoHelperMessages::ResetDefault('subscribe'); break;
		case 'activate' : $messages = PommoHelperMessages::resetDefault('activate'); break;
		case 'unsubscribe' : $messages = PommoHelperMessages::resetDefault('unsubscribe'); break;
	}
	// reset _POST.
	$_POST = array(); 
}

// ADD CUSTOM VALIDATOR FOR CHARSET
function check_notifyMails($value, $empty, & $params, & $formvars) {
	$mails = PommoHelper::trimArray(explode(',',$value));
	$ret = true;
	foreach($mails as $mail)
		if (!empty($mail) && !PommoHelper::isEmail($mail))
			$ret = false;
	return $ret;
}

SmartyValidate :: connect($smarty);
if (!SmartyValidate :: is_registered_form('messages') || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	SmartyValidate::register_form('messages', true);
	
	// register custom criteria
	SmartyValidate::register_criteria('isMails','check_notifyMails', 'messages');
	

	SmartyValidate :: register_validator('subscribe_sub', 'subscribe_sub', 'notEmpty', false, false, 'trim', 'messages');
	SmartyValidate :: register_validator('subscribe_msg', 'subscribe_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim', 'messages');
	SmartyValidate :: register_validator('subscribe_suc', 'subscribe_suc', 'notEmpty', false, false, 'trim', 'messages');
	
	SmartyValidate :: register_validator('activate_sub', 'activate_sub', 'notEmpty', false, false, 'trim', 'messages');
	SmartyValidate :: register_validator('activate_msg', 'activate_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim', 'messages');
	
	SmartyValidate :: register_validator('unsubscribe_suc', 'unsubscribe_suc', 'notEmpty', false, false, 'trim', 'messages');
	
	SmartyValidate :: register_validator('notify_email','notify_email','isMails', false, false, false, 'messages');   
	SmartyValidate :: register_validator('notify_subscribe','notify_subscribe:!^(on|off)$!','isRegExp', false, false, false, 'messages');   
	SmartyValidate :: register_validator('notify_unsubscribe','notify_unsubscribe:!^(on|off)$!','isRegExp', false, false, false, 'messages');   
	SmartyValidate :: register_validator('notify_update','notify_update:!^(on|off)$!','isRegExp', false, false, false, 'messages');   
	SmartyValidate :: register_validator('notify_pending','notify_pending:!^(on|off)$!','isRegExp', false, false, false, 'messages');   
	
	
	$vMsg = array();
	$vMsg['unsubscribe_suc'] = 
	$vMsg['subscribe_sub'] = 
	$vMsg['activate_sub'] = 
	$vMsg['subscribe_suc'] = Pommo::_T('Cannot be empty.');
	
	$vMsg['subscribe_msg'] =
	$vMsg['activate_msg'] = Pommo::_T('You must include "[[URL]]" for the confirm link');
	
	$smarty->assign('vMsg', $vMsg);

	// populate _POST with info from database (fills in form values...)
	$dbvalues = PommoAPI::configGet(array(
		'messages',
		'notices'));
		
	$notices = unserialize($dbvalues['notices']);
	$messages = unserialize($dbvalues['messages']);
		
	if (empty($messages)) 
		$messages = PommoHelperMessages::resetDefault('all');
		
	if (empty($notices)) 
		$notices = array(
			'email' => $pommo->_config['admin_email'],
			'subject' => Pommo::_T('[poMMo Notice]'),
			'subscribe' => 'off',
			'unsubscribe' => 'off',
			'update' => 'off',
			'pending' => 'off');
			
	$p = array();	
	$p['notify_email'] = $notices['email'];
	$p['notify_subject'] = $notices['subject'];
	$p['notify_subscribe'] = $notices['subscribe'];
	$p['notify_unsubscribe'] = $notices['unsubscribe'];
	$p['notify_update'] = $notices['update'];
	$p['notify_pending'] = $notices['pending'];
	
	$p['subscribe_msg'] = $messages['subscribe']['msg'];
	$p['subscribe_sub'] = $messages['subscribe']['sub'];
	$p['subscribe_suc'] = $messages['subscribe']['suc'];
	$p['activate_msg'] = $messages['activate']['msg'];
	$p['activate_sub'] = $messages['activate']['sub'];
	$p['unsubscribe_suc'] = $messages['unsubscribe']['suc'];
	
	$smarty->assign($p);
} 
else {
	// ___ USER HAS SENT FORM ___
	if (SmartyValidate :: is_valid($_POST,'messages')) {
	// __ FORM IS VALID
		$messages = array();
		
		$messages['subscribe'] = array();
		$messages['subscribe']['msg'] = $_POST['subscribe_msg'];
		$messages['subscribe']['sub'] = $_POST['subscribe_sub'];
		$messages['subscribe']['suc'] = $_POST['subscribe_suc']; 
		
		$messages['activate'] = array();
		$messages['activate']['msg'] = $_POST['activate_msg']; 
		$messages['activate']['sub'] = $_POST['activate_sub']; 
		
		$messages['unsubscribe'] = array();
		$messages['unsubscribe']['suc'] = $_POST['unsubscribe_suc'];
		
		$notices = array();
		$notices['email'] = $_POST['notify_email'];
		$notices['subject'] = $_POST['notify_subject'];
		$notices['subscribe'] = $_POST['notify_subscribe'];
		$notices['unsubscribe'] = $_POST['notify_unsubscribe'];
		$notices['update'] = $_POST['notify_update'];
		$notices['pending'] = $_POST['notify_pending'];
		
		$input = array('messages' => serialize($messages),'notices' => serialize($notices));
		PommoAPI::configUpdate( $input, TRUE);
		
		$smarty->assign('output',Pommo::_T('Settings updated.'));
	} 
	else {
		// __ FORM NOT VALID
		$smarty->assign('output',Pommo::_T('Please review and correct errors with your submission.'));
	}
}

$smarty->assign('t_subscribe',Pommo::_T('Subscription'));
$smarty->assign('t_unsubscribe',Pommo::_T('Unsubscription'));
$smarty->assign('t_pending',Pommo::_T('Pending'));
$smarty->assign('t_update',Pommo::_T('Update'));

$smarty->assign($_POST);
$smarty->display('admin/setup/ajax/config_messages.tpl');
Pommo::kill();
			