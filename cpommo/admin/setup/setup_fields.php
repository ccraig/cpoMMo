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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

// add field if requested, redirect to its edit page on success
if (!empty ($_POST['field_name'])) {
	$field = PommoField::make(array(
		'name' => $_POST['field_name'],
		'type' => $_POST['field_type'],
		'prompt' => 'Field Prompt',
		'required' => 'off',
		'active' => 'off'
	));
	
	$id = PommoField::add($field);
	if ($id)
		$smarty->assign('added',$id);
	else
		$logger->addMsg(Pommo::_T('Error with addition.'));
}

// check for a deletion request
if (!empty ($_GET['delete'])) {

	$field = PommoField::get(array('id' => $_GET['field_id']));
	$field =& current($field);
	
	if (count($field) === 0) {
		$logger->addMsg(Pommo::_T('Error with deletion.'));
	}
	else {
		$affected = PommoField::subscribersAffected($field['id']);
		if(count($affected) > 0 && empty($_GET['dVal-force'])) {
			$smarty->assign('confirm', array (
				'title' => Pommo::_T('Confirm Action'),
				'nourl' => $_SERVER['PHP_SELF'] . '?field_id=' . $_GET['field_id'],
				'yesurl' => $_SERVER['PHP_SELF'] . '?field_id=' . $_GET['field_id'] . '&delete=TRUE&dVal-force=TRUE',
				'msg' => sprintf(Pommo::_T('Currently, %1$s subscribers have a non empty value for %2$s. All Subscriber data relating to this field will be lost.'), '<b>' . count($affected) . '</b>','<b>' . $field['name'] . '</b>')));
			$smarty->display('admin/confirm.tpl');
			Pommo::kill();
		}
		else {
			(PommoField::delete($field['id'])) ?
				Pommo::redirect($_SERVER['PHP_SELF']) :
				$logger->addMsg(Pommo::_T('Error with deletion.'));
		}
	}
}

// Get array of fields. Key is ID, value is an array of the demo's info
$fields = PommoField::get(array('byName' => FALSE));
if (!empty($fields))
	$smarty->assign('fields', $fields);
	
$smarty->display('admin/setup/setup_fields.tpl');
Pommo::kill();
?>