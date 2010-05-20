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
require('../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/pending.php');

$pommo->init(array('authLevel' => 0, 'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

if (isset($_GET['input'])) {
	$input = (unserialize($_GET['input']));
}

$pending = (isset($input['adminID'])) ? // check to see if we're resetting admin password
	PommoPending::getBySubID(0) :
	PommoPending::getByEmail($input['Email']);
if (!$pending) 	
	Pommo::redirect('login.php');

// check if user wants to reconfirm or cancel their request
if (!empty ($_POST)) {
	if (isset ($_POST['reconfirm'])) {
		Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
		
		switch ($pending['type']) {
			case "add" :
				$status = PommoHelperMessages::sendConfirmation($input['Email'], $pending['code'], 'subscribe');
				break;
			case "change" :
				$status = PommoHelperMessages::sendConfirmation($input['Email'], $pending['code'], 'update');
				break;
			case "password" :
				$status = PommoHelperMessages::sendConfirmation($input['Email'], $pending['code'], 'password');
				break;
		}
		if (!$status) 
			$logger->addErr(Pommo::_T('Error sending mail'));
		else
			$logger->addMsg(sprintf(Pommo::_T('A confirmation email has been sent to %s. It should arrive within the next few minutes. Please follow its instructions to complete your request. Thanks!'),$input['Email']));
	} elseif (isset($_POST['cancel'])) {
		PommoPending::cancel($pending);
		$logger->addMsg(Pommo::_T('Your pending request has been cancelled.'));		
	}
	$smarty->assign('nodisplay',TRUE);
} else {
	switch ($pending['type']) {
		case "add" :
		case "change" :
		case "password" :
			$logger->addMsg(Pommo::_T('You have pending changes. Please respond to your confirmation email'));
			break;
		default :
			$logger->addErr(sprintf(Pommo::_T('Please Try Again! %s login %s'), '<a href="' . $pommo->_baseUrl . 'user/login.php">', '</a>'));
	}
}
$smarty->display('user/pending.tpl');
Pommo::kill();
?>