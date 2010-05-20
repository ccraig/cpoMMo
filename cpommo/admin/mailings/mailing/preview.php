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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

if (PommoMailing::isCurrent())
	Pommo::kill(sprintf(Pommo::_T('A Mailing is currently processing. Visit the %sStatus%s page to check its progress.'),'<a href="mailing_status.php">','</a>'));

// TODO -- fix stateInit so we don't NEED to supply the defaults that have already been defined

$dbvalues = PommoAPI::configGet(array(
	'list_fromname',
	'list_fromemail',
	'list_frombounce',
	'list_charset',
	'list_wysiwyg'
));

// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('mailing',array(
	'fromname' => $dbvalues['list_fromname'],
	'fromemail' => $dbvalues['list_fromemail'],
	'frombounce' => $dbvalues['list_frombounce'],
	'list_charset' => $dbvalues['list_charset'],
	'wysiwyg' => $dbvalues['list_wysiwyg'],
	'mailgroup' => 'all',
	'subject' => '',
	'body' => '',
	'altbody' => ''
),
$_POST);

$state['charset'] = $state['list_charset'];

// validate composition
$tempbody = trim($state['body']);
$tempalt = trim($state['altbody']);
if(empty($tempbody) && empty($tempalt) || empty($state['subject'])) {
	$logger->addErr(Pommo::_T('Subject or Message cannot be empty!'));
	$smarty->assign($state);
	$smarty->display('admin/mailings/mailing/preview.tpl');
	Pommo::kill();
}

// get the group
$group = new PommoGroup($state['mailgroup'], 1);
$state['tally'] = $group->_tally;
$state['group'] = $group->_name;


// determine html status
$state['ishtml'] = (empty($tempbody))? 'off' : 'on';


// processs send request
if (!empty ($_REQUEST['sendaway'])) {
	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	Pommo::requireOnce($pommo->_baseDir.'inc/classes/json.php');
	$json = new PommoJSON();
	
	if ($state['tally'] > 0) {
		
		if($state['ishtml'] == 'off') {
			$state['body'] = $state['altbody'];
			$state['altbody'] = '';
		} 
		
		$mailing = PommoMailing::make(array(), TRUE);
		$state['status'] = 1;
		$state['current_status'] = 'stopped';
		$state['command'] = 'restart';
		$mailing = PommoHelper::arrayIntersect($state, $mailing);

		$code = PommoMailing::add($mailing);
		
		if(!PommoMailCtl::queueMake($group->_memberIDs))
			$json->fail('Unable to populate queue');
			
		if (!PommoMailCtl::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?code='.$code))
			$json->fail('Unable to spawn background mailer');
			
		// clear mailing composistion data from session
		PommoAPI::stateReset(array('mailing'));
		$json->add('callbackFunction','redirect');
		$json->add('callbackParams',$pommo->_baseUrl.'admin/mailings/mailing_status.php');
		
	}
	else {
		$json->fail(Pommo::_T('Cannot send a mailing to 0 subscribers!'));
	}
$json->serve();
}

$smarty->assign($state);
$smarty->display('admin/mailings/mailing/preview.tpl');
Pommo::kill();
?>