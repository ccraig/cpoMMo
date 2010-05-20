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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/pending.php');

$pommo->init(array('authLevel' => 0,'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// Prepare for subscriber form -- load in fields + POST/Saved Subscribe Form
$smarty->prepareForSubscribeForm();

// fetch the subscriber, validate code
$subscriber = current(PommoSubscriber::get(array('email' => (empty($_REQUEST['email'])) ? '0' : $_REQUEST['email'], 'status' => 1)));
if (empty($subscriber))
	Pommo::redirect('login.php');
if ($_REQUEST['code'] != PommoSubscriber::getActCode($subscriber))
	Pommo::kill(Pommo::_T('Invalid activation code.'));
	
// check if we have pending request
if (PommoPending::isPending($subscriber['id'])) {
	$input = urlencode(serialize(array('Email' => $_POST['Email'])));
	Pommo::redirect('pending.php?input='.$input);
}


$config = PommoAPI::configGet(array('notices'));
$notices = unserialize($config['notices']);

if (!isset($_POST['d']))
	$smarty->assign('d', $subscriber['data']);

// check for an update + validate new subscriber info (also converts dates to ints)
if (!empty ($_POST['update']) && PommoValidate::subscriberData($_POST['d'])) {
	
	$newsub = array(
		'id' => $subscriber['id'],
		'email' => $subscriber['email'],
		'data' => $_POST['d']
	);
	
	if (!empty($_POST['newemail'])) { // if change in email, validate and send confirmation of update
		if ($_POST['newemail'] != $_POST['newemail2']) 
			$logger->addErr(Pommo::_T('Emails must match.'));
		elseif (!PommoHelper::isEmail($_POST['newemail']))
			$logger->addErr(Pommo::_T('Invalid Email Address'));
		elseif (PommoHelper::isDupe($_POST['newemail']))
			$logger->addMsg(Pommo::_T('Email address already exists. Duplicates are not allowed.'));	
		else {
			$newsub['email'] = $_POST['newemail'];
			$code = PommoPending::add($newsub, 'change');
			if(!$code)
				die('Failed to Generate Pending Subscriber Code');
			Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
			PommoHelperMessages::sendMessage(array('to' => $newsub['email'], 'code' => $code, 'type' => 'update'));
			
			if (isset($notices['update']) && $notices['update'] == 'on')
				PommoHelperMessages::notify($notices, $newsub, 'update');
		}		
	}
	// else if NO change in email, update subscriber
	elseif (!PommoSubscriber::update($newsub, 'REPLACE_ACTIVE')) 
		$logger->addErr('Error updating subscriber.');
	else { // update successful
		$logger->addMsg(Pommo::_T('Your records have been updated.'));
		Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
		if (isset($notices['update']) && $notices['update'] == 'on')
			PommoHelperMessages::notify($notices, $newsub, 'update');	
	}
}
// check if an unsubscribe was requested
elseif (!empty ($_POST['unsubscribe'])) {
	
	$comments = (isset($_POST['comments'])) ? substr($_POST['comments'],0,255) : false;
	
	$newsub = array(
		'id' => $subscriber['id'],
		'status' => 0,
		'data' => array()
	);
	if (!PommoSubscriber::update($newsub))
		$logger->addErr('Error updating subscriber.');
	else {
		$dbvalues = PommoAPI::configGet(array('messages'));
		$messages = unserialize($dbvalues['messages']);
		
		Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
		
		// send unsubscription email / print unsubscription message
		PommoHelperMessages::sendMessage(array('to' => $subscriber['email'], 'type' => 'unsubscribe'));
		
		if ($comments || isset($notices['unsubscribe']) && $notices['unsubscribe'] == 'on') 
			PommoHelperMessages::notify($notices, $subscriber, 'unsubscribe',$comments);
		
		$smarty->assign('unsubscribe', TRUE);
	}
}

$smarty->assign('email',$subscriber['email']);
$smarty->assign('code',$_REQUEST['code']);
$smarty->display('user/update.tpl');
Pommo::kill();
?>