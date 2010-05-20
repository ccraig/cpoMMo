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
require ('../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$pommo->init(array('authLevel' => 0,'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// attempt to detect if referer was set 
//  TODO; enable HTTP_REFERER after stripping out ?input= tags. These will continually repeat
//$referer = (!empty($_POST['bmReferer'])) ? $_POST['bmReferer'] : $_SERVER['HTTP_REFERER'];
$referer = (!empty($_POST['bmReferer'])) ? $_POST['bmReferer'] : $pommo->_http.$pommo->_baseUrl.'user/subscribe.php';

// append stored input
$smarty->assign('referer',$referer.'?input='.urlencode(serialize($_POST)));

/**********************************
	VALIDATE INPUT
 *********************************/

if (empty ($_POST['pommo_signup']))
	Pommo::redirect('login.php');

$subscriber = array(
	'email' => $_POST['Email'],
	'registered' => time(),
	'ip' => $_SERVER['REMOTE_ADDR'],
	'status' => 1,
	'data' => @$_POST['d'],
);

// ** check for correct email syntax
if (!PommoHelper::isEmail($subscriber['email']))
	$logger->addErr(Pommo::_T('Invalid Email Address'));
		
// ** check if email already exists in DB ("duplicates are bad..")
if (PommoHelper::isDupe($subscriber['email'])) {
	$logger->addErr(Pommo::_T('Email address already exists. Duplicates are not allowed.'));
	$smarty->assign('dupe', TRUE);
}

// check if errors exist with data, if so print results and die.
if ($logger->isErr() || !PommoValidate::subscriberData($subscriber['data'], array(
	'active' => FALSE))) {
	$smarty->assign('back', TRUE);
	$smarty->display('user/process.tpl');
	Pommo::kill();
}

$comments = (isset($_POST['comments'])) ? substr($_POST['comments'],0,255) : false;

/**********************************
	ADD SUBSCRIBER
 *********************************/
 
$config = PommoAPI::configGet(array (
	'site_success', // URL to redirect to on success, null is us (default)
	'site_confirm', // URL users will see upon subscription attempt, null is us (default)
	'list_confirm', // Requires email confirmation
	'notices'
));
$notices = unserialize($config['notices']);
Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');

if ($config['list_confirm'] == 'on') { // email confirmation required. 
	// add user as "pending"
	
	$subscriber['pending_code'] = PommoHelper::makeCode();
	$subscriber['pending_type'] = 'add';
	$subscriber['status'] = 2;
	
	$id = PommoSubscriber::add($subscriber);
	if (!$id) {
		$logger->addErr('Error adding subscriber! Please contact the administrator.');
		$smarty->assign('back', TRUE);
	}
	else {
		
		$logger->addMsg(Pommo::_T('Subscription request received.'));
		
		// send confirmation message.
		if (PommoHelperMessages::sendMessage(array('to' => $subscriber['email'], 'code' => $subscriber['pending_code'], 'type' => 'confirm'))) {
			$subscriber['registered'] = date("F j, Y, g:i a",$subscriber['registered']);
			if ($comments || isset($notices['pending']) && $notices['pending'] == 'on')
				PommoHelperMessages::notify($notices, $subscriber, 'pending', $comments);
			
			if ($config['site_confirm'])
				Pommo::redirect($config['site_confirm']);
		}
		else {
			$smarty->assign('back', TRUE);
			// delete the subscriber
			PommoSubscriber::delete($id);
		}
	}
}
else { // no email confirmation required
	if (!PommoSubscriber::add($subscriber)) {
		$logger->addErr('Error adding subscriber! Please contact the administrator.');
		$smarty->assign('back', TRUE);
	}
	else {
		
		// send/print welcome message
		PommoHelperMessages::sendMessage(array('to' => $subscriber['email'], 'type' => 'subscribe'));
	
		$subscriber['registered'] = date("F j, Y, g:i a",$subscriber['registered']);
		if ($comments || isset($notices['subscribe']) && $notices['subscribe'] == 'on')
			PommoHelperMessages::notify($notices, $subscriber, 'subscribe',$comments);
		
		// redirect
		if ($config['site_success'])
			Pommo::redirect($config['site_success']);
	}
	
}
$smarty->display('user/process.tpl');
Pommo::kill();

?>