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
	
	
	// spawns a page in the background, used by mail processor.
	function spawn($page, $log = false) {
		global $pommo;
		$logger =& $pommo->_logger;

		/* Convert illegal characters in url */
		$page = str_replace(' ', '%20', $page);

		$errno = '';
		$errstr = '';

		// NOTE: fsockopen() SSL Support requires PHP 4.3+ with OpenSSL compiled in
		$ssl = ($pommo->_ssl) ? 'ssl://' : '';

		$out = "GET $page HTTP/1.1\r\n";
		$out .= "Host: " . $pommo->_hostname . "\r\n";
		
		//$out .= 'User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1) Gecko/20021204\r\n';
		//$out .= "Keep-Alive: 300\r\n";
		//$out .= "Connection: keep-alive\r\n";
		//$out .= "Referer: $pommo->_http\r\n";

		// to allow for basic .htaccess http authentication, 
		//   uncomment and fill in the following;
		// $out .= "Authorization: Basic " . base64_encode('username:password')."\r\n";

		$out .= "Connection: Close\r\n\r\n";

		$spawnPage = $out;
		
		$logger->addMsg('Attempting to spawn '.(($ssl) ? 'https://' : 'http://').$pommo->_hostname.':'.$pommo->_hostport.$page,2,TRUE);
		
		$socket = fsockopen($ssl . $pommo->_hostname, $pommo->_hostport, $errno, $errstr, 25);

		// LOG SPAWN ATTEMPTS TO FILE *TEMP, DEBUG*
		if($log || $pommo->_debug) {
			if(is_file($pommo->_workDir . '/SPAWN_0'))
				copy($pommo->_workDir . '/SPAWN_0',$pommo->_workDir . '/SPAWN_1');
				
			if ($handle = fopen($pommo->_workDir . '/SPAWN_0', 'w')) {
				fwrite($handle, $out);
				fclose($handle);
			}
		}

		if ($socket) {
			fwrite($socket, $out);
			sleep(1);
			fclose($socket); // spawned script must have ignore_user_abort, eh? ;)
		} else {
			$msg = time().' >>> Error Spawning Page! ** Errno : Errstr: ' . $errno . ' : ' . $errstr;
			$logger->addMsg($msg,3,TRUE);
			trigger_error($msg);
			return false;
		}
		return true;
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
			
			// remove all notices
			PommoMailCtl::delNotices($id,0);
			
			// remove mailing from DB
			$query = "
				DELETE FROM ". $dbo->table['mailings']."
				WHERE mailing_id=%i";
			$query = $dbo->prepare($query, array($id));
			
		}
		else {
			
			// shorten notices to last 50
			PommoMailCtl::delNotices($id);
			
			// update mailing in DB
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
	
	function addNotices($id) {	
		global $pommo;
		$logger =& $pommo->_logger;
		$dbo =& $pommo->_dbo;
		
		if(!is_numeric($id))
			return;
		
		$notices = array();
		$i = 0;
		foreach($logger->getAll() as $n) {
			$i++;
			$notices[] = $dbo->prepare("(%i,'%s', %i)",array($id, $n, $i));
		}
		
		// update DB notices
		if (!empty($notices)) {
			$query = "
				INSERT INTO ".$dbo->table['mailing_notices']."
				(mailing_id,notice,id) VALUES ".implode(',',$notices);
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
				ORDER BY touched ASC, id ASC
				LIMIT %i";
			$query = $dbo->prepare($query,array($id, ($count - $keep)));
			$dbo->query($query);
		}
		return;
	}
}
?>