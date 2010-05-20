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
require ('../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

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

/** SET PAGE STATE
 * limit	- # of mailings per page
 * sort		- Sorting of Mailings [subject, started]
 * order	- Order Type (ascending - ASC /descending - DESC)
 */
// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('mailings_history',array(
	'limit' => 100,
	'sort' => 'finished',
	'order' => 'asc',
	'page' => 1),
	$_REQUEST);



// if mail_id is passed, display the mailing.
if(isset($_GET['mail_id']) && is_numeric($_GET['mail_id'])) {
	$input = current(PommoMailing::get(array('id' => $_GET['mail_id'])));
	
	// attempt personalizations
	if(isset($_GET['email']) && isset($_GET['code'])) {
		$subscriber = current(PommoSubscriber::get(array('email' => $_GET['email'], 'status' => 1)));
		if($_GET['code'] == PommoSubscriber::getActCode($subscriber)) {
			Pommo::requireOnce($pommo->_baseDir.'inc/helpers/personalize.php'); // require once here so that mailer can use
			
			$matches = array();
			preg_match('/\[\[[^\]]+]]/', $input['body'], $matches);
			if (!empty($matches)) {
				$pBody = PommoHelperPersonalize::search($input['body']);
				$input['body'] = PommoHelperPersonalize::replace($input['body'], $subscriber, $pBody);
				
			}
			preg_match('/\[\[[^\]]+]]/',  $input['altbody'], $matches);
			if (!empty($matches)) {
				$pAltBody = PommoHelperPersonalize::search($input['altbody']);
				$input['altbody'] = PommoHelperPersonalize::replace($input['altbody'], $subscriber, $pAltBody);	
			}
		}

	}

	$smarty->assign($input);
	$smarty->display('inc/mailing.tpl');
	Pommo::kill();
}


/**********************************
	VALIDATION ROUTINES
*********************************/
	
if(!is_numeric($state['limit']) || $state['limit'] < 10 || $state['limit'] > 200)
	$state['limit'] = 100;
	
if($state['order'] != 'asc' && $state['order'] != 'desc')
	$state['order'] = 'asc';
	
if($state['sort'] != 'start' &&
	$state['sort'] != 'subject')
		$state['sort'] = 'start';
		
		
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

$smarty->display('user/mailings.tpl');
Pommo::kill();
?>