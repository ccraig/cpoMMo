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
* Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

// Cool DB Query Wrapper from Monte Ohrt
// TODO -> merge this into class.dbo.php ( or rewrite $dbo->query() to use this class)-- DO NOT require it here
require_once (bm_baseDir . '/inc/safesql/SafeSQL.class.php');


// reads in an array of email addresses and inserts them into the queue table
function dbQueueCreate(& $dbo, & $input) {
	if (!is_array($input))
		die('<img src="' .
		bm_baseUrl . 'themes/shared/images/icons/alert.png" align="middle">dbQueueCreate() -> Bad Queue Passed.');

	// clear the table
	$sql = 'TRUNCATE TABLE ' . $dbo->table['queue'];
	$dbo->query($sql);

	foreach ($input as $email) {
		if (isset ($valStr))
			$valStr .= ',(\'' . $email . '\')';
		else
			$valStr = '(\'' . $email . '\')';
	}

	$sql = 'INSERT IGNORE INTO ' . $dbo->table['queue'] . ' (email) VALUES ' . $valStr;
	return $dbo->query($sql);
}

// Returns an array of emails + their domain from the queue. defaults to 100 at a time 
function & dbQueueGet(& $dbo, $id = '1', $limit = 100) {

	// purge our working queue
	$sql = 'UPDATE  '. $dbo->table['queue'] .' SET smtp_id=\'0\' WHERE smtp_id=\'' . $id . '\'';
	$dbo->query($sql);
	
	// mark our working queue
	$sql = 'UPDATE  '. $dbo->table['queue'] .' SET smtp_id=\'' . $id . '\' WHERE smtp_id=\'0\' LIMIT ' . $limit;
	$dbo->query($sql);

	// grab our working queue
	$sql = 'SELECT email FROM ' . $dbo->table['queue'] . ' WHERE smtp_id=\'' . $id . '\'';
	$emails = $dbo->getAll($sql, 'row', '0');
	
	// seperate emails into array([email],[domain])
	$retArray = array ();
	foreach ($emails as $email)
		$retArray[] = array (
			$email,
			substr($email,
			strpos($email,
			'@'
		) + 1));

	return $retArray;
}

function dbMailingCreate(& $dbo, & $input) {
	// generate security code
	$code = md5(rand(0, 5000) . time());

	// clear the current mailing from table if one exists ...
	$sql = 'TRUNCATE TABLE ' . $dbo->table['mailing_current'];
	$dbo->query($sql);

	// determine if this mailing is a HTML one or not..
	$html = "off";
	$altbody = '';
	if ($input['ishtml'] == "html") {
		$html = "on";
		if ($input['altInclude'] == 'yes' && !empty ($input['altbody']))
			$altbody = ' altbody=\'' . str2db($input['altbody']) . '\',';
	}

	// add this mailing to the mailing_current table.
	$sql = 'INSERT INTO ' . $dbo->table['mailing_current'] . ' SET fromname=\'' . str2db($input['fromname']) . '\', ' .
	'fromemail=\'' . str2db($input['fromemail']) . '\', frombounce=\'' . str2db($input['frombounce']) . '\', ' .
	'subject=\'' . str2db($input['subject']) . '\', body=\'' . str2db($input['body']) . '\',' . $altbody . ' ishtml=\'' . $html . '\', ' .
	'mailgroup=\'' . str2db($input['mailgroup']) . '\', subscriberCount=\'' . str2db($input['subscriberCount']) . '\', ' .
	'sent=\'0\', command=\'none\', status=\'stopped\', serial=NULL, securityCode=\'' . $code . '\', ' .
	'charset=\'' . str2db($input['charset']) . '\'';
	$dbo->query($sql);
	
	// clear background processing scripts
	$sql = 'UPDATE `'.$dbo->table['config'].'` SET config_value=0 WHERE config_name=\'dos_processors\' LIMIT 1';
	$dbo->query($sql);

	return $code;
}

function dbMailingStamp(& $dbo, $arg) {
	switch ($arg) {
		case 'start' :
			$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET started=NOW()';
			break;
		case 'finished' :
			$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET finished=NOW()';
			break;
		case 'stop' :
			$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET command=\'stop\'';
			break;
		case 'restart' :
			$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET command=\'restart\'';
	}
	return ($dbo->query($sql)) ? true : false;
}

// checks the status or if a "command" has been issued for a mailing
function dbMailingPoll($serial = '') {
	global $dbo;
	global $skipSecurity;
	global $logger;
	
	$sql = 'SELECT command, status, serial FROM ' . $dbo->table['mailing_current'];
	$dbo->query($sql);
	$row = mysql_fetch_row($dbo->_result);
	
	switch ($row[0]) {
		case 'restart':
				$sql = "UPDATE {$dbo->table['mailing_current']} SET serial='" . $serial . "', command='none', status='started'";
				$dbo->query($sql);
				$logger->addMsg('Mailing resumed under script with serial ' . $serial, 3);
			break;
	
		case 'stop':
				$sql = "UPDATE {$dbo->table['mailing_current']} SET status='stopped', command='none'";
				$dbo->query($sql);
				bmMKill('Mail processing has stopped as per Administrator\'s request',TRUE);
			break;
			
		default :
			if ($row[2] != $serial && !$skipSecurity) 
				bmMKill('Serials do not match. Another script is probably processing this mailing. To take control, stop and restart the mailing.',TRUE);
			
			if ($row[1] == "stopped")  // if mailing is in "stopped" status...
				bmMKill('Mail processing is in halted state. You must restart the mailing...',TRUE);			
			break;
	}
	return true;
}

function dbMailingUpdate(& $dbo, & $sentMails) {
	global $logger;
	
	// update DB
	$sql = 'UPDATE '.$dbo->table['mailing_current'].' SET sent=sent + '.count($sentMails).', notices=CONCAT_WS(\',\',notices,\''. mysql_real_escape_string(array2csv($logger->getMsg())) .'\')';
	$dbo->query($sql);

	// flush queue of sent mails
	if (!empty($sentMails)) {
	$sql = 'DELETE FROM ' . $dbo->table['queue'] . ' WHERE email IN (\''. implode('\',\'', $sentMails) . '\')';
	$dbo->query($sql);
	}
	return;
}


// Write a Mail that is being sent to the mailing history
function dbMailingEnd(&$dbo) {

	require_once (bm_baseDir . '/inc/db_groups.php');
	
	// TODO -- redo this function, maintain similar functionality
	// get group name
	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("SELECT mailgroup FROM %s LIMIT 1", array( $dbo->table['mailing_current'] ) );
  	$group = dbGroupName($dbo,$dbo->query($sql,0));
  	
  	// copy mailing to history table
	$sql = $safesql->query("INSERT INTO %s (fromname, fromemail, frombounce, subject, body, altbody, ishtml, 
		mailgroup, subscriberCount, started, finished, sent, charset) SELECT fromname, fromemail, frombounce, subject, 
		body, altbody, ishtml, mailgroup, subscriberCount, started, finished, sent, charset FROM %s LIMIT 1",
		array ($dbo->table['mailing_history'], $dbo->table['mailing_current']) );
	$dbo->query($sql);
	
	// update mailing group with actual name
	// TODO : this is kind of ugly.. can we have a more complicated INSERT statement while keeping MySQL 3.23 compatibility?
	$sql = $safesql->query("UPDATE %s SET mailgroup='%s' WHERE id='%s'", array($dbo->table['mailing_history'],$group,$dbo->lastId()));
	$dbo->query($sql);

 	
 	$sql = 'TRUNCATE TABLE '.$dbo->table['mailing_current'];
	$dbo->query($sql);
	$sql = 'TRUNCATE TABLE '.$dbo->table['queue'];
	$dbo->query($sql);
 }
 

function mailingQueueEmpty(& $dbo) {
	$sql = 'SELECT email FROM ' . $dbo->table['queue'] . ' LIMIT 1';
	return ($dbo->query($sql,0)) ? false : true;
}

// TODO -> don't pass $dbo to the following 2 functions...
	
function & bmInitMailer(& $dbo, $relay_id = 1) {
	/*
	if (isset ($_SESSION["bMailer_" . $relay_id]))
		return $_SESSION["bMailer_" . $relay_id];
	*/
	
	if (empty($_SESSION['pommo']['mailing'])) {
		$sql = "SELECT ishtml,fromname,fromemail,frombounce,subject,body,altbody,charset FROM " . $dbo->table['mailing_current'];
		$dbo->query($sql);
		$_SESSION['pommo']['mailing'] = $dbo->getRows($sql);
		
		// detect personalization
		$_SESSION['pommo']['mailing']['personal'] = isPersonalized($_SESSION['pommo']['mailing']['body']);
	}
	$row = & $_SESSION['pommo']['mailing'];
	
	// require personalization library if needed
	if ($row['personal']) {
		require_once (bm_baseDir . '/inc/lib.personalization.php');
		// cache personalization data into session if not already
		if(empty($_SESSION['pommo']['personalization'])) {
			$_SESSION['pommo']['personalization'] = getPersonalizations($row['body']);
		}
	}
	
	global $poMMo;
	global $logger;

	$html = FALSE;
	$altbody = NULL;
	if ($row['ishtml'] == "on") {
		$html = TRUE;
		if (!empty ($row['altbody']))
			$altbody = db2mail($row['altbody']);
	}

	$bMailer = new bMailer(db2mail($row['fromname']), $row['fromemail'], $row['frombounce'], $poMMo->_config['list_exchanger'], NULL, $row['charset'], $row['personal']);
	
	$logger->addMsg('bmMailer initialized with relay ID #'.$relay_id,1);

	// prepare the Mail with prepareMail()	-- if it fails, stop the mailing & report errors.
	if (!$bMailer->prepareMail(db2mail($row['subject']), db2mail($row['body']), $html, $altbody)) {
		$logger->addMsg(_T('prepareMail() returned errors.'));
		$sql = 'UPDATE '.$dbo->table['mailing_current'].' SET status=\'stopped\', notices=CONCAT_WS(\',\',notices,\''. mysql_real_escape_string(array2csv($logger->getMsg())) .'\')';
		$dbo->query($sql);
		bmMKill('prepareMail() returned errors.',TRUE);
	}

	// Set the appropriate SMTP relay and keep SMTP connection up
	if ($poMMo->_config['list_exchanger'] == 'smtp') {
		$bMailer->setRelay($poMMo->_config['smtp_' . $relay_id]);
		$bMailer->SMTPKeepAlive = TRUE;
	}
	return $bMailer;
}

function & bmInitThrottler(& $dbo, & $queue, $relay_id = 1) {
	
	/*
	if (isset ($_SESSION["bThrottle_" . $relay_id]))
		return $_SESSION["bThrottle_" . $relay_id];
	*/

	global $poMMo;
	
	if (empty($_SESSION['pommo']['mailing']['throttler'])) {
		$_SESSION['pommo']['mailing']['throttler'] = 
			$poMMo->getConfig(array (
				'throttle_MPS',
				'throttle_BPS',
				'throttle_DP',
				'throttle_DMPP',
				'throttle_DBPP'
			));
	}
	if (empty($_SESSION['pommo']['mailing']['throttler']['relay'.$relay_id])) {
		$_SESSION['pommo']['mailing']['throttler']['relay'.$relay_id] = array(
			'genesis' => time(), 'domainHistory' => array()
		);
	}

	
	$throttler = & $_SESSION['pommo']['mailing']['throttler'];
	
	return new bThrottler(
		$throttler['relay'.$relay_id]['genesis'],
		$queue,
		$throttler['throttle_MPS'],
		intval($throttler['throttle_BPS'] * 1024),
		$throttler['throttle_DP'],
		$throttler['throttle_DMPP'],
		intval($throttler['throttle_DBPP'] * 1024),
		$throttler['relay'.$relay_id]['domainHistory']
		);
}
?>
