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
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');


$pommo->init(array('noDebug' => TRUE, 'keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$pommo->toggleEscaping(); // _T and logger responses will be wrapped in htmlspecialchars

function jsonKill($msg,$key = 0) {
	PommoSubscriber::delete($key);
	$json = "{success: false, msg: \"".$msg."\"}";
	die($json);
}

$input = array_merge($pommo->_session['state']['mailings_send'], $pommo->_session['state']['mailings_send2']);
$input['charset'] = $input['list_charset'];

$subscriber = array(
	'email' => $_POST['Email'],
	'registered' => time(),
	'ip' => $_SERVER['REMOTE_ADDR'],
	'status' => 0,
	'data' => $_POST['d']);
	

if(!PommoHelper::isEmail($_POST['Email']))
	jsonKill(Pommo::_T('Invalid Email Address'));


PommoValidate::subscriberData($subscriber['data'],array('active' => FALSE, 'ignore' => TRUE));
$key = PommoSubscriber::add($subscriber);
if (!$key)
	jsonKill('Unable to add test subscriber',$key);


$input['tally'] = 1;
$input['group'] = Pommo::_T("Test Mailing");

$mailing = PommoMailing::make(array(), TRUE);
$input['status'] = 1;
$input['current_status'] = 'stopped';
$input['command'] = 'restart';
$mailing = PommoHelper::arrayIntersect($input, $mailing);
		
$code = PommoMailing::add($mailing);
if (!$code)
	jsonKill('Unable to add mailing',$key);
	
$queue = array($key);
if(!PommoMailCtl::queueMake($queue))
	jsonKill('Unable to populate queue',$key);
			
if (!PommoMailCtl::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?testMailing=TRUE&securityCode='.$code))
	jsonKill('Unable to spawn background mailer',$key);

$json = "{success: true, msg: \"".Pommo::_T('Mail Sent.')."\"}";
die($json);
?>