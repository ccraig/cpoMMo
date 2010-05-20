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

require ('../bootstrap.php');
require_once (bm_baseDir . '/install/helper.install.php');

session_start(); // required by smartyValidate. TODO -> move to prepareForForm() ??

$poMMo = & fireup('install');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;
$dbo->dieOnQuery(FALSE);


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();

// clear the cache
$smarty->clear_compiled_tpl();
$smarty->clear_all_cache();

$smarty->prepareForForm();

// Check to make sure poMMo is not already installed.
if (bmIsInstalled()) {
	$logger->addErr(_T('poMMo appears to already by installed. If you would like to clear all data and re-install poMMo, delete your database.'));
	$smarty->assign('installed', TRUE);
	$smarty->display('install.tpl');
	bmKill();
}

if (isset ($_REQUEST['disableDebug']))
	unset ($_REQUEST['debugInstall']);
elseif (isset ($_REQUEST['debugInstall'])) $smarty->assign('debug', TRUE);

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	SmartyValidate :: connect($smarty, true);
	
	SmartyValidate :: register_validator('list_name', 'list_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('site_name', 'site_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('site_url', 'site_url', 'isURL');
	SmartyValidate :: register_validator('admin_password', 'admin_password', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('admin_password2', 'admin_password:admin_password2', 'isEqual');
	SmartyValidate :: register_validator('admin_email', 'admin_email', 'isEmail');

	$formError = array ();
	$formError['list_name'] = $formError['site_name'] = $formError['admin_password'] = _T('Cannot be empty.');
	$formError['admin_password2'] = _T('Passwords must match.');
	$formError['site_url'] = _T('Must be a valid URL');
	$formError['admin_email'] = _T('Must be a valid email');
	$smarty->assign('formError', $formError);
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID
		if (isset ($_POST['installerooni'])) {

			// drop existing poMMo tables
			foreach (array_keys($dbo->table) as $key) {
				$table = $dbo->table[$key];
				$sql = 'DROP TABLE IF EXISTS ' . $table;
				$dbo->query($sql);
			}
			
			if (isset ($_REQUEST['debugInstall']))
				$dbo->debug(TRUE);

			// install poMMo
			require_once (bm_baseDir . '/inc/db_procedures.php');
			$install = parse_mysql_dump();

			if ($install) {
				// installation of DB went OK, set configuration values to user supplied ones

				$pass = $_POST['admin_password'];

				// install configuration
				$_POST['admin_password'] = md5($_POST['admin_password']);
				dbUpdateConfig($dbo, $_POST);

				// load configuration, set message defaults.
				$poMMo->loadConfig('TRUE');
				dbResetMessageDefaults('all');

				$logger->addMsg(_T('Installation Complete! You may now login and setup poMMo.'));
				$logger->addMsg(_T('Login Username: ') . 'admin');
				$logger->addMsg(_T('Login Password: ') . $pass);

				$smarty->assign('installed', TRUE);
			} else {
				// INSTALL FAILED

				$dbo->debug(FALSE);

				// drop existing poMMo tables
				foreach (array_keys($dbo->table) as $key) {
					$table = $dbo->table[$key];
					$sql = 'DROP TABLE IF EXISTS ' . $table;
					$dbo->query($sql);
				}

				$logger->addErr('Installation failed! Enable debbuging to expose the problem.');
			}
		}
	} else {
		// __ FORM NOT VALID
		$logger->addMsg(_T('Please review and correct errors with your submission.'));
	}
}
$smarty->assign($_POST);
$smarty->display('install.tpl');
bmKill();
?>