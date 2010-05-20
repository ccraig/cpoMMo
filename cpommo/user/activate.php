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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');

$pommo->init(array('authLevel' => 0,'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// make sure email/login is valid
$subscriber = current(PommoSubscriber::get(array('email' => (empty($_REQUEST['email'])) ? '0' : $_REQUEST['email'], 'status' => 1)));
if (empty($subscriber))
	Pommo::redirect('login.php');

// see if an anctivation email was sent to this subscriber in the last 2 minutes;
$query = "
	SELECT 
		*
	FROM 
		".$dbo->table['scratch']."
	WHERE
		`type`=1
		AND `int`=%i
		AND `time` > (NOW() - INTERVAL 2 MINUTE)
	LIMIT 1";
$query = $dbo->prepare($query,array($subscriber['id']));
$test = $dbo->query($query,0);

// attempt to send activation code if once has not recently been sent
if (empty($test)) {
	$code = PommoSubscriber::getActCode($subscriber);
	if (PommoHelperMessages::sendMessage(array('to' => $subscriber['email'], 'code' => $code, 'type' => 'activate'))) {
		
		$smarty->assign('sent', true);
		
		// timestamp this activation email
		$query = "
			INSERT INTO ".$dbo->table['scratch']."
			SET
				`type`=1,
				`int`=%i";
		$query = $dbo->prepare($query,array($subscriber['id']));
		$dbo->query($query);
		
		// remove ALL activation email timestamps older than 2 minutes
		$query = "
			DELETE FROM 
				".$dbo->table['scratch']."
			WHERE
				`type`=1
				AND `time` < (NOW() - INTERVAL 2 MINUTE)";
		$query = $dbo->prepare($query,array());
		$dbo->query($query);
	}
}
else {
	$smarty->assign('sent', false);
}


$smarty->assign('email', $subscriber['email']);
$smarty->display('user/activate.tpl');
Pommo::kill();
?>