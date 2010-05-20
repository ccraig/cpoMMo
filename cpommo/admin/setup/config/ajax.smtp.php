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
$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

Pommo::requireOnce($pommo->_baseDir . 'inc/lib/phpmailer/class.phpmailer.php');
Pommo::requireOnce($pommo->_baseDir . 'inc/lib/phpmailer/class.smtp.php');


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();
$smarty->assign('returnStr', Pommo::_T('Configure'));

// Read user requested changes	
if (!empty ($_POST['addSmtpServer'])) {
	$server = array (
		'host' => 'mail.localhost',
		'port' => '25',
		'auth' => 'off',
		'user' => '',
		'pass' => ''
	);
	$input['smtp_' . key($_POST['addSmtpServer'])] = serialize($server);
	PommoAPI::configUpdate($input, TRUE);
	$update = true;
}
elseif (!empty ($_POST['updateSmtpServer'])) {
	$key = key($_POST['updateSmtpServer']);
	$server = array (
		'host' => $_POST['host'][$key], 'port' => $_POST['port'][$key], 'auth' => $_POST['auth'][$key], 'user' => $_POST['user'][$key], 'pass' => $_POST['pass'][$key]);
	$input['smtp_' . $key] = serialize($server);
	PommoAPI::configUpdate( $input, TRUE);
	$update = true;
}
elseif (!empty ($_POST['deleteSmtpServer'])) {
	$input['smtp_' . key($_POST['deleteSmtpServer'])] = '';
	PommoAPI::configUpdate( $input, TRUE);
	$update = true;
}
elseif (!empty ($_POST['throttle_SMTP'])) {
	$input['throttle_SMTP'] = $_POST['throttle_SMTP'];
	PommoAPI::configUpdate( $input);
	$update = true;
}

if(isset($update))
	$smarty->assign('output',($update)?Pommo::_T('Configuration Updated.'):Pommo::_T('Please review and correct errors with your submission.'));

// Get the SMTP settings from DB
$smtpConfig = PommoAPI::configGet(array (
	'smtp_1',
	'smtp_2',
	'smtp_3',
	'smtp_4',
	'throttle_SMTP'
));

$smtp[1] = unserialize($smtpConfig['smtp_1']);
$smtp[2] = unserialize($smtpConfig['smtp_2']);
$smtp[3] = unserialize($smtpConfig['smtp_3']);
$smtp[4] = unserialize($smtpConfig['smtp_4']);

if (empty ($smtp[1]))
	$smtp[1] = array (
		'host' => 'mail.localhost',
		'port' => '25',
		'auth' => 'off',
		'user' => '',
		'pass' => ''
	);

// Test the servers
$addServer = FALSE;
$smtpStatus = array ();
for ($i = 1; $i < 5; $i++) {

	if (empty ($smtp[$i])) {
		if (!$addServer)
			$addServer = $i;
		continue;
	}

	$test[$i] = new PHPMailer();

	$test[$i]->Host = (empty ($smtp[$i]['host'])) ? null : $smtp[$i]['host'];
	$test[$i]->Port = (empty ($smtp[$i]['port'])) ? null : $smtp[$i]['port'];
	if (!empty ($smtp[$i]['auth']) && $smtp[$i]['auth'] == 'on') {
		$test[$i]->SMTPAuth = TRUE;
		$test[$i]->Username = (empty ($smtp[$i]['user'])) ? null : $smtp[$i]['user'];
		$test[$i]->Password = (empty ($smtp[$i]['pass'])) ? null : $smtp[$i]['pass'];
	}
	if (@ $test[$i]->SmtpConnect()) {
		$smtpStatus[$i] = TRUE;
		$test[$i]->SmtpClose();
	} else {
		$smtpStatus[$i] = FALSE;
	}
}

$smarty->assign('addServer',$addServer);
$smarty->assign('smtpStatus',$smtpStatus);
$smarty->assign('smtp', $smtp);
$smarty->assign('throttle_SMTP', $smtpConfig['throttle_SMTP']);

$smarty->display('admin/setup/config/ajax.smtp.tpl');
Pommo::kill();
?>