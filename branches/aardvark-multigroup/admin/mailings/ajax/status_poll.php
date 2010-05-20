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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
 Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.json.php');
$pommo->logErrors(); // PHP Errors are logged, turns display_errors off.
$pommo->toggleEscaping(); // Wraps _T and logger responses with htmlspecialchars()


$json = array(
	'percent' => null,
	'status' => null,
	'statusText' => null,
	'sent' => null,
	'incAttempt' => FALSE,
	'command' => FALSE,
	'notices' => FALSE,
	'timeStamp' => null
);

$statusText = array(
	1 => Pommo::_T('Processing'),
	2 => Pommo::_T('Stopped'),
	3 => Pommo::_T('Frozen'),
	4 => Pommo::_T('Finished')
);

$mailing = (isset($_GET['id'])) ?
	 current(PommoMailing::get(array('id' => $_GET['id']))) :
	 current(PommoMailing::get(array('active' => TRUE)));

// status >> 1: Processing  2: Stopped  3: Frozen  4: Finished
if ($mailing['status'] != 1)
	$json['status'] = 4;
elseif($mailing['current_status'] == 'stopped')
	$json['status'] = 2;
else
	$json['status'] = 1;


// check for frozen mailing

// get the old timestamp
$timestamp = $pommo->get('timestamp');

if (empty($timestamp))
	$timestamp = @$mailing['touched']; // get retuns a blank array -- not false

if ($json['status'] != 4) {
	if ($mailing['command'] != 'none' || ($mailing['touched'] == $timestamp && $mailing['current_status'] != 'stopped'))
		$json['incAttempt'] = TRUE;
	if ($mailing['command'] != 'none')
		$json['command'] = TRUE;
	if ($_GET['attempt'] > 4)
		$json['status'] = 3;
}

@$pommo->set(array('timestamp' => $mailing['touched']));

$json['statusText'] = $statusText[$json['status']];

// get last 50 unique notices
$oldNotices = $pommo->get('notices');
$newNotices = PommoMailing::getNotices($mailing['id'], 50, true);
$notices = array();
foreach($newNotices as $time => $arr) {
	if (!isset($oldNotices[$time])) {
		$notices = array_merge($notices, $arr);
		continue;	
	}
	foreach($arr as $notice) {
		if (array_search($notice,$arr) === false) 
			$notices[] = $notice;
	}
}
$pommo->set(array('notices' => $newNotices));
$json['notices'] = array_reverse($notices);

// calculate sent
$query = "
	SELECT count(subscriber_id) 
	FROM ".$dbo->table['queue']."
	WHERE status > 0";
$json['sent'] = ($json['status'] == 4) ? 
	$mailing['sent'] :
	$dbo->query($query,0);

$json['percent'] = ($json['status'] == 4) ?
	100 :
	round($json['sent'] * (100 / $mailing['tally']));

$encoder = new json;
die($encoder->encode($json));
?>