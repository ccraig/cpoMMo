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
require ('../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$config = PommoAPI::configGet('public_history');
if($config['public_history'] == 'on') {
	$pommo->init(array('authLevel' => 0));
} else {
	Pommo::redirect('login.php');
}
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('title', $pommo->_config['site_name'] . ' - ' . Pommo::_T('Mailing History'));
$smarty->assign('returnStr', Pommo::_T('Mailings Page'));


/** SET PAGE STATE
 * limit	- # of mailings per page
 * sort		- Sorting of Mailings [subject, mailgroup, subscriberCount, started, etc.]
 * order	- Order Type (ascending - ASC /descending - DESC)
 */
// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('mailings_history',array(
	'limit' => 10,
	'sort' => 'started',
	'order' => 'desc'),
	$_REQUEST);
	
$tally = PommoMailing::tally();

// fireup Monte's pager
$smarty->addPager($state['limit'], $tally);
$start = SmartyPaginate::getCurrentIndex();
SmartyPaginate::assign($smarty);

// if mail_id is passed, display the mailing.
if(isset($_GET['mail_id']) && is_numeric($_GET['mail_id'])) {
	$input = current(PommoMailing::get(array('id' => $_GET['mail_id'])));
	$smarty->assign($input);
	$smarty->display('inc/mailing.tpl');
	Pommo::kill();
}

// Fetch Mailings
$mailings = PommoMailing::get(array(
	'noBody' => TRUE,
	'sort' => $state['sort'],
	'order' => $state['order'],
	'limit' => $state['limit'],
	'offset' => $start));
	

$smarty->assign('state',$state);
$smarty->assign('mailings', $mailings);
$smarty->assign('tally',$tally); // was "rowinset"

$smarty->display('user/mailings.tpl');
Pommo::kill();
?>