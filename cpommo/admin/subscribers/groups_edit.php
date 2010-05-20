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


// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('groups_edit',array(
	'group' => 0),
	$_REQUEST);
	

$groups = & PommoGroup::get();
$fields = & PommoField::get();

$group =& $groups[$state['group']];

if(empty($group))
	Pommo::redirect('subscribers_groups.php');
	
$rules = PommoSQL::sortRules($group['rules']);
$rules['and'] = PommoSQL::sortLogic($rules['and']);
$rules['or'] = PommoSQL::sortLogic($rules['or']);

foreach($rules as $key => $a) {
	if ($key == 'include' || $key == 'exclude')
		foreach($a as $k => $gid)
			$rules[$key][$k] = $groups[$gid]['name'];
}


$smarty->assign('fields',$fields);

$smarty->assign('legalFieldIDs', PommoRules::getLegal($group, $fields));
$smarty->assign('legalGroups', PommoRules::getLegalGroups($group, $groups));



$smarty->assign('group',$group);

$smarty->assign('logicNames',PommoRules::getEnglish());



$smarty->assign('rules', $rules);
$smarty->assign('tally', PommoGroup::tally($group));
$smarty->assign('ruleCount', count($rules['and'])+count($rules['or'])+count($rules['include'])+count($rules['exclude']));

$smarty->assign('getURL',$_SERVER['PHP_SELF'].'?group_id='.$group['id']);
$smarty->assign('t_include',Pommo::_T('INCLUDE'));

$smarty->display('admin/subscribers/groups_edit.tpl');
Pommo::kill();

?>