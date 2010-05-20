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

require ('bootstrap.php');

$poMMo = & fireup();
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();

if (isset ($_GET['logout'])) {
	// if user chose to logout, destroy session and redirect to this page
	$poMMo->setAuthenticated(FALSE);
	session_destroy();
	header('Location: ' . bm_http . bm_baseUrl . 'index.php');
}
elseif ($poMMo->isAuthenticated()) {
	// If user is authenticated (has logged in), redirect to admin.php
	bmRedirect(bm_http . bm_baseUrl . 'admin/admin.php');
}
elseif (!empty ($_POST['username']) || !empty ($_POST['password'])) {
	// Check if user submitted correct username & password. If so, Authenticate.
	$auth = $poMMo->getConfig(array (
		'admin_username',
		'admin_password'
	));
	if ($_POST['username'] == $auth['admin_username'] && md5($_POST['password']) == $auth['admin_password']) {
		
		// LOGIN SUCCESS -- PERFORM MAINTENANCE, SET AUTH, REDIRECT TO REFERER
		bmMaintenance();
		
		$poMMo->setAuthenticated(TRUE);
		bmRedirect(bm_http . $_POST['referer']);
	}
	else {
		$logger->addMsg(_T('Failed login attempt. Try again.'));
	}
}
elseif (!empty ($_POST['resetPassword'])) {
	// Check if a reset password request has been received

	// check that captcha matched
	if (!isset($_POST['captcha'])) {
		// generate captcha
		$captcha = substr(md5(rand()), 0, 4);

		$smarty->assign('captcha', $captcha);
	}
	elseif ($_POST['captcha'] == $_POST['realdeal']) {
		// user inputted captcha matched. Reset password

		require_once (bm_baseDir . '/inc/db_subscribers.php');
		require_once (bm_baseDir . '/inc/lib.mailings.php');

		// see if there is already a pending request for the administrator
		if (isDupeEmail($dbo, $poMMo->_config['admin_email'], 'pending')) {
			$poMMo->set(array (
				'email' => $poMMo->_config['admin_email']
			));
			bmRedirect(bm_http . bm_baseUrl . 'user/user_pending.php');
		}

		// create a password change request, send confirmation mail
		$code = dbPendingAdd($dbo, "password", $poMMo->_config['admin_email']);
		if (!empty ($code)) {
			bmSendConfirmation($poMMo->_config['admin_email'], $code, "password");
		}

		$logger->addMsg(_T('Password reset request recieved. Check your email.'));
		$smarty->assign('captcha',FALSE);
		
	} else {
		// captcha did not match
		$logger->addMsg(_T('Captcha did not match. Try again.'));
	}
}

// referer (used to return user to requested page upon login success)
$smarty->assign('referer',(isset($_REQUEST['referer']) ? $_REQUEST['referer'] : bm_baseUrl . 'admin/admin.php'));

$smarty->display('index.tpl');
die();
?>