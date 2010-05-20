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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

if (PommoMailing::isCurrent())
	Pommo::kill(sprintf(Pommo::_T('A Mailing is currently processing. Visit the %sStatus%s page to check its progress.'),'<a href="mailing_status.php">','</a>'));

$input = array_merge($pommo->_session['state']['mailings_send'], $pommo->_session['state']['mailings_send2']);
$input['charset'] = $input['list_charset'];

// redirect (restart) if body or group id are null...
if (empty($input['mailgroup']) || empty($input['body'])) {
	Pommo::redirect('mailings_send.php');
}

if ($pommo->_config['demo_mode'] == 'on')
	$logger->addMsg(Pommo::_T('Demonstration Mode is on. No Emails will be sent.'));

$group = new PommoGroup($input['mailgroup'], 1);

$input['tally'] = $group->_tally;
$input['group'] = $group->_name;

// if sendaway variable is set (user confirmed mailing parameters), send mailing & redirect.
if (!empty ($_GET['sendaway'])) {
	if ($input['tally'] > 0) {
		$mailing = PommoMailing::make(array(), TRUE);
		$input['status'] = 1;
		$input['current_status'] = 'stopped';
		$input['command'] = 'restart';
		$mailing = PommoHelper::arrayIntersect($input, $mailing);

		$code = PommoMailing::add($mailing);
		if(!PommoMailCtl::queueMake($group->_memberIDs))
			Pommo::kill('Unable to populate queue');

		if (!PommoMailCtl::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?securityCode='.$code))
			Pommo::kill('Unable to spawn background mailer');

		// clear mailing composistion data from session
		PommoAPI::stateReset(array('mailings_send','mailings_send2'));
		
		Pommo::redirect('mailing_status.php');
	}
	else {
		$logger->addMsg(Pommo::_T('Cannot send a mailing to 0 subscribers!'));
	}
}

$smarty->assign($input);
$smarty->display('admin/mailings/mailings_send3.tpl');
?>