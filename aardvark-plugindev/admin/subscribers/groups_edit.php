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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/sql.gen.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/rules.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('returnStr', Pommo::_T('Groups Page'));


$groups = & PommoGroup::get();
$fields = & PommoField::get();

$group =& $groups[$_REQUEST['group_id']];

if(empty($group))
	Pommo::redirect('subscribers_groups.php');
	
$rules = PommoSQL::sortRules($group['rules']);
$rules['and'] = PommoSQL::sortLogic($rules['and']);
$rules['or'] = PommoSQL::sortLogic($rules['or']);
	
// change group name if requested
if (isset($_POST['rename']) && !empty ($_POST['group_name']))
	if (PommoGroup::nameChange($group['id'], $_POST['group_name']))
		Pommo::redirect($_SERVER['PHP_SELF'].'?group_id='.$group['id'].'&renamed='.$_POST['group_name']);
if (isset($_GET['renamed']))
	$logger->addMsg(Pommo::_T('Group Renamed'));

if(isset($_GET['delete'])) {
	PommoRules::deleteRule($group['id'], $_GET['delete'], $_GET['logic']);
	Pommo::redirect($_SERVER['PHP_SELF'].'?group_id='.$group['id']);
}

if(isset($_GET['toggle'])) { 
	if($_GET['type'] == 'or' && count($rules['and']) < 2)
		$logger->addMsg(Pommo::_T('At least 1 "and" rule must exist before an "or" rule takes effect.'));
	else {
		PommoRules::changeType($group['id'], $_GET['toggle'], $_GET['logic'], $_GET['type']);
		Pommo::redirect($_SERVER['PHP_SELF'].'?group_id='.$group['id']);
	}
}

	
$new = & PommoRules::getLegal($group, $fields);
$gnew = & PommoRules::getLegalGroups($group, $groups);


// convert the rules array ID's + logics to user readable values
//	$rules[rule_id] = array (
	//		'field_id' => $row['field_id'],
  	//		'logic' => $row['logic'],
	//		'value' => $row['value'],
	//	);
	
// 	// A "logic array" resembles:
	//  $logic[field_id] = array(
	//		[logic] => array(values)
	//		is_in => array(1,2)
	//	);
	



foreach($rules as $key => $a) {
	if ($key == 'include' || $key == 'exclude')
		foreach($a as $k => $gid)
			$rules[$key][$k] = $groups[$gid]['name'];
}

$smarty->assign('group',$group);
$smarty->assign('fields',$fields);
$smarty->assign('logicNames',PommoRules::getEnglish());
$smarty->assign('new', $new);
$smarty->assign('gnew', $gnew);
$smarty->assign('rules', $rules);
$smarty->assign('tally', PommoGroup::tally($group));
$smarty->assign('ruleCount', count($rules['and'])+count($rules['or'])+count($rules['include'])+count($rules['exclude']));

$smarty->assign('getURL',$_SERVER['PHP_SELF'].'?group_id='.$group['id']);
$smarty->assign('t_include',Pommo::_T('INCLUDE'));

$smarty->display('admin/subscribers/groups_edit.tpl');
Pommo::kill();

?>