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

$input = (isset($_GET['input'])) ?
	unserialize($_GET['input']) : array('Email' => NULL);

$pending = (isset($input['adminID'])) ? // check to see if we're resetting admin password
	PommoPending::getBySubID(0) :
	PommoPending::getByEmail($input['Email']);
if (!$pending) 	
	Pommo::redirect('login.php');
	

switch ($pending['type']) {
	case "add" : 
		$msg = Pommo::_T('subscription request');
		$pending['type'] = 'confirm'; // normalize for PommoHelperMessages::sendMessage
		break;
	case "change" :
		$msg = Pommo::_T('record update request');
		$pending['type'] = 'update'; // normalize for PommoHelperMessages::sendMessage
		break;
	case "password" :
		$msg = Pommo::_T('password change request');
		break;
	default:
		Pommo::redirect('login.php?badPendingType=TRUE');
}
	
// check if user wants to reconfirm or cancel their request
if (!empty ($_POST)) {
	if (isset ($_POST['reconfirm'])) {
		Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
		PommoHelperMessages::sendMessage(array('to' => $input['Email'], 'code' => $pending['code'], 'type' => $pending['type']));	
	} elseif (isset($_POST['cancel'])) {
		if (PommoPending::cancel($pending))
			$logger->addMsg(sprintf(Pommo::_T('Your %s has been cancelled.'),$msg));		
	}
	$smarty->assign('nodisplay',TRUE);
} else {
	$logger->addMsg(sprintf(Pommo::_T('Your %s is still pending. To complete this request, please review the confirmation email sent to %s.'), $msg, $input['Email']));
}
$smarty->display('user/pending.tpl');
Pommo::kill();
?>