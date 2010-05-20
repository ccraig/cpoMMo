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

$poMMo = & fireup('install');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;
$dbo->dieOnQuery(FALSE);

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();

// clear the cache  TODO -> maybe not necessatry to clear ALL ?
$smarty->clear_compiled_tpl();
$smarty->clear_all_cache();

$smarty->prepareForForm();

$poMMo->loadConfig('TRUE');

// Check to make sure poMMo is not already installed.
if ($poMMo->_config['revision'] == pommo_revision && !isset ($_REQUEST['forceUpgrade']) && !isset ($_REQUEST['continue'])) {
	$logger->addErr(sprintf(_T('poMMo appears to be up to date. If you want to force an upgrade, %s click here %s'), '<a href="' . $_SERVER['PHP_SELF'] . '?forceUpgrade=TRUE">', '</a>'));
	$smarty->display('upgrade.tpl');
	bmKill();
}

require(bm_baseDir . '/install/helper.upgrade.php');

if (isset ($_REQUEST['disableDebug']))
	unset ($_REQUEST['debugInstall']);
elseif (isset ($_REQUEST['debugInstall'])) $smarty->assign('debug', TRUE);

if (empty($_REQUEST['continue'])) {
	if (!bmIsInstalled())
		$logger->addErr(sprintf(_T('poMMo does not appear to be installed! Please %s INSTALL %s before attempting an upgrade.'), '<a href="' . bm_baseUrl . 'install/install.php">', '</a>'));
	else
		$logger->addErr(sprintf(_T('To upgrade poMMo, %s click here %s'), '<a href="' . bm_baseUrl . 'install/upgrade.php?continue=TRUE">', '</a>'));
} else {
	$smarty->assign('attempt', TRUE);

	if (isset ($_REQUEST['debugInstall']))
		$dbo->debug(TRUE);

	$dbo->dieOnQuery(FALSE);
	if (bmUpgrade($dbo)) {
		$logger->addErr(_T('Upgrade Complete!'));

		// Read in RELEASE Notes -- TODO -> use file_get_contents() one day when everyone has PHP 4.3
		$filename = bm_baseDir . '/docs/RELEASE';
		$handle = fopen($filename, "r");
		$x = fread($handle, filesize($filename));
		fclose($handle);

		$smarty->assign('notes', $x);
		$smarty->assign('upgraded', TRUE);
	} else {
		$logger->addErr(_T('Upgrade Failed!'));
	}
}

$smarty->display('upgrade.tpl');
bmKill();
?>
