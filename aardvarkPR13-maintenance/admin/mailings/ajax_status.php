<?php

/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/class.json.php');
require_once (bm_baseDir . '/inc/lib.txt.php');

$poMMo = & fireup('secure','keep');
$dbo = & $poMMo->_dbo;

$sql = 'SELECT subscriberCount, sent, notices, status, command FROM ' . $dbo->table['mailing_current'];
$dbo->query($sql);
if ($row = mysql_fetch_assoc($dbo->_result)) {
	$subscriberCount = $row['subscriberCount'];
	$sent = $row['sent'];
	$notices = quotesplit($row['notices']);
	$command = $row['command'];
	$status = $row['status']; 
	
	if ($command != 'none') {
		if (isset($poMMo->_data['commandTimer'])) {
			$commandTimer = $poMMo->_data['commandTimer'];
		}
		else {
			$commandTimer = time();
			$poMMo->_data['commandTimer'] = $commandTimer;
		}
		
		if ((time() - $commandTimer) > 19) 
			$status = 'frozen'; 
	}
} else {
	$subscriberCount = 0;
	$sent = 0;
	$percent = 100;
	$status = 'finished';
	$notices = array();
}

// end the mailing?
if ($sent >= $subscriberCount  || $status == 'finished') {
		$status = 'finished';
		require_once (bm_baseDir . '/inc/db_mailing.php');
		if (mailingQueueEmpty($dbo)) {
			dbMailingEnd($dbo);
		}
}

if (!isset($percent))
	$percent = round($sent * (100 / $subscriberCount));

// make JSON return
$json = array();
$encoder = new json;

$json['percent'] = $percent;
$json['sent'] = $sent;
$json['status'] = $status;

$json['command'] = (empty($command)) ? 'none' : $command;

if (count($notices) > 50) {
	$notices = array_slice($notices, -50);
}

$json['notices'] = (empty($notices)) ? null : $notices;

header('x-json: '.$encoder->encode($json));
?>