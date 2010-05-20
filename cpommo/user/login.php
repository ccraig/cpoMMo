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
require('../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/pending.php');

$pommo->init(array('authLevel' => 0, 'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

session_start(); // required by smartyValidate. TODO -> move to prepareForForm() ??

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('title', $pommo->_config['site_name'] . ' - ' . Pommo::_T('subscriber logon'));

$smarty->prepareForForm();

if (!SmartyValidate :: is_registered_form() || empty($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	SmartyValidate :: connect($smarty, true);
	SmartyValidate :: register_validator('email', 'Email', 'isEmail', false, false, 'trim');

	$formError = array ();
	$formError['email'] = Pommo::_T('Invalid email address');
	$smarty->assign('formError', $formError);
	
	// Assign email to form if pre-provided
	if (isset($_REQUEST['Email']))
		$smarty->assign('Email',$_REQUEST['Email']);
	elseif (isset($_REQUEST['email']))
		$smarty->assign('Email',$_REQUEST['email']);
		
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);
	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID __
		if (PommoHelper::isDupe($_POST['Email'])) {
			if (PommoPending::isEmailPending($_POST['Email'])) {
				$input = urlencode(serialize(array('Email' => $_POST['Email'])));
				SmartyValidate :: disconnect();
				Pommo::redirect('pending.php?input='.$input);
			}
			else {
				// __ EMAIL IN SUBSCRIBERS TABLE, REDIRECT
				SmartyValidate :: disconnect();
				Pommo::redirect('activate.php?email='.$_POST['Email']);
			}
		}
		else {
			// __ REPORT STATUS
			$logger->addMsg(Pommo::_T('Email address not found! Please try again.'));
			$logger->addMsg(sprintf(Pommo::_T('To subscribe, %sclick here%s'),'<a href="'.$pommo->_baseUrl.'user/subscribe.php?Email='.$_POST['Email'].'">','</a>'));
		}
	}
	$smarty->assign($_POST);
}
$smarty->display('user/login.tpl');
Pommo::kill();
?>