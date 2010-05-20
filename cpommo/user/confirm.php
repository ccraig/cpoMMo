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

// TODO -> Add auto firewalling [DOS protection] scripts here.. ie. if Bad/no code received by same IP 3 times, temp/perm ban. 
//  If page is being bombed/DOSed... temp shutdown. should all be handled inside @ _IS_VALID or fireup(); ..

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

if (empty($_GET['code'])) {
	$logger->addMsg(Pommo::_T('No code given.'));
	$smarty->display('user/confirm.tpl');
	Pommo::kill();
}

// lookup code
$pending = PommoPending::get($_GET['code']);

if (!$pending) {
	$logger->addMsg(Pommo::_T('Invalid code! Make sure you copied it correctly from the email.'));
	$smarty->display('user/confirm.tpl');
	Pommo::kill();
}

// Load success messages and redirection URL from config
$config = PommoAPI::configGet(array (
	'site_success',
	'messages',
	'notices'
));
$messages = unserialize($config['messages']);
$notices = unserialize($config['notices']);

if(PommoPending::perform($pending)) {
	
	Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
	
	// get subscriber info
	Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
	$subscriber = current(PommoSubscriber::get(array('id' => $pending['subscriber_id'])));
			
	switch ($pending['type']) {
		case "add" :
			// send/print welcome message
			PommoHelperMessages::sendMessage(array('to' => $subscriber['email'], 'type' => 'subscribe'));
		
			if (isset($notices['subscribe']) && $notices['subscribe'] == 'on') 
				PommoHelperMessages::notify($notices, $subscriber, 'subscribe');
				
			if (!empty($config['site_success']))
				Pommo::redirect($config['site_success']);
				
			break;
			
		case "change" :
		
			if (isset($notices['update']) && $notices['update'] == 'on')
				PommoHelperMessages::notify($notices, $subscriber, 'update');
				
			$logger->addMsg(Pommo::_T('Your records have been updated.'));
			break;
		
		case "password" :
			break;
			
		default :
			$logger->addMsg('Unknown Pending Type.');
			break;
	}
}
$smarty->display('user/confirm.tpl');
Pommo::kill();
?>