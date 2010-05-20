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


/** 
 * Don't allow direct access to this file. Must be called from elsewhere
 */
defined('_IS_VALID') or die('Move along...');

// Cool DB Query Wrapper from Monte Ohrt
require_once (bm_baseDir.'/inc/safesql/SafeSQL.class.php');


/* Get the number of mailings in the table mailing_history of the database */
function & dbGetMailingCount(& $dbo) {
	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("SELECT count(id) FROM %s ", array($dbo->table['mailing_history']) );
	$count = $dbo->query($sql,0); // note, this will return "false" if no row returned -- though count always returns 0 (mySQL)!
	return ($count) ? $count : 0;
} //dbGetMailingCount


/* Get the mailings history matrix */
function & dbGetMailingHistory(& $dbo, $start, $limit, $order, $orderType) {

	//id, fromname, fromemail, frombounce, subject, body, ishtml, mailgroup, subscriberCount, started, finished, sent
	//$countmailings = $dbo->records();
	
	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("SELECT id, fromname, fromemail, frombounce, subject, ishtml, mailgroup, 
		subscriberCount, started, finished, sent FROM %s ORDER BY %s %s LIMIT %s, %s ", 
		array($dbo->table['mailing_history'], $order, $orderType, $start, $limit) );

	$mailings = array();
	
	while ($row = $dbo->getRows($sql)) {
	 		
	 		// calculate duration and mails per minute
	 		$started = strtotime($row['started']);
	 		$finished = strtotime($row['finished']);
	 		
	 		if (is_numeric($started) && ($started < $finished)) {
	 			$duration = $finished - $started;
	 			$durationStr = ''.round((($duration/60)/60)).':'.round(($duration/60)).':'.($duration%60);
	 			$mps = round($row['sent'] / $duration,2);
	 		}
	 		else {
	 			$duration = 0;
	 			$mpm = FALSE;
	 		}
	 		
	 		$mailings[] = array(
	 			'mailid' => $row['id'],
	 			'subject' => $row['subject'],
	 			'ishtml' => $row['ishtml'],
	 			'mailgroup' => $row['mailgroup'],
	 			'subscriberCount' => $row['subscriberCount'],
	 			'started' => $row['started'],
	 			'finished' => $row['finished'],
	 			'sent' => $row['sent'],
	 			'duration' => $durationStr,
	 			'mps' => $mps	 			
	 		);
	 }
	return $mailings;

} //dbGetMailingHistory



// Get Infos on a Mailing from a Array or numeric ID Information
function & dbGetMailingInfo(& $dbo, $selid) {
	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("SELECT id, fromname, fromemail, frombounce, subject, body, altbody, 
		ishtml, mailgroup, charset FROM %s WHERE id IN (%q)", 
		array($dbo->table['mailing_history'], $selid) );

	
	$mailings = $dbo->getAll($sql);

	return $mailings;
} //dbGetMailInfo



// Removes one or more data records from the mailing_history table
// $delid can be numeric oder a Array
function & dbRemoveMailFromHistory(& $dbo, $delid) {
	
	if (empty($delid))
		return false;
	// NOTE; not necessary to check if delid is an array, as safeSQL %q
	// will automatically convert to one... & SQL 'IN' can take 1 param.
		
	// delete array of mails from mailing_history table
	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("DELETE FROM %s WHERE id IN (%q) ", array($dbo->table['mailing_history'], $delid) );
	$dbo->query($sql);

	return true;
} //dbRemoveMailFromHistory

// returns the ID of a group based off a name
function & getGroupID($dbo, $groupname) {
	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("SELECT group_id FROM %s WHERE group_name='%s' LIMIT 1",
		array ($dbo->table['groups'], $groupname) );
		
	// bb Simplified return (note: the 0 says get first row. False if none returned)
	return $dbo->query($sql,0);

}
