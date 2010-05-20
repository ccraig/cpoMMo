<?php
/**
 * Copyright (C) 2005, 2006, 2007  Brice Burgess <bhb@iceburg.net>
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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();


$gid = $_POST['groupID'];
$fid = $_POST['fieldID'];
$type = $_POST['ruleType'];

$andOr = (isset($_POST['andOr'])) ? $_POST['andOr'] : 'and';

$logic = (isset($_POST['logic']) && $_POST['logic'] != "0") ? $_POST['logic'] : false;

if(!is_numeric($gid) || !is_numeric($fid) || ($type != 'group' && $type != 'field') || ($andOr != 'or' && $andOr != 'and'))
	die('invalid input');


// current group
$group = current(PommoGroup::get(array('id' => $gid)));


if ($type == 'group') {
	$match = PommoGroup::getNames($fid);
	$key = key($match);
	
	$smarty->assign('match_name',$match[$key]);
	$smarty->assign('match_id',$key);
	
	$smarty->display('admin/subscribers/ajax/group_edit.tpl');
	Pommo::kill();
}
elseif ($type == 'field') {
	Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
	Pommo::requireOnce($pommo->_baseDir.'inc/helpers/rules.php');
	
	// check to see if we're editing
	
	$values = array();
	if ($logic) { // logic is passed *only* when edit button is clicked..
		foreach($group['rules'] as $rule) {
			if($rule['logic'] == $logic && $rule['field_id'] == $fid)
				$values[] = $rule['value'];
		}
	}
	$firstVal = (empty($values)) ? false : array_shift($values);
	$smarty->assign('values',$values);
	$smarty->assign('firstVal',$firstVal);
	
	$field = current(PommoField::get(array('id' => $fid)));
	
	if ($logic) {
		$logic = array($logic => PommoRules::getEnglish($logic));
	}
	else {
		$logic = array();
		$f = array($field);
		foreach(PommoRules::getLegal($group, $f) as $logics)				
			foreach ($logics as $l)
				$logic[$l] = PommoRules::getEnglish($l);
	}
	
	$smarty->assign('field',$field);
	$smarty->assign('logic',$logic);
	

	$smarty->display('admin/subscribers/ajax/group_field.tpl');
	Pommo::kill();
	
}
die();
?>