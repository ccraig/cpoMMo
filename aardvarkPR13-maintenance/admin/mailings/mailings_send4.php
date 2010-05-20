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
	STARTUP ROUTINES
 *********************************/

// skips serial and security code checking. For debbuing this script.
$skipSecurity = TRUE;

// # of mails to fetch from the queue at a time (Default: 100)
$queueSize = 100;

// set maximum runtime of this script in seconds (Default: 110). If unable to set (SAFE MODE,etc.), max runtime will default to 3 seconds less than current max.
$maxRunTime = 110;
if (ini_get('safe_mode'))
	$maxRunTime = ini_get('max_execution_time') - 3;
else
	set_time_limit($maxRunTime +7);

define('_IS_VALID', TRUE);
require ('../../bootstrap.php');
require (bm_baseDir . '/inc/class.bmailer.php');
require (bm_baseDir . '/inc/class.bthrottler.php');
require (bm_baseDir . '/inc/db_mailing.php');

$serial = (empty ($_GET['serial'])) ? time() : addslashes($_GET['serial']);
$bm_sessionName = $serial;

$poMMo = & fireup('sessionName');
$dbo = & $poMMo->_dbo;
$dbo->dieOnQuery(FALSE); // TODO -> what was this for? isn't it somewhat dangerous?

// load from config -- DOS protection, throttle values...

$logger = & $poMMo->_logger;

if (empty ($poMMo->_config['list_exchanger'])) {
	$logger->addMsg(sprintf(_T('Mailing processor with serial %d spawned'), $serial), 3);
	// get list exchanger & smtp values. If more than 1 smtp relay exist, enter "multimode"
	$config = $poMMo->getConfig(array (
		'list_exchanger',
		'smtp_1',
		'smtp_2',
		'smtp_3',
		'smtp_4',
		'throttle_SMTP'
	));
	$poMMo->_config['list_exchanger'] = $config['list_exchanger'];
	$poMMo->_config['multimode'] = false;
	$poMMo->_config['throttler'] = 'shared';

	if ($config['list_exchanger'] == 'smtp') {
		if (!empty ($config['smtp_1'])) {
			$poMMo->_config['smtp_1'] = unserialize($config['smtp_1']);
			$logger->addMsg('SMTP Relay #1 detected', 1);
		}
		if (!empty ($config['smtp_2'])) {
			$poMMo->_config['multimode'] = true;
			$poMMo->_config['smtp_2'] = unserialize($config['smtp_2']);
			$logger->addMsg('SMTP Relay #2 detected', 1);
		}
		if (!empty ($config['smtp_3'])) {
			$poMMo->_config['multimode'] = true;
			$poMMo->_config['smtp_3'] = unserialize($config['smtp_3']);
			$logger->addMsg('SMTP Relay #3 detected', 1);
		}
		if (!empty ($config['smtp_4'])) {
			$poMMo->_config['multimode'] = true;
			$poMMo->_config['smtp_4'] = unserialize($config['smtp_4']);
			$logger->addMsg('SMTP Relay #4 detected', 1);
		}
		if ($config['throttle_SMTP'] == 'individual')
			$poMMo->_config['throttler'] = 'individual';
		$logger->addMsg('SMTP Throttle control set to: ' . $poMMo->_config['throttler'], 1);
		if ($poMMo->_config['multimode'])
			$logger->addMsg('multimode enabled', 1);
	}
} else {
	$logger->addMsg(sprintf(_T('Mailing processor with serial %d spawned'), $serial), 2);
}

// cleanup function called just before script termination
function bmMKill($reason, $killSession = FALSE) {
	global $logger;
	global $dbo;

	$logger->addMsg('Script Ending: ' . $reason, 2);

	// deduct value (this script) from DOS mail processor protection.
	$sql = 'UPDATE `' . $dbo->table['config'] . '` SET config_value=config_value-1 WHERE config_name=\'dos_processors\' LIMIT 1';
	$dbo->query($sql);

	// update DB notices
	$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET notices=CONCAT_WS(\',\',notices,\'' . mysql_real_escape_string(array2csv($logger->getAll())) . '\')';
	$dbo->query($sql);
	
	if ($killSession)
		session_destroy();

	bmKill($reason);
}

function bmSpawn($url) {
	global $logger;
	$logger->addMsg('Attempting to spawn: '.$url,1);
	(bmHttpSpawn($url)) ? $logger->addMsg($url.' spawned.',1) : $logger->addMsg('ERROR SPAWNING: '.$url,1);
}

// checks a message for personalization
function isPersonalized(&$msg) {
	$matches = array();
	$pattern = '/\[\[[^\]]+]]/';
	preg_match($pattern, $msg, $matches);
	return (empty($matches)) ? FALSE : TRUE;
}

/**********************************
	SECURITY ROUTINES
 *********************************/

// DOS prevention
if ($poMMo->_config['dos_processors'] > 5 && !$skipSecurity)
	die();
else {
	$sql = 'UPDATE `' . $dbo->table['config'] . '` SET config_value=config_value+1 WHERE config_name=\'dos_processors\' LIMIT 1';
	$dbo->query($sql);
}

// check to see if mailing is finished, has been serialized, and security code
$sql = 'SELECT serial,securityCode,finished FROM ' . $dbo->table['mailing_current'] . ' LIMIT 1';
$dbo->query($sql);
$row = mysql_fetch_assoc($dbo->_result);

if ($row['finished'] > 0)
	bmMKill('Mailing has completed.',TRUE);
	
if (empty ($row['serial'])) { // if no serial has yet been entered for this mailing... serialize & start the mailing...
	$sql = "UPDATE {$dbo->table['mailing_current']} SET serial='" . $serial . "', status='started', command='none'";
	$dbo->query($sql);
}

if (!$skipSecurity && (empty($row['securityCode']) || $_GET['securityCode'] != $row['securityCode']))
	bmMKill('Script stopped for security reasons.',TRUE);

/**********************************
 * MAILING INITIALIZATION
 *********************************/

// checks to see if mailing should be halted (or is in halted state...)
dbMailingPoll($serial);

// spawn script per relay if in multimode
if ($poMMo->_config['multimode']) {
	if (empty ($_GET['relay_id'])) {
		if (!empty ($poMMo->_config['smtp_1']))
			bmSpawn(bm_baseUrl .
			'admin/mailings/mailings_send4.php?relay_id=1&serial=' .
			$serial . '&securityCode=' . $_GET['securityCode']);
		sleep(2); // delay to help prevent "shared" throttlers racing to create queue
		if (!empty ($poMMo->_config['smtp_2']))
			bmSpawn(bm_baseUrl .
			'admin/mailings/mailings_send4.php?relay_id=2&serial=' .
			$serial . '&securityCode=' . $_GET['securityCode']);
		if (!empty ($poMMo->_config['smtp_3']))
			bmSpawn(bm_baseUrl .
			'admin/mailings/mailings_send4.php?relay_id=3&serial=' .
			$serial . '&securityCode=' . $_GET['securityCode']);
		if (!empty ($poMMo->_config['smtp_4']))
			bmSpawn(bm_baseUrl .
			'admin/mailings/mailings_send4.php?relay_id=4&serial=' .
			$serial . '&securityCode=' . $_GET['securityCode']);
		bmMKill('Multimode detected. Spawning background scripts for SMTP relays.');
	}
	$bmMailer = & bmInitMailer($dbo, $_GET['relay_id']);
	$bmQueue = & dbQueueGet($dbo, $_GET['relay_id'], $queueSize);

	if ($poMMo->_config['throttler'] == 'individual')
		$bmThrottler = & bmInitThrottler($dbo, $bmQueue, $_GET['relay_id']);
	else
		$bmThrottler = & bmInitThrottler($dbo, $bmQueue);
} else {
	$bmMailer = & bmInitMailer($dbo);
	$bmQueue = & dbQueueGet($dbo, 1, $queueSize);
	$bmThrottler = & bmInitThrottler($dbo, $bmQueue);
}


// start throttler's timer
$bmThrottler->startScript($maxRunTime);

$logger->addMsg('Mailer+Queue+Throttler Initialized. Queue Size: ' . count($bmQueue) . ' mails.', 1);

$byteMask = $bmThrottler->byteTracking();
if ($byteMask > 1) // byte tracking/throttling enabled
	$bmMailer->trackMessageSize();

/**********************************
   PROCESS QUEUE
 *********************************/

// TODO -> all these globals seem kludgey.. use iterative, or a sender object.

$sentMails = array ();
$timer = time();

function updateDB(& $sentMails, & $timer) {
	global $serial;
	global $dbo;

	// update mailing status in database and flush sent mails from queue
	dbMailingUpdate($dbo, $sentMails);

	// poll mailing	
	dbMailingPoll($serial);

	// reset variables
	$sentMails = array ();
	$timer = time();
}

// recursively proccess the throttler, returns true if queue is empty, false if not.
function proccessQueue() {
	global $bmThrottler;
	global $bmMailer;
	global $logger;
	global $byteMask;
	global $sentMails;
	global $poMMo;
	global $timer;

	// check if there are mails in throttler queue, return true if throttler's queue is empty
	if (!$bmThrottler->mailsInQueue())
		return true;

	// attempt to pull email from throttler's queue
	$mail = $bmThrottler->pullQueue();

	// if an email was returned, send it.
	if ($mail) {
		if (!$bmMailer->bmSendmail($mail[0])) // sending failed, write to log  
			$logger->addMsg(_T('Error Sending Mail'));

		// If throttling by bytes (bandwith) is enabled, add the size of the message to the throttler
		if ($byteMask > 1) {
			$bytes = $bmMailer->GetMessageSize();
			if ($byteMask > 2)
				$bmThrottler->updateBytes($bytes, $mail[1]);
			else
				$bmThrottler->updateBytes($bytes);
			$logger->addMsg('Added ' . $bytes . ' bytes to throttler.', 1);
		}

		// add email to sent mail array
		$sentMails[] = $mail[0];
	}
	elseif ($bmThrottler->getCommand() == 2) // kill command received
	return false;

	// Every 10-ish seconds, or to prevent MySQL update "flood", launch updateDB() which; 
	// updates mailing status in database, removes sent mails from queue, and perform a "poll" 
	if ((time() - $timer) > 9 || count($sentMails) > 40 || $logger->isMsg() > 40)
		updateDB($sentMails, $timer);

	// recurisve call to processQueue()
	return proccessQueue();
}

// process the queue until it is empty or kill command received
while (proccessQueue()) {

	updateDB($sentMails, $timer);

	// fetch emails from queue
	$bmQueue = array ();
	$bmQueue = & dbQueueGet($dbo, $_GET['relay_id'], $queueSize);
	

	// if queue is empty, end mailing and kill script.	
	if (empty($bmQueue)) {
		if ($poMMo->_config['multimode']) {
			// before killing check to see if we're in multimode and queue is truly empty
			$sql = 'SELECT COUNT(*) FROM ' . $dbo->table['queue'] . ' LIMIT 1';
			if ($dbo->query($sql,0)) {
				// the queue is not empty, another relay is working on it. Sleep 10 seconds then break (respawn)
				sleep(10);
				break;
			}
		}
		
		dbMailingStamp($dbo, "finished");
		if ($bmMailer->SMTPKeepAlive == TRUE)
			$bmMailer->SmtpClose();
		bmMKill('Mailing finished!',TRUE);
	}
	else {
	// else, repopulate throttler's queue
	$bmThrottler->loadQueue($bmQueue);
	$logger->addMsg('Adding more mails to the throttler queue.', 1);
	}
}

updateDB($sentMails, $timer);

// kill signal sent from throttler (max exec time likely reached), respawn.	
if (!empty ($_GET['relay_id']))
	bmSpawn(bm_baseUrl .
	'admin/mailings/mailings_send4.php?relay_id=' .
	$_GET['relay_id'] . '&serial=' . $serial . '&securityCode=' . $_GET['securityCode']);
else
	bmSpawn(bm_baseUrl .
	'admin/mailings/mailings_send4.php?serial=' . $serial . '&securityCode=' . $_GET['securityCode']);

bmMKill('Respawned... Max exec time likely reached.');

//echo 'Ready to respawn <a href="mailings_send4.php?serial=' . $serial . '&securityCode=' . $_GET['securityCode'].'">here</a>';
?>
