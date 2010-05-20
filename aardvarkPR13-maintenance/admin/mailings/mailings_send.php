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
require_once (bm_baseDir . '/inc/db_groups.php');
require_once (bm_baseDir . '/inc/db_mailing.php');

$poMMo = & fireup('secure', 'keep');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();

// SmartyValidate Custom Validation Function
function check_charset($value, $empty, & $params, & $formvars) {
	$validCharsets = array (
		'UTF-8',
		'ISO-8859-1',
		'ISO-8859-2',
		'ISO-8859-7',
		'ISO-8859-15',
		'cp1251',
		'KOI8-R',
		'GB2312',
		'EUC-JP'
	);

	return in_array($value, $validCharsets);
}

// check to see if a mailing is taking place (queue not empty)
if (!mailingQueueEmpty($dbo)) {
	bmKill(sprintf(_T('A mailing is already taking place. Please allow it to finish before creating another. Return to the %s Mailing Page %s'), '<a href="admin_mailings.php">', '</a>'));
}

// get groups for select -- key == ID, val == group name
$groups = dbGetGroups($dbo);
$smarty->assign('groups', $groups);

if ($poMMo->_config['demo_mode'] == 'on')
	$logger->addMsg(_T('Demonstration Mode is on. No Emails will be sent.'));

// Get MailingData from SESSION.
$mailingData = $poMMo->get('mailingData');
if (!$mailingData) {
	$mailingData = array ();
}

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);

	// register custom criteria
	SmartyValidate :: register_criteria('isCharSet', 'check_charset');

	SmartyValidate :: register_validator('fromname', 'fromname', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('subject', 'subject', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('fromemail', 'fromemail', 'isEmail', false, false, 'trim');
	SmartyValidate :: register_validator('frombounce', 'frombounce', 'isEmail', false, false, 'trim');
	SmartyValidate :: register_validator('ishtml', 'ishtml:/(html|plain)/i', 'isRegExp', false, false, 'trim');
	SmartyValidate :: register_validator('mailgroup', 'mailgroup:/(all|\d+)/i', 'isRegExp', false, false, 'trim');

	SmartyValidate :: register_validator('charset', 'charset', 'isCharSet', false, false, 'trim');

	$formError = array ();
	$formError['fromname'] = $formError['subject'] = _T('Cannot be empty.');
	$formError['charset'] = _T('Invalid Character Set');
	$formError['fromemail'] = $formError['frombounce'] = _T('Invalid email address');
	$formError['ishtml'] = $formError['mailgroup'] = _T('Invalid Input');

	$smarty->assign('formError', $formError);

	if (!empty ($mailingData)) {
		// assign mailingData to POST
		$_POST['fromname'] = $mailingData['fromname'];
		$_POST['fromemail'] = $mailingData['fromemail'];
		$_POST['frombounce'] = $mailingData['frombounce'];
		$_POST['subject'] = $mailingData['subject'];
		$_POST['ishtml'] = ($mailingData['ishtml'] == 'on' || $mailingData['ishtml'] == 'html') ? 'html' : 'plain';
		$_POST['charset'] = $mailingData['charset'];
		$_POST['mailgroup'] = $mailingData['mailgroup'];
	} else { // mailingData Empty. Load default values from DB
		$dbvalues = $poMMo->getConfig(array (
			'list_fromname',
			'list_fromemail',
			'list_frombounce',
			'list_charset'
		));
		if (!isset ($_POST['fromname']))
			$_POST['fromname'] = $dbvalues['list_fromname'];
		if (!isset ($_POST['fromemail']))
			$_POST['fromemail'] = $dbvalues['list_fromemail'];
		if (!isset ($_POST['frombounce']))
			$_POST['frombounce'] = $dbvalues['list_frombounce'];
		if (!isset ($_POST['charset']))
			$_POST['charset'] = $dbvalues['list_charset'];
	}
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID

		SmartyValidate :: disconnect();

		// Save inputted data to $MailingData[] (gets stored in Session)
		$mailingData['fromname'] = $_POST['fromname'];
		$mailingData['fromemail'] = $_POST['fromemail'];
		$mailingData['frombounce'] = $_POST['frombounce'];
		$mailingData['subject'] = $_POST['subject'];
		$mailingData['ishtml'] = $_POST['ishtml'];
		$mailingData['charset'] = $_POST['charset'];
		$mailingData['mailgroup'] = $_POST['mailgroup'];
		$poMMo->set(array('mailingData' => $mailingData));

		if (!empty ($mailingData['body']))
			bmRedirect('mailings_send3.php');
		else
			bmRedirect('mailings_send2.php');
	} else {
		// __ FORM NOT VALID
		$logger->addMsg(_T('Please review and correct errors with your submission.'));
	}
}

$smarty->assign($_POST);
$smarty->display('admin/mailings/mailings_send.tpl');
bmKill();
?>
