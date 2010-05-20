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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

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
	'sort' => 'email',
	'order' => 'ASC',
	'status' => 1,
	'group' => 'all',
	'info' => 'hide'),
	$_REQUEST);

if(in_array($state['sort'], array('ip','time_registered', 'time_touched', 'status')))
	$state['info'] = 'show';

// get the group
$group = new PommoGroup($state['group'], $state['status']);

// fireup Monte's pager
$smarty->addPager($state['limit'], $group->_tally);
$start = SmartyPaginate::getCurrentIndex();
SmartyPaginate::assign($smarty);


// get the subscribers details
$subscribers = $group->members(array(
	'sort' => $state['sort'],
	'order' => $state['order'],
	'limit' => $state['limit'],
	'offset' => $start));
	

$smarty->assign('state',$state);
$smarty->assign('subscribers',$subscribers);
$smarty->assign('tally',$group->_tally);
$smarty->assign('groups',PommoGroup::get());
$smarty->assign('fields',PommoField::get());

$smarty->display('admin/subscribers/subscribers_manage.tpl');
Pommo::kill();
?>