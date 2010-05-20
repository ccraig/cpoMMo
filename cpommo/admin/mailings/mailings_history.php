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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('returnStr', Pommo::_T('Mailings Page'));


/** SET PAGE STATE
 * limit	- # of mailings per page
 * sort		- Sorting of Mailings [subject, mailgroup, subscriberCount, started, etc.]
 * order	- Order Type (ascending - ASC /descending - DESC)
 */
// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('mailings_history',array(
	'limit' => 10,
	'sort' => 'end',
	'order' => 'desc',
	'page' => 1),
	$_REQUEST);
	
/**********************************
	VALIDATION ROUTINES
*********************************/
	
if(!is_numeric($state['limit']) || $state['limit'] < 1 || $state['limit'] > 1000)
	$state['limit'] = 10;
	
if($state['order'] != 'asc' && $state['order'] != 'desc')
	$state['order'] = 'asc';
	
if($state['sort'] != 'start' &&
	$state['sort'] != 'end' &&
	$state['sort'] != 'subject' &&
	$state['sort'] != 'sent' &&
	$state['sort'] != 'status' &&
	$state['sort'] != 'group')
		$state['sort'] = 'end';
		
		
/**********************************
	DISPLAY METHODS
*********************************/

// Calculate and Remember number of pages
$tally = PommoMailing::tally();
$state['pages'] = (is_numeric($tally) && $tally > 0) ?
	ceil($tally/$state['limit']) :
	0;
	
$smarty->assign('state',$state);
$smarty->assign('tally',$tally);
$smarty->assign('mailings', $mailings);

$smarty->display('admin/mailings/mailings_history.tpl');
Pommo::kill();
?>