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

$config = PommoAPI::configGet('public_history');
if($config['public_history'] == 'on') {
	$pommo->init(array('authLevel' => 0));
} else {
	Pommo::redirect('login.php');
}
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

// Remember the Page State
$state =& PommoAPI::stateInit('mailings_history');

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/json.php');
$json = new PommoJSON();

/**********************************
	PAGINATION AND ORDERING
*********************************/
// Get and Remember the requested page
if(!empty($_REQUEST['page']) && (
	is_numeric($_REQUEST['page']) && 
	$_REQUEST['page'] <= $state['pages']
	))
		$state['page'] = $_REQUEST['page'];

// Get and Remember the sort column
if(!empty($_REQUEST['sidx']) && (
	$_REQUEST['sidx'] == 'start' || 
	$_REQUEST['sidx'] == 'subject'
	))
		$state['sort'] = $_REQUEST['sidx'];
		

// Get and Remember the sort order
if(!empty($_REQUEST['sord']) && (
	$_REQUEST['sord'] == 'asc' || 
	$_REQUEST['sord'] == 'desc'
	))
		$state['order'] = $_REQUEST['sord'];
		
		
// Calculate the offset
$start = $state['limit']*$state['page']-$state['limit'];
if($start < 0)
	$start = 0;
	
	
/**********************************
	RECORD RETREVIAL
*********************************/
	
// normalize sort to database column
if($state['sort'] == 'start') $state['sort'] = 'started';
	
// Fetch Mailings for this Page
$mailings = PommoMailing::get(array(
	'noBody' => TRUE,
	'sort' => $state['sort'],
	'order' => $state['order'],
	'limit' => $state['limit'],
	'offset' => $start));


/**********************************
	OUTPUT FORMATTING
*********************************/

$records = array();
foreach($mailings as $o) {
	$row = array(
		'id' => $o['id'],
		'subject' => $o['subject'],
		'start' => $o['start']
	);
	
	if($o['status'] == 0) // only return "complete" mailings
		array_push($records,$row);
}

// format for JSON output to jqGrid
$json->add(array(
		'page' => $state['page'],
		'total' => $state['pages'],
		'records' => PommoMailing::tally(),
		'rows' => $records
	)
);
$json->serve();
?>