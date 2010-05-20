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

$pommo->init(array('authLevel' => 0,'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// make sure email be valid

$subscriber = current(PommoSubscriber::get(array('email' => (empty($_REQUEST['email'])) ? '0' : $_REQUEST['email'], 'status' => 1)));
if (empty($subscriber))
	Pommo::redirect('login.php');


// check for request to send activation code
if (!empty($_GET['send'])) {
	$code = md5($subscriber['id'].$subscriber['registered']);
	Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
	if (!PommoHelperMessages::sendConfirmation($subscriber['email'], $code, 'activate'))
		$logger->addErr(Pommo::_T('Error sending mail')); 
	else
		Pommo::redirect('activate.php?sent=true&email='.$subscriber['email']);
}

$smarty->assign('sent', (isset($_GET['sent']))?true:false);
$smarty->assign('email', $subscriber['email']);
$smarty->display('user/activate.tpl');
Pommo::kill();
?>