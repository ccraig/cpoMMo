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
require ('../bootstrap.php');
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

// make sure email be valid
$email = $_REQUEST['Email'];
if (!PommoHelper::isDupe($email))
	Pommo::redirect('login.php');

// verify activation code (if sent) || that user is not already activated
$code = (isset($_GET['codeTry'])) ? $_GET['code'] : false;
if (PommoPending::actCodeTry($code, $email)) {
	$input = urlencode(serialize(array('Email' => $email)));
	Pommo::redirect('update.php?input='.$input);
}
if ($code !== false)
	$logger->addErr(Pommo::_T('Invalid Activation Code!'));


// check for request to send activation code
if (!empty($_GET['send'])) {
	$code = PommoPending::actCodeGet($email);
	Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
	if (!PommoHelperMessages::sendConfirmation($email, $code, 'activate'))
		$logger->addErr(Pommo::_T('Error sending mail')); 
	else
		$logger->addMsg(Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
}

$smarty->assign('Email', $email);
$smarty->display('user/update_activate.tpl');
Pommo::kill();
?>