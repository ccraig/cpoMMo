<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/db_procedures.php');
require_once (bm_baseDir . '/inc/phpmailer/class.phpmailer.php');
require_once (bm_baseDir . '/inc/phpmailer/class.smtp.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();
$smarty->assign('returnStr', _T('Configure'));

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
	dbUpdateConfig($dbo, $input, TRUE);
}
elseif (!empty ($_POST['updateSmtpServer'])) {
	$key = key($_POST['updateSmtpServer']);
	$server = array (
		'host' => str2db($_POST['host'][$key]
	), 'port' => str2db($_POST['port'][$key]), 'auth' => str2db($_POST['auth'][$key]), 'user' => str2db($_POST['user'][$key]), 'pass' => str2db($_POST['pass'][$key]));
	$input['smtp_' . $key] = serialize($server);
	dbUpdateConfig($dbo, $input, TRUE);
}
elseif (!empty ($_POST['deleteSmtpServer'])) {
	$input['smtp_' . key($_POST['deleteSmtpServer'])] = '';
	dbUpdateConfig($dbo, $input, TRUE);
}
elseif (!empty ($_POST['throttle_SMTP'])) {
	$input['throttle_SMTP'] = str2db($_POST['throttle_SMTP']);
	dbUpdateConfig($dbo, $input);
}

// Get the SMTP settings from DB
$smtpConfig = $poMMo->getConfig(array (
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

$smarty->display('admin/setup/setup_smtp.tpl');
bmKill();
?>