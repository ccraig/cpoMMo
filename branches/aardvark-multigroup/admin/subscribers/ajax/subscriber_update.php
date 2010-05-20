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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

$pommo->init();
$dbo = & $pommo->_dbo;
$logger = & $pommo->_logger;



/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
 Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.json.php');
$pommo->logErrors(); // PHP Errors are logged, turns display_errors off.
$pommo->toggleEscaping(); // Wraps _T and logger responses with htmlspecialchars()

// TODO page needs rewrite to utilize the json class for output. e.g. admin/mailings/ajax/status_poll.php


function jsonKill($msg, $success = FALSE) {
	$status = ($success) ? "true" : "false";
	$json = "{success: $status, msg: \"".$msg."\"}";
	die($json);
}

if (!is_numeric($_GET['key']) || $_GET['key'] < 1)
	jsonKill(Pommo::_T('Error updating subscriber.')." ".'Bad Key');
	
if (isset($_POST['email'])) {
	if (!PommoHelper::isEmail($_POST['email']))
		jsonKill(Pommo::_T('Error updating subscriber.').' '.Pommo::_T('Invalid Email.'));
	if(PommoHelper::isDupe($_POST['email']))
		jsonKill(Pommo::_T('Error updating subscriber.').' '.Pommo::_T('Email address already exists. Duplicates are not allowed.'));
}

$s = @array(
	'id' => $_GET['key'],
	'email' => $_POST['email']
	);
	
$data = array();
foreach($_POST as $key => $val) {
	if (is_numeric($key))
		$data[$key] = $val;
}

if (!PommoValidate::subscriberData($data,array('skipReq' => TRUE, 'active' => FALSE)))
	jsonKill(Pommo::_T('Error updating subscriber.').' '.Pommo::_T('Fields failed validation')." >>> ".implode($logger->getAll(), " : "));

$s['data'] = $data;
if (!PommoSubscriber::update($s,FALSE))
	jsonKill(Pommo::_T('Error updating subscriber.'));
	
jsonKill('',TRUE);
?>