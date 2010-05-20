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

class PommoMailCtl {
 	
	// populates the queue with subscribers
	// accepts an array of subscriber IDs
	// returns (bool) - true if success
	function queueMake(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		// clearQueue
		$query = "DELETE FROM ".$dbo->table['queue'];
		if (!$dbo->query($query))
				return false;
				
		if (empty($in)) { // all subscribers
			$query = "
				INSERT IGNORE INTO ".$dbo->table['queue']."
				(subscriber_id)
				SELECT subscriber_id FROM ".$dbo->table['subscribers']."
				WHERE status=1";
			if (!$dbo->query($query))
				return false;
				
			return true;
		}
				
		$values = array();
		foreach ($in as $id)
			$values[] = $dbo->prepare("(%i)",array($id));
			
		$query = "
			INSERT IGNORE INTO ".$dbo->table['queue']."
			(subscriber_id)
			VALUES ".implode(',',$values);
		if (!$dbo->query($query))
				return false;	
		return true;
	}
	
	
	// Polls a mailings commands, performs any necessary actions
	function poll() {
		global $pommo;
		global $skipSecurity;
		global $relayID;
		global $serial;
		global $mailingID;
		global $mailer;
		
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		$query = "
			SELECT command, current_status, serial
			FROM ". $dbo->table['mailing_current']."
			WHERE current_id=%i";
		$query = $dbo->prepare($query,array($mailingID));
		
		$row = mysql_fetch_assoc($dbo->query($query));
		if (empty($row))
			PommoMailCtl::kill('Failed to poll mailing.');
			
		switch ($row['command']) {
		case 'restart':
			
			// terminate if this is not a "fresh"/"new" process
			if (is_object($mailer)) {
				$mailer->SmtpClose();
				PommoMailCtl::kill('Mailing with serial '.$serial.' terminated'); 
			}
			
			$query = "
				UPDATE ". $dbo->table['mailing_current']."
				SET
					serial=%i,
					command='none',
					current_status='started'
					WHERE current_id=%i";
			$query = $dbo->prepare($query,array($serial,$mailingID));
			if (!$dbo->query($query))
				PommoMailCtl::kill('Failed to restart mailing');
			$logger->addMsg(sprintf(Pommo::_T('Mailing resumed under serial %s'),$serial), 3);
			
			$query = "UPDATE ".$dbo->table['queue']." SET smtp=0";
			if(!$dbo->query($query))
				PommoMailCtl::kill('Could not clear relay allocations');
			
			break;
	
		case 'stop':
			if (is_object($mailer))
				$mailer->SmtpClose();
			$query = "
				UPDATE ". $dbo->table['mailing_current']."
				SET
					command='none',
					current_status='stopped'";
			if (!$dbo->query($query))
				PommoMailCtl::kill('Failed to stop mailing');
			PommoMailCtl::kill('Mailing stopped',TRUE);
			break;
			
		default :
			if (!$skipSecurity && $row['serial'] != $serial) 
				PommoMailCtl::kill(Pommo::_T('Serials do not match. Another background script is probably processing the mailing.'),TRUE);
			if ($row['current_status'] == 'stopped')
				PommoMailCtl::kill(Pommo::_T('Mailing halted. You must restart the mailing.'), TRUE);			
			
			// upate the timestamp
			$query = "UPDATE ". $dbo->table['mailing_current']." SET touched=NULL";
			$dbo->query($query);
			break;
		}
		
		return true;
	}
	
	// updates the queue and notices
	// accepts a array of failed emails
	// accepts a array of sent emails
	// accepts an hash array (key == email, value == subsriber ID)
	function update(&$sent, &$failed, &$emailHash) {
		global $mailingID;
		global $pommo;
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		if (!empty($sent)) {
			$a = array();
			foreach($sent as $e)
				$a[] = $emailHash[$e];
			
			$query = "
				UPDATE ".$dbo->table['queue']."
				SET status=1
				WHERE subscriber_id IN(%q)";
			$query = $dbo->prepare($query,array($a));
			
			if (!$dbo->query($query))
				PommoMailCtl::kill('Unable to update queue sent');
				
		}
		
		if (!empty($failed)) {
			$a = array();
			foreach($failed as $e)
				$a[] = $emailHash[$e];
			
			$query = "
				UPDATE ".$dbo->table['queue']."
				SET status=2
				WHERE subscriber_id IN(%q)";
			$query = $dbo->prepare($query,array($a));
			
			if (!$dbo->query($query))
				PommoMailCtl::kill('Unable to update queue failed');
		}
			
		// add notices
		PommoMailCtl::addNotices($mailingID);
	}
	
	
	// end a mailing
	// shortens notices to the last 50
	function finish($id = 0, $cancel = false, $test = false) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$status = ($cancel) ? 2 : 0;
		
		$query = "
			DELETE FROM ". $dbo->table['mailing_current']."
			WHERE current_id=%i";
		$query = $dbo->prepare($query, array($id));
		if ($dbo->affected($query) < 1)
			return false;
			
		if ($test) { // remove if this was a test mailing
			// remove notices
			PommoMailCtl::delNotices($id,0);
			
			$query = "
				DELETE FROM ". $dbo->table['mailings']."
				WHERE mailing_id=%i";
			$query = $dbo->prepare($query, array($id));
		}
		else {
			// shorten notices to last 50
			PommoMailCtl::delNotices($id);
			
			$query = "
				UPDATE ". $dbo->table['mailings']."
				SET 
				finished=FROM_UNIXTIME(%i),
				status=%i,
				sent=(SELECT count(subscriber_id) FROM ". $dbo->table['queue']." WHERE status > 0)
				WHERE mailing_id=%i";
			$query = $dbo->prepare($query, array(time(), $status, $id));
		}
		
		if (!$dbo->query($query))
			return false;
		return true;	
	}
	
	// cleanup function called just before script termination
	function kill($reason = null, $killSession = FALSE) {
		global $pommo;
		global $relayID;
		global $mailingID;
		
		$logger =& $pommo->_logger;
		$dbo =& $pommo->_dbo;
	
		if(!empty($reason))
			$logger->addMsg('Script Ending: ' . $reason, 2);	
			
		echo 'REASON! '.$reason;
	
		// release queue items allocated to this relayID
		PommoMailCtl::queueRelease($relayID);
		
		// add notices
		PommoMailCtl::addNotices($mailingID);
		
		if ($killSession)
			session_destroy();
			
		Pommo::kill($reason);
	}
	
	// allocates part of the queue to a relay
	// returns an array of subscriber_ids
	function queueGet($relay, $limit) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if(!is_numeric($relay) || $relay == 0)
			return array();
		
		// release from the queue (only if serials match)
		if(!PommoMailCtl::queueRelease($relay))
			PommoMailCtl::kill('Unable to release queue.');
		
		// mark our working queue
		$query = "
			UPDATE ".$dbo->table['queue']."
			SET smtp=%i
			WHERE smtp=0 AND status=0
			LIMIT %i";
		$query = $dbo->prepare($query,array($relay,$limit));
		if (!$dbo->query($query))
			PommoMailCtl::kill('Unable to mark queue.');
		
		// return our queue
		$query = "
			SELECT subscriber_id
			FROM ".$dbo->table['queue']."
			WHERE smtp=%i";
		$query = $dbo->prepare($query,array($relay));
		
		return $dbo->getAll($query, 'assoc', 'subscriber_id');
	}
	
	// release queue items allocated to a relay ID
	// returns success (bool)
	function queueRelease($relay) {
		global $pommo;
		global $mailingID;
		global $skipSecurity;
		global $serial;
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		// make sure the serial matches before releasing -- this prevents double sending
		//  it is helpful when a user resumes/restarts/dethaws a mailing while a background
		//  script is currently processing (which will have a queue checked out)
		
		// code removed...
		
		$query = "
			UPDATE ".$dbo->table['queue']."
			SET smtp=0 
			WHERE smtp=%i";
		$query = $dbo->prepare($query, array($relay));
		return (!$dbo->query($query)) ? false : true;
	}
	
	// returns the # of unsent emails in a queue
	function queueUnsentCount() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT COUNT(subscriber_id) 
			FROM ".$dbo->table['queue']."
			WHERE status=0";
		return $dbo->query($query,0);
	}
	
	// mark (serialize) a mailing
	// returns success (bool)
	function mark($serial, $id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if(!is_numeric($serial))
			return false;
			
		$query = "
			UPDATE ".$dbo->table['mailing_current']."
			SET serial=%i
			WHERE current_id=%i";
		$query = $dbo->prepare($query,array($serial, $id));
		return ($dbo->affected($query) > 0) ? true : false;
	}
	
	
	function respawn($p = array(), $page = false) {
		global $pommo;
		
		$defaults = array('code' => null, 'relayID' => null, 'serial' => null, 'spawn' => null);
		$p = PommoAPI :: getParams($defaults, $p);
		
		if (!$page)
			$page = $pommo->_baseUrl.'admin/mailings/mailings_send4.php';
			
		return PommoMailCtl::spawn($page.'?securityCode='.$p['code'].'&relayID='.$p['relayID'].'&serial='.$p['serial'].'&spawn='.$p['spawn']);
	}
	
	// spawns a page in the background, used by mail processor.
	function spawn($page) {
		global $pommo;
		$logger =& $pommo->_logger;

		/* Convert illegal characters in url */
		$page = str_replace(' ', '%20', $page);

		$errno = '';
		$errstr = '';

		// NOTE: fsockopen() SSL Support requires PHP 4.3+ with OpenSSL compiled in
		$ssl = ($pommo->_ssl) ? 'ssl://' : '';

		$out = "GET $page HTTP/1.1\r\n";
		$out .= "Host: " . $pommo->_hostname . ":".$pommo->_hostport."\r\n";
		$out .= 'User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1) ';
		$out .= "Gecko/20021204\r\n";
		$out .= "Keep-Alive: 300\r\n";
		$out .= "Connection: keep-alive\r\n";
		$out .= "Referer: $pommo->_http\r\n";

		// to allow for basic .htaccess http authentication, 
		//   uncomment and fill in the following;
		// $out .= "Authorization: Basic " . base64_encode('username:password')."\r\n";

		$out .= "\r\n";
		
		if ($pommo->_verbosity < 3)
			echo 'Attempting to spawn '.(($ssl) ? 'https://' : 'http://').$pommo->_hostname.':'.$pommo->_hostport.$page.'<br />';
		
		$socket = fsockopen($ssl . $pommo->_hostname, $pommo->_hostport, $errno, $errstr, 10);

		if ($socket) {
			fwrite($socket, $out);
		} else {
			$logger->addErr(Pommo::_T('Error Spawning Page') . ' ** Errno : Errstr: ' . $errno . ' : ' . $errstr);
			return false;
		}

		return true;
	}
	
	function addNotices($id) {	
		global $pommo;
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		$notices = array();
		foreach($logger->getAll() as $n)
			$notices[] = $dbo->prepare("(%i,'%s')",array($id, $n));
		
		// update DB notices
		if (!empty($notices)) {
			$query = "
				INSERT INTO ".$dbo->table['mailing_notices']."
				(mailing_id,notice) VALUES ".implode(',',$notices);
			$dbo->query($query);
		}
		
		// trim notices
		PommoMailCtl::delNotices($id);
	}
	
	// removes notices from a mailing
	// accepts mailing ID
	// accepts # of notices to keep -- if 0, none are kept
	function delNotices($id, $keep = 50) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$keep = intval($keep);
		if ($keep == 0) {
			$query = "
				DELETE FROM ".$dbo->table['mailing_notices']." 
				WHERE mailing_id=%i";
			$query = $dbo->prepare($query,array($id));
			$dbo->query($query);
			return;
		}
		
		// shorten notices to keep # (50)
		$query = "
			SELECT count(mailing_id) 
				FROM ".$dbo->table['mailing_notices']." 
			WHERE mailing_id=%i";
		$query = $dbo->prepare($query,array($id));
		$count = $dbo->query($query,0);
		
		if ($count > $keep) {
			$query = "
				DELETE FROM ".$dbo->table['mailing_notices']." 
				WHERE mailing_id=%i
				ORDER BY touched ASC
				LIMIT %i";
			$query = $dbo->prepare($query,array($id, ($count - $keep)));
			$dbo->query($query);
		}
		return;
	}
	
	// temporary function to get the current mailing ID.. will be removed when
	//  simultaneous mailings support is enabled
	function getCurID() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "SELECT current_id FROM {$dbo->table['mailing_current']} LIMIT 1";
		return $dbo->query($query,0);
	}
	
}
?>