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
		'EUC-JP',
		'ISO-2022-JP'
	);
	return in_array($value, $validCharsets);
}

SmartyValidate :: connect($smarty);

if (!SmartyValidate :: is_registered_form('mailings') || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	SmartyValidate::register_form('mailings', true);
	
	// register custom criteria
	SmartyValidate::register_criteria('isCharSet','check_charset', 'mailings');
	
	SmartyValidate :: register_validator('list_fromname', 'list_fromname', 'notEmpty', false, false, 'trim', 'mailings');
	SmartyValidate :: register_validator('list_fromemail', 'list_fromemail', 'isEmail', false, false, false, 'mailings');
	SmartyValidate :: register_validator('list_frombounce', 'list_frombounce', 'isEmail', false, false, false, 'mailings');
	SmartyValidate :: register_validator('list_charset', 'list_charset', 'isCharSet', false, false, 'trim', 'mailings');
	SmartyValidate :: register_validator('public_history','public_history:!^(on|off)$!','isRegExp', false, false, false, 'mailings');   
	SmartyValidate :: register_validator('demo_mode','demo_mode:!^(on|off)$!','isRegExp', false, false, false, 'mailings');   
	SmartyValidate :: register_validator('list_fromname', 'list_fromname', 'notEmpty', false, false, 'trim', 'mailings');
	SmartyValidate :: register_validator('maxRuntime', 'maxRuntime', 'isInt', false, false, 'trim', 'mailings');
	
	$vMsg = array();
	$vMsg['maxRuntime'] = Pommo::_T('Enter a number.');
	$vMsg['list_fromname'] = Pommo::_T('Cannot be empty.');
	$vMsg['list_fromemail'] = $vMsg['list_frombounce'] = Pommo::_T('Invalid email address');
	$smarty->assign('vMsg', $vMsg);
	
	// populate _POST with info from database (fills in form values...)
	$dbVals = PommoAPI::configGet(array (
		'list_fromname',
		'list_fromemail',
		'list_frombounce',
		'list_charset',
		'public_history',
		'maxRuntime'
	));
	$dbVals['demo_mode'] = (!empty ($pommo->_config['demo_mode']) && ($pommo->_config['demo_mode'] == "on")) ? 'on' : 'off';
	$smarty->assign($dbVals);
} else {
	// ___ USER HAS SENT FORM ___
	
	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	Pommo::requireOnce($pommo->_baseDir.'inc/classes/json.php');
	$json = new PommoJSON();
	
	if (SmartyValidate :: is_valid($_POST, 'mailings')) {
		// __ FORM IS VALID

		PommoAPI::configUpdate($_POST);
		$pommo->reloadConfig();
		
		$json->success(Pommo::_T('Configuration Updated.'));
	}
	else {
			// __ FORM NOT VALID
		
		$json->add('fieldErrors',$smarty->getInvalidFields('mailings'));
		$json->fail(Pommo::_T('Please review and correct errors with your submission.'));
	}
	
}
$smarty->assign($_POST);
$smarty->display('admin/setup/config/mailings.tpl');
Pommo::kill();