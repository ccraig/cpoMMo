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
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;
	
/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

$current = PommoMailing::isCurrent();


if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);

	SmartyValidate :: register_validator('email', 'email', 'isEmail', false, false, 'trim');
	$vMsg = array ();
	$vMsg['email'] = Pommo::_T('Invalid email address');
	$smarty->assign('vMsg', $vMsg);
	
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST) && !$current) {
		// __ FORM IS VALID
		Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');
		Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
		Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');
		
		// get a copy of the message state
		// composition is valid (via preview.php)
		$state = $pommo->_session['state']['mailing'];
		
		// create temp subscriber
		$subscriber = array(
			'email' => $_POST['email'],
			'registered' => time(),
			'ip' => $_SERVER['REMOTE_ADDR'],
			'status' => 0,
			'data' => $_POST['d']);
		PommoValidate::subscriberData($subscriber['data'],array('active' => FALSE, 'ignore' => TRUE, 'log' => false));
		$key = PommoSubscriber::add($subscriber);
		if (!$key)
			$logger->addErr('Unable to Add Subscriber');
		else { // temp subscriber created
			$state['tally'] = 1;
			$state['group'] = Pommo::_T('Test Mailing');
			
			if($state['ishtml'] == 'off') {
				$state['body'] = $state['altbody'];
				$state['altbody'] = '';
			} 
			
			// create mailing
			$mailing = PommoMailing::make(array(), TRUE);
			$state['status'] = 1;
			$state['current_status'] = 'stopped';
			$state['command'] = 'restart';
			$state['charset'] = $state['list_charset'];
			$mailing = PommoHelper::arrayIntersect($state, $mailing);
			$code = PommoMailing::add($mailing);
			
			// populate queue
			$queue = array($key);
			if(!PommoMailCtl::queueMake($queue))
				$logger->addErr('Unable to Populate Queue');
			
			// spawn mailer
			else if (!PommoMailCtl::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?test=TRUE&code='.$code))
				$logger->addErr('Unable to spawn background mailer');
			else 
				$smarty->assign('sent',$_POST['email']);
		}
	} elseif ($current) {
		$logger->addMsg(Pommo::_T('A mailing is currently taking place. Please try again later.'));
		$smarty->assign($_POST);
	}
	else { 
		// __ FORM NOT VALID
		$logger->addMsg(Pommo::_T('Please review and correct errors with your submission.'));
		$smarty->assign($_POST);
	}
}

if ($pommo->_config['demo_mode'] == 'on')
	$logger->addMsg(sprintf(Pommo::_T('%sDemonstration Mode%s is on -- no Emails will actually be sent. This is good for testing settings.'),'<a href="'.$pommo->_baseUrl.'admin/setup/setup_configure.php#mailings">','</a>'));

$smarty->assign('fields',PommoField::get());
$smarty->display('admin/mailings/mailing/ajax.mailingtest.tpl');
Pommo::kill();
?>