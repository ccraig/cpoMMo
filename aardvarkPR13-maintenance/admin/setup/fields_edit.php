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
require_once (bm_baseDir . '/inc/db_fields.php');
require_once (bm_baseDir.'/inc/lib.txt.php');

$poMMo = & fireup('secure','keep');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();

if (isset ($_REQUEST['field_id']) && dbFieldCheck($dbo, $_REQUEST['field_id']))
	$field_id = str2db($_REQUEST['field_id']);
else {
	bmRedirect('setup_fields.php');
}

// check if user submitted options to add
if (!empty ($_POST['dVal-add']) && !empty ($_POST['addOption'])) {
	dbFieldOptionAdd($dbo, $field_id, $_POST['addOption']);
	$_POST = array();
}

// check if user requestedfield_id='.$field_id to remove an option
if (!empty ($_REQUEST['dVal-del']) && !empty ($_REQUEST['delOption'])) {
	
	// See if this change will affect any subscribers, if so, confirm the change.
	$sql = 'SELECT COUNT(data_id) FROM ' . $dbo->table['subscribers_data'] . ' WHERE field_id=\'' . $field_id . '\' AND value=\'' . str2db($_POST['delOption']) . '\'';
	$affected = $dbo->query($sql, 0);
	
	if ($affected && empty($_GET['dVal-force'])) {
		$smarty->assign('confirm',array(
		 	'title' => _T('Remove Option'),
		 	'nourl' =>  $_SERVER['PHP_SELF'].'?field_id='.$field_id,
		 	'yesurl' => $_SERVER['PHP_SELF'].'?field_id='.$field_id.'&dVal-del=TRUE&dVal-force=TRUE&delOption='.$_POST['delOption'],
		 	'msg' => sprintf(_T('Deleting option %1$s will affect %2$s subscribers who have selected this choice. They will be flagged as needing to update their records.'), '<b>'.$_POST['delOption'].'</b>', '<em>'.$affected.'</em>')
		 	));
		 
		 $smarty->display('admin/confirm.tpl');
		 bmKill();
	}
	else {
		// delete option, no subscriber is affected || force given.
		dbFieldOptionDelete($dbo, $field_id, $_REQUEST['delOption']);
		bmRedirect($_SERVER['PHP_SELF'].'?field_id='.$field_id);
	}
}


if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);
	SmartyValidate :: register_validator('field_name', 'field_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('field_prompt', 'field_prompt', 'notEmpty', false, false, 'trim');

	$formError = array ();
	$formError['field_name'] = $formError['field_prompt'] = _T('Cannot be empty.');
	$smarty->assign('formError', $formError);
	
	// fetch field info
	$fields = & dbGetFields($dbo, $field_id);
	$field = & $fields[$field_id];
	$field['id'] = $field_id;
	$smarty->assign('field', $field);
	$poMMo->set($field);

	// populate _POST with info from database (fills in form values...)
	@ $_POST['field_name'] = $field['name'];
	@ $_POST['field_prompt'] = $field['prompt'];
	@ $_POST['field_active'] = $field['active'];
	@ $_POST['field_required'] = $field['required'];
	@ $_POST['field_normally'] = $field['normally'];

} else {
	
	$field =& $poMMo->get();
	$smarty->assign('field', $field);
	
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID

		dbFieldUpdate($dbo, $_POST);
		$logger->addMsg(_T('Settings updated.'));

	} else {
		// __ FORM NOT VALID
		$logger->addMsg(_T('Please review and correct errors with your submission.'));
	}
}

switch ($field['type']) {
		case 'text' :
			$smarty->assign('intro', _T('This is a <b>TEXT</b> based field. Subscribers will be allowed to type in any value in for this field. Text fields are useful for collecting names, cities, and such.'));
			break;
		case 'checkbox' :
			$smarty->assign('intro', _T('This is a <b>CHECKBOX</b> based field. Subscribers will be allowed to toggle this field ON and OFF. Checkboxes are useful for asking a user if they\'d like to be included or excluded in something.'));
			break;
		case 'number' :
			$smarty->assign('intro', _T('This is a <b>NUMBER</b> based field -- <b>UNSUPPORTED</b>. Support for this type will be added later.'));
			break;
		case 'date' :
			$smarty->assign('intro', _T('This is a <b>DATE</b> based field -- <b>UNSUPPORTED</b>. Support for this type will be added later.'));
			break;
		case 'multiple' :
			$smarty->assign('intro', _T('This is a <b>MULTIPLE CHOICE</b> based field. Subscribers will be able to select a value from the options you provide below. Multiple choice fields have reliable values, and are useful for collecting subsscriber Country, income range, and such.'));
			break;
	}
	
$smarty->assign($_POST);
$smarty->display('admin/setup/fields_edit.tpl');
bmKill();