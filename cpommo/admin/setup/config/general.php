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

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

SmartyValidate :: connect($smarty);

if (!SmartyValidate :: is_registered_form('general') || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	SmartyValidate::register_form('general', true);
	

	SmartyValidate :: register_validator('list_name', 'list_name', 'notEmpty', false, false, 'trim', 'general');
	SmartyValidate :: register_validator('site_name', 'site_name', 'notEmpty', false, false, 'trim', 'general');
	SmartyValidate :: register_validator('site_url', 'site_url', 'isURL', false, false, 'trim', 'general');
	
	SmartyValidate :: register_validator('site_success', 'site_success', 'isURL', TRUE, false, false, 'general');
	SmartyValidate :: register_validator('site_confirm', 'site_confirm', 'isURL', TRUE, false, false, 'general');
	
	SmartyValidate :: register_validator('list_confirm','list_confirm:!^(on|off)$!','isRegExp', false, false, false, 'general');   
	SmartyValidate :: register_validator('list_exchanger','list_exchanger:!^(sendmail|mail|smtp)$!','isRegExp', false, false, false, 'general');   
	
	
	// no validation for exchanger
	$vMsg = array();
	$vMsg['site_url'] = $vMsg['site_success'] = $vMsg['site_confirm'] = Pommo::_T('Must be a valid URL');
	$vMsg['list_name'] = $vMsg['site_name'] = Pommo::_T('Cannot be empty.');
	$smarty->assign('vMsg', $vMsg);
	
	// populate _POST with info from database (fills in form values...)
	$dbVals = PommoAPI::configGet(array (
		'site_success',
		'site_confirm',
		'list_exchanger',
		'list_confirm'
	));
	$dbVals['site_url'] = $pommo->_config['site_url'];
	$dbVals['site_name'] = $pommo->_config['site_name'];
	$dbVals['list_name'] = $pommo->_config['list_name'];
	
	$smarty->assign($dbVals);
} else {
	// ___ USER HAS SENT FORM ___
	
	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	Pommo::requireOnce($pommo->_baseDir.'inc/classes/json.php');
	$json = new PommoJSON();
	
	if (SmartyValidate :: is_valid($_POST, 'general')) {
		// __ FORM IS VALID

		PommoAPI::configUpdate($_POST);
		$pommo->reloadConfig();

		$json->success(Pommo::_T('Configuration Updated.'));
	}
	else {
		// __ FORM NOT VALID
		
		$json->add('fieldErrors',$smarty->getInvalidFields('general'));
		$json->fail(Pommo::_T('Please review and correct errors with your submission.'));
	}
	
}
$smarty->assign($_POST);
$smarty->display('admin/setup/config/general.tpl');
Pommo::kill();
			