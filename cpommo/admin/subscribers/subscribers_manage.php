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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;
	
/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('returnStr', Pommo::_T('Subscribers Page'));


/** SET PAGE STATE
 * limit	- The Maximum # of subscribers to show per page
 * sort		- The subscriber field to sort by (email, ip, time_registered, time_touched, status, or field_id)
 * order	- Order Type (ascending - ASC /descending - DESC)
 * info		- (hide/show) Time Registered/Updated, IP address
 * 
 * status	- Filter by subscriber status (active, inactive, pending, all)
 * group	- Filter by group members (groupID or 'all')
 */

// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('subscribers_manage',array(
	'limit' => 150,
	'sort' => $pommo->_default_subscriber_sort,
	'order' => 'asc',
	'status' => 1,
	'group' => 'all',
	'page' => 1,
	'search' => false),
	$_REQUEST);


/**********************************
	VALIDATION ROUTINES
*********************************/
	
if(!is_numeric($state['limit']) || $state['limit'] < 1 || $state['limit'] > 1000)
	$state['limit'] = 150;
	
if($state['order'] != 'asc' && $state['order'] != 'desc')
	$state['order'] = 'asc';
	
if(!is_numeric($state['sort']) &&
	$state['sort'] != 'email' &&
	$state['sort'] != 'ip' &&
	$state['sort'] != 'time_registered' &&
	$state['sort'] != 'time_touched')
		$state['sort'] = 'email';
		
if(!is_numeric($state['status']))
	$state['status'] = 1;
	
if(!is_numeric($state['group']) && $state['group'] != 'all')
	$state['group'] = 'all';

if(isset($_REQUEST['searchClear']))
	$state['search'] = false;
elseif(isset($_REQUEST['searchField']) && (
	is_numeric($_REQUEST['searchField']) ||
 	$_REQUEST['searchField'] == 'email' ||
	$_REQUEST['searchField'] == 'ip' ||
	$_REQUEST['searchField'] == 'time_registered' ||
	$_REQUEST['searchField'] == 'time_touched')) 
	{
	$_REQUEST['searchString'] = trim($_REQUEST['searchString']);
	$state['search'] = (empty($_REQUEST['searchString'])) ?
		false :
		array(
		'field' => $_REQUEST['searchField'],
		'string' => trim($_REQUEST['searchString'])
		);
	}


/**********************************
	DISPLAY METHODS
*********************************/

// Get the *empty* group [no member IDs. 3rd arg is set TRUE]
$group = new PommoGroup($state['group'], $state['status'], $state['search']);

// Calculate and Remember number of pages for this group/status combo
$state['pages'] = (is_numeric($group->_tally) && $group->_tally > 0) ?
	ceil($group->_tally/$state['limit']) :
	0;
	
$smarty->assign('state',$state);
$smarty->assign('tally',$group->_tally);
$smarty->assign('groups',PommoGroup::get());
$smarty->assign('fields',PommoField::get());

$smarty->display('admin/subscribers/subscribers_manage.tpl');
Pommo::kill();
?>