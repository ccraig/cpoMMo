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
require ('../../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$pommo->toggleEscaping(); // _T and logger responses will be wrapped in htmlspecialchars

function jsonKill($msg) {
	$json = "{success: false, msg: \"".$msg."\"}";
	die($json);
}

/*
if (!PommoHelper::isEmail($_POST['Email']))
	jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.Pommo::_T('Invalid email.'));

if(PommoHelper::isDupe($_POST['Email']))
	jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.Pommo::_T('Email address already exists. Duplicates are not allowed.'));
*/

$user = array(
	'username' => $_POST['username'],
	'userpass' => $_REQUEST['userpass'],
	'userpasscheck' => $_REQUEST['userpasscheck'],
	'usergroup' => $_POST['usergroup']);

$flag = false;
if ($user['userpass'] == $user['userpasscheck']) {
	$flag = TRUE;
}
/*if (!PommoValidate::subscriberData($subscriber['data'],array('active' => FALSE, 'ignore' => TRUE))) {
	if(!isset($_GET['force']))
		jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.Pommo::_T('Invalid or missing information.').'<br />'.implode("<br />", $logger->getAll()));

	$flag = true;
	$subscriber['flag'] = 9; // 9 for "update"
}*/


//$key = PommoSubscriber::add($subscriber);

/*
Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/useradmin/usermanager/class.userplugin.php');
Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/useradmin/usermanager/class.db_userhandler.php');
$userplugin = new UserPlugin($pommo);
$key = $userplugin->addUser($_REQUEST['username'], $_REQUEST['userpass'], $_REQUEST['userpasscheck'], $_REQUEST['usergroup']);
*/

if (!$key)
	jsonKill(Pommo::_T('Error adding user.'));

// some homebrew json.. ;(
$msg = ($flag) ? 
	sprintf(Pommo::_T('User %s added!'),$_POST['username']).' '.Pommo::_T('User has been flagged for update due to invalid or missing information.') :
	sprintf(Pommo::_T('User %s added!'),$_POST['username']);

$json = 'user: "'.$user['username'].'",pass: "'.$user['userpass'].'",usergroup: "'.$user['usergroup'].'",passcheck: "'.$user['userpasscheck'].'"';
/*foreach($user['data'] as $key => $val) 
	$json .= ",$key: \"".htmlspecialchars($val)."\"";
*/

$json = "{success: true, key: $key, msg: \"".$msg."\", data: {".$json."} }";
die($json);
?>