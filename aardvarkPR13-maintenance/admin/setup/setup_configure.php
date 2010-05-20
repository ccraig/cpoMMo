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

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();

// ADD CUSTOM VALIDATOR FOR CHARSET

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

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);

	// register custom criteria
	SmartyValidate::register_criteria('isCharSet','check_charset');

	SmartyValidate :: register_validator('admin_username', 'admin_username', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('site_name', 'site_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('list_name', 'list_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('list_fromname', 'list_fromname', 'notEmpty', false, false, 'trim');

	SmartyValidate :: register_validator('site_url', 'site_url', 'isURL', false, false, 'trim');
	SmartyValidate :: register_validator('site_success', 'site_success', 'isURL', TRUE);
	SmartyValidate :: register_validator('site_confirm', 'site_confirm', 'isURL', TRUE);

	SmartyValidate :: register_validator('admin_password2', 'admin_password:admin_password2', 'isEqual', TRUE);

	SmartyValidate :: register_validator('admin_email', 'admin_email', 'isEmail');
	SmartyValidate :: register_validator('list_fromemail', 'list_fromemail', 'isEmail');
	SmartyValidate :: register_validator('list_frombounce', 'list_frombounce', 'isEmail');

	SmartyValidate :: register_validator('list_charset', 'list_charset', 'isCharSet', false, false, 'trim');


	$formError = array ();
	$formError['admin_username'] = $formError['sitename'] = $formError['list_name'] = $formError['list_fromname'] = _T('Cannot be empty.');

	$formError['admin_email'] = $formError['list_fromemail'] = $formError['list_frombounce'] = _T('Invalid email address');

	$formError['admin_password2'] = _T('Passwords must match.');

	$formError['site_url'] = $formError['site_success'] = $formError['site_confirm'] = _T('Must be a valid URL');

	$formError['list_charset'] = _T('Invalid Character Set');

	$smarty->assign('formError', $formError);

	// populate _POST with info from database (fills in form values...)
	$dbVals = $poMMo->getConfig(array (
		'admin_username',
		'site_success',
		'site_confirm',
		'list_fromname',
		'list_fromemail',
		'list_frombounce',
		'list_exchanger',
		'list_confirm',
		'list_charset'
	));

	$dbVals['demo_mode'] = (!empty ($poMMo->_config['demo_mode']) && ($poMMo->_config['demo_mode'] == "on")) ? 'on' : 'off';

	$dbVals['site_url'] = $poMMo->_config['site_url'];
	$dbVals['site_name'] = $poMMo->_config['site_name'];
	$dbVals['admin_email'] = $poMMo->_config['admin_email'];
	$dbVals['list_name'] = $poMMo->_config['list_name'];

	$smarty->assign($dbVals);
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID

		// convert password to MD5 if given...
		if (!empty ($_POST['admin_password']))
			$_POST['admin_password'] = md5($_POST['admin_password']);

		$oldDemo = $poMMo->_config['demo_mode'];

		dbUpdateConfig($dbo, $_POST);

		$poMMo->loadConfig('TRUE');

		$logger->addMsg(_T('Configuration Updated.'));

		// refresh page to reflect demonstration mode changes
		if ($oldDemo != $poMMo->_config['demo_mode'])
			bmRedirect('setup_configure.php');

	} else {
		// __ FORM NOT VALID
		$logger->addMsg(_T('Please review and correct errors with your submission.'));
	}
}

$smarty->assign($_POST);
$smarty->display('admin/setup/setup_configure.tpl');
bmKill();
?>