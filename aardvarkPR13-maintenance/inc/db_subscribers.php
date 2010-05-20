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

// TODO -> code cleanups. Have a subscriber Object with ability to hold multiple
//  subscribers, their attributes (including pending status, etc.).. so as not to
//  do so many similar queries (esp. when involving confirmation codes/etc)

/** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

// TODO -> better manage includes via common, etc.  NO MORE require_once [huge performance hit]!!!!
require_once (bm_baseDir.'/inc/lib.txt.php');

// checks for the existance of an email in the pending, subscribers tables, or both if no table given.
function isDupeEmail(& $dbo, $email, $table = NULL) {
	if ($table != 'subscribers') {
	$sql = 'SELECT email FROM '.$dbo->table['pending'].' WHERE email=\''.str2db($email).'\'';
	if ($dbo->query($sql,0))
		return true;
	if ($table == 'pending')
		return false;
	}
	$sql = 'SELECT email FROM '.$dbo->table['subscribers'].' WHERE email=\''.str2db($email).'\'';
	if ($dbo->query($sql,0))
		return true;
	return false;
}

// dbGetSubscriber -> 
//  Reads in: Array of IDs, Array of Emails, ID, Email or Pending Code -- or 'all' to return all subscribers
//  Outputs depending on type passed:
//    (id) ID, (email) email, (code) Pending Code, or (detailed) Subscriber Details in *Subscriber Array* format
//  If an array is read in, an array of returnType will be returned representing the same order  the array was read in

// Subscirber Array format:
//   array[115] => array(      -- 115 == subscriber_id
// 		email => 'sub@scriber.com', 
//		date => '01/01/2006', 
//		data => array(
//				99 => 'Brice Burgess',   -- 99 == field_id
//				101 => 'Milwaukee'
//				)
//		)
function & dbGetSubscriber(& $dbo, $input, $retunType = 'detailed', $table = 'subscribers') {
	
	if (!is_array($input))
		$input = array ($input);

	if (isEmail($input[0]))
		$dbMatch = 's.email IN(\''.implode('\',\'', $input).'\')';
	elseif (is_numeric($input[0])) {
		$dbMatch = 's.'.$table.'_id IN ('.implode(',', $input).')';
		// set the ordering skeleton
		foreach ($input as $sid) {
			$subscribers[$sid] = array ('data' => array ());
		}
	}
	elseif ($input[0] == 'all')
		$dbMatch = '1';
	elseif ($table == 'pending') 
		$dbMatch = 's.code IN(\''.implode('\',\'', $input).'\')';
	
	$addFields = '';
	if ($table == 'pending')
		$addFields = ', s.newEmail';

	if (empty ($dbMatch)) {
		die('<img src="'.bm_baseUrl.'themes/shared/images/icons/alert.png" align="middle">dbGetSubscriber() -> Bad Input Passed.');
	}
	switch ($retunType) {
		case 'id' :
			$sql = 'SELECT s.'.$table.'_id FROM '.$dbo->table[$table].' s WHERE '.$dbMatch;
			return $dbo->getAll($sql, 'row', '0');
		case 'email' :
			$sql = 'SELECT s.email FROM '.$dbo->table[$table].' s WHERE '.$dbMatch;
			return $dbo->getAll($sql, 'row', '0');
		case 'code' :
			$sql = 'SELECT s.code FROM '.$dbo->table[$table].' s WHERE '.$dbMatch;
			return $dbo->getAll($sql, 'row', '0');
		case 'detailed' :
			$sql = 'SELECT DISTINCT s.'.$table.'_id,s.email,s.date,d.field_id,d.value'.$addFields.' FROM '.$dbo->table[$table].' s LEFT JOIN '.$dbo->table[$table.'_data'].' d ON (s.'.$table.'_id = d.'.$table.'_id) WHERE '.$dbMatch;
			$sArray = & $dbo->getAll($sql, 'row');

			foreach (array_keys($sArray) as $key) {
				$row = & $sArray[$key];
				// make the subscriber array if we haven't enountered this id before
				if (!isset ($subscribers[$row[0]]['email'])) {
					$subscribers[$row[0]] = array ('email' => & $row[1], 'date' => & $row[2], 'data' => array ());
					if (!empty($addFields))
						$subscribers[$row[0]]['newEmail'] =& $row[5]; // yes.. kludgey
				}
				// add subscriber_data value
				if (!empty($row[4]))
					$subscribers[$row[0]]['data'][$row[3]] = & $row[4];
			}
			return $subscribers;
	}
	die('<img src="'.bm_baseUrl.'themes/shared/images/icons/alert.png" align="middle">dbGetSubscriber() -> Reached end. Bad type sent?.');
}

// dbPending: Adds an entry to the pending table. Returns the confirmation code generated
//  Type is either "add", "del", or "mod" for Add Subscriber, Remove Subscriber, or Update Subscriber
//  $input is an array whose keys correlate to fields (columns) in a MySQL table
// fields must be passed as an array in order to be added to -- key = demo_id, value = demo_value

function dbPendingAdd(& $dbo, $type = NULL, $email = NULL, $fields = NULL) {
	$confirmation_key = md5(rand(0, 5000).time());
	$newEmail = '';
	
	switch ($type) {
		case 'change':
		case 'del' :
			if (!empty($fields['newEmail']) && isEmail($fields['newEmail']) && $fields['newEmail'] != $email)
				$newEmail = $fields['newEmail'];
				
			unset($fields['newEmail']); // must be done so as not to interfere with valuesStr below...
			unset($fields['newEmail2']);
		case 'add' :
		case 'password' :
			// check to make sure no entries for this email already exist in pending table
			if (isDupeEmail($dbo, $email, 'pending')) 
				return false;

			// add email to pending table
			$sql = 'INSERT INTO '.$dbo->table['pending'].' SET code=\''.str2db($confirmation_key).'\', type=\''.str2db($type).'\', email=\''.str2db($email).'\', newEmail=\''.str2db($newEmail).'\', date=\''.date("Y-m-d").'\'';
			$dbo->query($sql);

			// get ID of pending subscriber
			$pending_id = $dbo->lastId();

			if (empty ($pending_id) || !is_numeric($pending_id))
				die('<img src="'.bm_baseUrl.'themes/shared/images/icons/alert.png" align="middle">dbAddPending() -> Unable to fetch pending_id. Notify Administrator.');

			// if fields were given, add them to the pending_data table
			if (!empty ($fields) && is_array($fields))
				foreach (array_keys($fields) as $field_id) {
					// don't insert any null/blank values into pending_data
					if (!empty ($fields[$field_id])) {
						if (!isset ($values))
							$values = '('.str2db($pending_id).','.str2db($field_id).',\''.str2db($fields[$field_id]).'\')';
						else
							$values .= ',('.str2db($pending_id).','.str2db($field_id).',\''.str2db($fields[$field_id]).'\')';
					}
				}

			if (!empty ($values)) {
				$sql = 'INSERT INTO '.$dbo->table['pending_data'].' (pending_id,field_id,value) VALUES '.$values;
				$dbo->query($sql);
			}
			break;
		default:
			die('<img src="'.bm_baseUrl.'themes/shared/images/icons/alert.png" align="middle">dbAddPending() -> Unknown type passed.');
	}
	return $confirmation_key;
}

// dbPendingDel: <bool> Removes entries from pending & pending_data.
// you can pass a a code, email, or an array of either.
// returns the # of entries deleted
function dbPendingDel(& $dbo, $input = NULL) {
	if (empty ($input))
		return false;
		
	if (!is_array($input))
		$input = array($input);
		
	if (is_numeric($input[0])) // do not allow IDs to be passed to pendingDel...
		die('<img src="'.bm_baseUrl.'themes/shared/images/icons/alert.png" align="middle">dbPendingDel() -> Bad Input Passed.');

	// get IDs to purge
		$purge_ids = dbGetSubscriber($dbo, $input, 'id', 'pending');
		
	if (empty($purge_ids))
		return false; // nothing was deleted
		
	// TODO -> modify dbo->query() [or affected/records/etc] to take an array of SQL queries & return true if each one was successful. Then combine these
	$sql = 'DELETE FROM '.$dbo->table['pending_data'].' WHERE pending_id IN ('.implode(',', $purge_ids).')';
	$dbo->query($sql);

	$sql = 'DELETE FROM '.$dbo->table['pending'].' WHERE pending_id IN ('.implode(',', $purge_ids).')';
	return $dbo->affected($sql); // return # of rows deleted
}

// dbSubscriberAdd: Adds a subscriber to the subsribers table. If the passed argument
//  is an array (in dbGetSubscriber format), the subscriber will be added using its data. 
//  If it's a code / email, the subscriber will be looked up in the pending table
function dbSubscriberAdd(& $dbo, & $arg, $pending = FALSE) {

	if (is_array($arg)) 
		$subscriber = & $arg;
	else { // adding subscriber FROM pending table
		$subscribers = & dbGetSubscriber($dbo, $arg, 'detailed', 'pending');
		$subscriber = & current($subscribers);
		// sanitize subscriber data  for re-insertion.
		$subscriber['data'] = dbSanitize($subscriber['data'], 'db');
		$pending = TRUE;
	}
	
	// verify subscriber array is sane
	if (!is_array($subscriber) || empty ($subscriber['email']) || !isset ($subscriber['data']))
		die('<img src="'.bm_baseUrl.'themes/shared/images/icons/alert.png" align="middle">dbSubscriberAdd() -> Subscribers array not sane. Notify Administrator.');

	// set the date to today if it hasn't been provided	
	if (!isset ($subscriber['date']))
		$subscriber['date'] = date('Y-m-d');

	// insert new subscriber into subscribers, get ID
	$sql = 'INSERT INTO '.$dbo->table['subscribers'].' (email, date) VALUES(\''.$subscriber['email'].'\', \''.$subscriber['date'].'\')';
	$dbo->query($sql);
	$subscriber_id = $dbo->lastId();

	if (empty ($subscriber_id) || !is_numeric($subscriber_id))
		die('<img src="'.bm_baseUrl.'themes/shared/images/icons/alert.png" align="middle">dbSubscriberAdd() -> Unable to fetch subscriber_id. Notify Administrator.');

	// insert subscriber's data into subscribers_data
	foreach (array_keys($subscriber['data']) as $field_id) {
		if (!isset ($values))
			$values = '('.$subscriber_id.','.$field_id.',\''.$subscriber['data'][$field_id].'\')';
		else
			$values .= ',('.$subscriber_id.','.$field_id.',\''.$subscriber['data'][$field_id].'\')';
	}
	if (!empty ($values)) {
		$sql = 'INSERT INTO '.$dbo->table['subscribers_data'].' (subscribers_id,field_id,value) VALUES '.$values;
		$dbo->query($sql);
	}

	if ($pending) // remove subscriber from pending table if applicable.
		dbPendingDel($dbo, $arg);

	return true;
}

// dbUpdateFromPending: Updates a subscriber in the subsribers table.   If the passed argument
//  is an array, the subscriber will be updated with its data. If it's a code (not an array)
//  the subscriber will be updated with information from the pending table.
function dbSubscriberUpdate(& $dbo, & $arg, $pending = FALSE) {

		// TODO -> WAY TOO MANY QUERIES TAKE PLACE IN THIS PROCESS. FIX.
		//  perhaps pass email address from confirm page?? (vs. CODE, which is already looked up @ confirm page)

	if (is_array($arg)) {
		$subscriber = & $arg;
		$subscriber['data'] = dbSanitize($subscriber['data'], 'str');
	} else { // an email address was passed [note pending assignment]
		$subscribers = & dbGetSubscriber($dbo, $arg, 'detailed', 'pending');
		$subscriber = & current($subscribers);
		$subscriber['data'] = dbSanitize($subscriber['data'], 'db');
		$pending = TRUE;
	}

	// verify subscriber array is sane and the data array exists.
	if (!is_array($subscriber) || empty ($subscriber['email']) || !isset ($subscriber['data']))
		return false;
		
	
	// delete old subscriber information (from subscriber & pending table)
	// NOTE email gets passed by reference through functions and is eventually converted to an array in dbGetSubscriber,
	//  so we're working on a copy...
	$email = $subscriber['email'];
	if (!empty($subscriber['oldEmail']))
		$email = $subscriber['oldEmail'];
		dbSubscriberRemove($dbo, $email, $pending); 
	
	if (!empty($subscriber['newEmail'])) // set by pendingAdd (user update)
		$subscriber['email'] = $subscriber['newEmail'];
	
	// add new subscriber info
	return dbSubscriberAdd($dbo, $subscriber);
}

// dbSubscriberRemove: Pass an email address/array of or a pending 'code', and subscriber
//  will be purged from the subscribers, subscribers_data, and pending tables(s)
function dbSubscriberRemove(& $dbo, & $arg, $pending = FALSE) {

	if (is_array($arg) || isEmail($arg))
		$subscribers = & dbGetSubscriber($dbo, $arg, 'id');
	else {
		$email = & dbGetSubscriber($dbo, $arg, 'email', 'pending');
		$subscribers = & dbGetSubscriber($dbo,$email,'id');
	}
	
	// verify subscriber array is sane, and first subscriber_id is numeric
	if (empty ($subscribers) || !is_numeric($subscribers[0]))
		return false;

	// delete from subscribers table
	$sql = 'DELETE FROM '.$dbo->table['subscribers'].' WHERE subscribers_id IN ('.implode(',', $subscribers).')';
	$dbo->query($sql);

	// delete from subscribers_data table
	$sql = 'DELETE FROM '.$dbo->table['subscribers_data'].' WHERE subscribers_id IN ('.implode(',', $subscribers).')';
	$dbo->query($sql);

	dbPendingDel($dbo, $arg); // purge entries in pending

	return true;
}

// takes in an array of subscriber ids, and flags them with given type.
// TODO --> add support for flagging an email / array of emails..
function dbFlagSubscribers($subscribers, $type = 'update') {
	if(!is_array($subscribers) || !is_numeric($subscribers[0]))
		bmKill('Non subscriber ID passed to flagSubscribers()');
		
	global $dbo;
	
	foreach ($subscribers as $subscriber_id) {
		if (!isset ($values))
			$values = '('.$subscriber_id.',\''.$type.'\')';
		else
			$values .= ',('.$subscriber_id.',\''.$type.'\')';
	}
	if (!empty ($values)) {
		$sql = 'INSERT INTO '.$dbo->table['subscribers_flagged'].' (subscribers_id,flagged_type) VALUES '.$values;
		if ($dbo->query($sql))
			return true;
	}
	return false;
}
?>