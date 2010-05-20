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
 
// include the mailing prototype object 
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/prototypes.php');

/**
 * Mailing: A poMMo Mailing
 * ==SQL Schema==
 *	mailing_id		(int)		Database ID/Key
 *	fromname		(str)		Header: FROM name<>
 *  fromemail		(str)		Header: FROM <email>
 *  subject			(str)		Header: SUBJECT
 *  body			(str)		Message Body
 *  altbody			(str)		Alternative Text Body
 *  ishtml			(enum)		'on','off' toggle of HTML mailing
 *  mailgroup		(str)		Name of poMMo group mailed
 *  subscriberCount	(int)		Number of subscribers in group
 *  started			(datetime)	Time mailing started
 *  finished		(datetime)	Time mailing ended
 *  sent			(int)		Number of mails sent
 *  charset			(str)		Encoding of Message
 *  status			(bool)		0: finished, 1: processing, 2: cancelled
 * 	
 * ==Additional Columns for Current Mailing==
 * 
 *  current_id		(int)		ID of current mailing (from mailing_id)
 *  command			(enum)		'none' (default), 'restart', 'stop'
 *  serial			(int)		Serial of this mailing
 *  securityCode	(char[32])	Security Code of Mailing
 *  current_status	(enum)		'started', 'stopped' (default)
 */ 

class PommoMailing {
	
	// make a mailing template
	// accepts a mailing template (assoc array)
	// accepts a flag (bool) to designate return of current mailing type
	// return a mailing object (array)
	function & make($in = array(), $current = FALSE) {
		$o = ($current) ?
			PommoType::mailingCurrent() :
			PommoType::mailing();
		return PommoAPI::getParams($o, $in);
	}
	
	// make a mailing template based off a database row (mailing* schema)
	// accepts a mailing template (assoc array)  
	// accepts a flag (bool) to designate return of current mailing type
	// return a mailing object (array)	
	function & makeDB(&$row) {
		$in = @array(
		'id' => $row['mailing_id'],
		'fromname' => $row['fromname'],
		'fromemail' => $row['fromemail'],
		'frombounce' => $row['frombounce'],
		'subject' => $row['subject'],
		'body' => $row['body'],
		'altbody' => $row['altbody'],
		'ishtml' => $row['ishtml'],
		'group' => $row['mailgroup'],
		'tally' => $row['subscriberCount'],
		'start' => $row['started'],
		'end' => $row['finished'],
		'sent' => $row['sent'],
		'charset' => $row['charset'],
		'status' => $row['status']);
			
		if ($row['status'] == 1) {
			$o = @array(
				'command' => $row['command'],
				'serial' => $row['serial'],
				'code' => $row['securityCode'],
				'touched' => $row['touched'], // TIMESTAMP
				'current_status' => $row['current_status']);
			$in = array_merge($o,$in);
		}

		$o = ($row['status'] == 1) ?
			PommoAPI::getParams(PommoType::mailingCurrent(),$in) :
			PommoAPI::getParams(PommoType::mailing(),$in);
		return $o;
	}
	
	// mailing validation
	// accepts a mailing object (array)
	// returns true if mailing ($in) is valid, false if not
	function validate(&$in) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$invalid = array();

		if (empty($in['fromemail']) || !PommoHelper::isEmail($in['fromemail']))
			$invalid[] = 'fromemail';
		if (empty($in['frombounce']) || !PommoHelper::isEmail($in['frombounce']))
			$invalid[] = 'frombounce';
		if (empty($in['subject']))
			$invalid[] = 'subject';
		if (empty($in['body']))
			$invalid[] = 'body';
		if (!is_numeric($in['tally']) || $in['tally'] < 1)
			$invalid[] = 'subscriberCount';
		if (!empty($in['start']) && !is_numeric($in['start']))
			$invalid[] = 'started';
		if (!empty($in['end']) && !is_numeric($in['end']))
			$invalid[] = 'finished';
		if (!empty($in['sent']) && !is_numeric($in['sent']))
			$invalid[] = 'sent';
			
		switch($in['status']) {
			case 0:
			case 1:
			case 2:
				break;
			default:
				$invalid[] = 'status';
		}
		
		if ($in['status'] == 1) {
			switch ($in['command']) {
				case 'none':
				case 'restart':
				case 'stop':
					break;
				default:
					$invalid[] = 'command'; 
			}
			if (!empty($in['serial']) && !is_numeric($in['serial']))
			$invalid[] = 'serial';
			switch ($in['current_status']) {
				case 'started':
				case 'stopped':
					break;
				default:
					$invalid[] = 'current_status'; 
			}
		}
			
		if (!empty($invalid)) {
			$logger->addErr("Mailing failed validation on; ".implode(',',$invalid),1);
			return false;
		}
		
		return true;
	}
	
	
	// fetches mailings from the database
	// accepts a filtering array -->
	//   active (bool) toggle returning of only active mailings
	//	 noBody (bool) toggle returning of mailing bodies (default false)
	//   id (array||str) -> A single or an array of mailing IDs
	//   code (str) security code of mailing
	//   sort (str) [subject, mailgroup, subscriberCount, started, etc.]
	//   order (str) "ASC" or "DESC"
	//   limit (int) limits # mailings returned
	//   offset (int) the SQL offset to start at
	// returns an array of mailings. Array key(s) correlates to mailing ID.
	function & get($p = array()) {
		$defaults = array('active' => false, 'noBody' => false, 'id' => null, 'code' => null, 'sort' => null, 'order' => null, 'limit' => null, 'offset' => null);
		$p = PommoAPI :: getParams($defaults, $p);
		
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$p['active'] = ($p['active']) ? 1 : null;
		
		if (is_numeric($p['limit']) && !is_numeric($p['offset']))
			$p['offset'] = 0;
		
		$o = array();
		
		$select = "mailing_id, fromname, fromemail, frombounce, subject, ishtml, mailgroup, subscriberCount, started, finished, sent, charset, status, c.*";
		if(!$p['noBody'])
			$select .= ", body, altbody";
		
		$query = "
			SELECT $select
			FROM 
				" . $dbo->table['mailings']." m
				LEFT JOIN " . $dbo->table['mailing_current']." c ON (m.mailing_id = c.current_id)
			WHERE
				1
				[AND m.status=%I]
				[AND m.mailing_id IN(%C)]
				[AND c.securityCode='%S'] 
				[ORDER BY %S] [%S] 
				[LIMIT %I, %I]";
		$query = $dbo->prepare($query,array($p['active'],$p['id'],$p['code'], $p['sort'], $p['order'], $p['offset'], $p['limit']));
		
		while ($row = $dbo->getRows($query)) {
			$o[$row['mailing_id']] = PommoMailing::makeDB($row);
		}
		
		return $o;
	}
	

	
	// adds a mailing to the database
	// accepts a mailing (array)
	// returns the database ID of the added mailing,
	//  OR if the mailing is a current mailing (status == 1), returns
	//  the security code of the mailing. FALSE if failed
	function add(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		// set the start time if not provided
		if (empty($in['start']))
			$in['start'] = time();
			
		if (empty($in['sent']))
			$in['sent'] = 0;

		if (!PommoMailing::validate($in))
			return false;
		
		$query = "
			INSERT INTO " . $dbo->table['mailings'] . "
			SET
			[fromname='%S',]
			[fromemail='%S',]
			[frombounce='%S',]
			[subject='%S',]
			[body='%S',]
			[altbody='%S',]
			[ishtml='%S',]
			[mailgroup='%S',]
			[subscriberCount=%I,]
			[finished=FROM_UNIXTIME(%I),]
			[sent=%I,]
			[charset='%S',]
			[status=%I,]
			started=FROM_UNIXTIME(%i)";
		$query = $dbo->prepare($query,@array(
			$in['fromname'],
			$in['fromemail'],
			$in['frombounce'],
			$in['subject'],
			$in['body'],
			$in['altbody'],
			$in['ishtml'],
			$in['group'],
			$in['tally'],
			$in['end'],
			$in['sent'],
			$in['charset'],
			$in['status'],
			$in['start']));
		
		// fetch new subscriber's ID
		$id = $dbo->lastId($query);
		
		if (!$id)
			return false;
		
		// insert current if applicable
		if (!empty($in['status']) && $in['status'] == 1) {
			if(empty($in['code']))
				$in['code'] = PommoHelper::makeCode();
			
			$query = "
			INSERT INTO " . $dbo->table['mailing_current'] . "
			SET
			[command='%S',]
			[serial=%I,]
			[securityCode='%S',]
			[current_status='%S',]
			current_id=%i";
			$query = $dbo->prepare($query,@array(
				$in['command'],
				$in['serial'],
				$in['code'],
				$in['current_status'],
				$id
			));
			if (!$dbo->query($query))
				return false;
			return $in['code'];
		}
			
		return $id;
	}
	
	// removes a mailing from the database
	// accepts a single ID (int) or array of IDs 
	// returns the # of deleted subscribers (int). 0 (false) if none.
	function delete(&$id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			DELETE
			FROM " . $dbo->table['mailings'] . "
			WHERE mailing_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		
		$deleted = $dbo->affected($query);
		
		$query = "
			DELETE
			FROM " . $dbo->table['mailing_current'] . "
			WHERE current_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		$dbo->query($query);
		
		$query = "
			DELETE
			FROM " . $dbo->table['mailing_notices'] . "
			WHERE mailing_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		$dbo->query($query);
		
		return $deleted;
	}
	
	// checks if a mailing is processing
	// returns (bool) - true if current mailing
	function isCurrent() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT count(mailing_id)
			FROM ".$dbo->table['mailings']."
			WHERE status=1";
		return ($dbo->query($query,0) > 0) ? true : false;
	}
	
	// gets the number of mailings
	// returns mailing tally (int)
	function tally() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT count(mailing_id)
			FROM " . $dbo->table['mailings'];
		return ($dbo->query($query,0));
	}
	
	// gets *latest* notices from a mailing
	// accepts mailing ID
	// accepts a limit (def. 50) -- or 0
	// returns an array of notices, if timestamp set to true, array will contain an array of keys that are timestamps, and value is an array of notices
	//   e.g. array('<timestamp>' => array('notice1','notice2'))
	function & getNotices($id,$limit = 50, $timestamp = FALSE) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$limit = intval($limit);
		if($limit == 0)
			$limit = false;
		
		if (!$timestamp) {
		$query = "
			SELECT notice FROM ".$dbo->table['mailing_notices']."
			WHERE mailing_id = %i ORDER BY touched DESC, id DESC [LIMIT %I]";
		$query = $dbo->prepare($query,array($id,$limit));
		return $dbo->getAll($query,'assoc','notice');
		}
		
		$o = array();
		$query = "
			SELECT touched,notice FROM ".$dbo->table['mailing_notices']."
			WHERE mailing_id = %i ORDER BY touched DESC, id DESC [LIMIT %I]";
		$query = $dbo->prepare($query,array($id,$limit));
		while ($row = $dbo->getRows($query)) {
			if (!isset($o[$row['touched']]))
				$o[$row['touched']] = array();
			array_push($o[$row['touched']], $row['notice']);
		}
		return $o;
		
	}
	
	// returns the # of sent mails for a mailing
	function getSent($id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT sent FROM ".$dbo->table['mailings']
				."WHERE mailing_id = %i"; 
		$query = $dbo->prepare($query,array($id));
		return $dbo->query($query,0);
	}
	
	// returns the Subject of a Mailing
	function getSubject($id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT subject FROM ".$dbo->table['mailings']
				."WHERE mailing_id = %i"; 
		$query = $dbo->prepare($query,array($id));
		return $dbo->query($query,0);
	}
}
?>
