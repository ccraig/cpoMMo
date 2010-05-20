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

// include the subscriber prototype object 
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/prototypes.php');

/**
 * Subscriber: A Subscriber
 * ==SQL Schema==
 *	subscriber_id	(int)			Database ID/Key
 *	email			(str)			Email Address
 *	time_touched	(timestamp)		Date last modified (records changed)
 *	time_registered	(date)			Date registered (signed up)
 *	flag			(enum)			0: NULL, 1-8: REMOVE, 9: UPDATE
 *	ip				(str)			IP (tcp/ip) used to register - stored as INT via INET_ATON()
 *	status			(enum)			0: Inactive, 1: Active, 2: Pending
 *
 * == Additional columns for Pending ==
 *	pending_id		(int)			Database ID/Key
 *	subscriber_id	(int)			Subscriber ID in subscribers table
 *	pending_code	(str)			Code to complete pending request
 *	pending_type	(enum)			'add','del','change','password',NULL (def: null)
 *	pending_array	(str)			Serialized Subscriber object (for update)
 *
 * == Additional Data Columns ==
 *	data_id			(int)			Database ID/Key
 *	field_id		(int)			Field ID in fields table
 *	subscriber_id	(int)			Subscriber ID in subscribers table
 *	value			(str)			Subscriber's field value
 */
	
class PommoSubscriber {
	
	// make a subscriber template
	// accepts a subscriber template (assoc array)
	// accepts a flag (bool) to designate return of a pending subscriber type
	// return a subscriber object (array)
	function & make($in = array(), $pending = FALSE) {
		$o = ($pending) ?
			PommoType::subscriberPending() :
			PommoType::subscriber();
		return PommoAPI::getParams($o, $in);
	}
	
	// make a subscriber template based off a database row (field schema)
	// accepts a subscriber template (assoc array)  
	// accepts a flag (bool) to designate return of a pending subscriber type
	// return a subscriber object (array)
	function & makeDB(&$row, $pending = FALSE) {
		$in = @array(
		'id' => $row['subscriber_id'],
		'email' => $row['email'],
		'touched' => $row['time_touched'],
		'registered' => $row['time_registered'],
		'flag' => $row['flag'],
		'ip' => $row['ip'],
		'status' => $row['status']);
			
		if ($pending) {
			$o = array(
				'pending_code' => $row['pending_code'],
				'pending_array' => $row['pending_array'],
				'pending_type' => $row['pending_type']);
			$in = array_merge($o,$in);
		}

		$o = ($pending) ?
			PommoAPI::getParams(PommoType::subscriberPending(),$in) :
			PommoAPI::getParams(PommoType::subscriber(),$in);
		return $o;
	}
	
	// subscriber validation
	// accepts a subscriber object (array)
	// returns true if subscriber ($in) is valid, false if not
	// NOTE: has the magic functionality of converting english status to bool equiv.
	function validate(&$in) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$invalid = array();

		if (!PommoHelper::isEmail($in['email']))
			$invalid[] = 'email';
		if (!is_numeric($in['registered']))
			$invalid[] = 'registered';
		if (!empty($in['flag']) && !is_numeric($in['flag']))
			$invalid[] = 'flag';
		if (!is_array($in['data']))
			$invalid[] = 'data';
		
		switch($in['status']) {
			case 0:
			case 1:
			case 2:
				break;
			default:
				$invalid[] = 'status';
		}
		
		if ($in['status'] == 2) {
			if(empty($in['pending_code']))
				$invalid[] = 'pending_code';
			switch ($in['pending_type']) {
				case 'add':
				case 'del':
				case 'change':
				case 'password':
					break;
				default:
					$invalid[] = 'pending_type'; 
			}
		}
			
		if (!empty($invalid)) {
			$logger->addErr("Subscriber failed validation on; ".implode(',',$invalid),1);
			return false;
		}
		
		return true;
	}
	
	// fetches subscribers (and their data) from the databse
	// accepts filtering array -->
	//   status (str) [0,1,2,'all'(def)]
	//   email (str||array) Email address(es)
	//   sort (str) [email, ip, time_registered, time_touched, status, etc.]
	//   order (str) "ASC" or "DESC"
	//   limit (int) limits # subscribers returned
	//   offset (int) the SQL offset to start at
	//   id (array||str) A single or an array of subscriber IDs
	// accepts a search array -->
	//   field (str) - the numeric field ID or 'email', 'time_touched', 'time_registered', 'ip, 'subscriber_id'
	//   string (str) - the search query
	// returns an array of subscribers. Array key(s) correlates to subscriber id.
	function & get($p = array(), $search = array('field' => null, 'string' => null)) {
		$defaults = array(
			'status' => 'all', 
			'email' => null, 
			'sort' => null, 
			'order' => null, 
			'limit' => null, 
			'offset' => null, 
			'id' => null);
		$p = PommoAPI :: getParams($defaults, $p);
			
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if ($p['status'] == 'all')
			$p['status'] = null;
			
		if (is_numeric($p['limit']) && !is_numeric($p['offset']))
			$p['offset'] = 0;
		
		$o = array();
		
		$query = "
			SELECT
				s.subscriber_id,
				s.email,
				s.time_touched,
				s.time_registered,
				s.flag,
				INET_NTOA(s.ip) ip,
				s.status,
				p.pending_code,
				p.pending_array,
				p.pending_type".
				
				// if sort is numeric, we're sorting by a field and must grab the field from data table
			    (is_numeric($p['sort']) ? 
			    	", d.value" : 
			    	'').
			    // if searching against a subriber field, we must fetch subscriber data for this field
			    (is_numeric($search['field']) ? 
			    	", search.value" : 
			    	'').

			" FROM ".$dbo->table['subscribers']." s
			LEFT JOIN " . $dbo->table['subscriber_pending']." p ON (s.subscriber_id = p.subscriber_id) ".
			
			// if sort is numeric, we're sorting by a field and must grab the field from data table
			(is_numeric($p['sort']) ?
				"LEFT JOIN (SELECT * FROM " .$dbo->table['subscriber_data'].
					" WHERE field_id = ".(int)($p['sort'])." ) AS d".
					" ON (s.subscriber_id = d.subscriber_id)" : 
				'').
			
			// if searching against a subscriber field, left join the data table
			(is_numeric($search['field']) ?
				"LEFT JOIN (SELECT value FROM " .$dbo->table['subscriber_data'].
					" WHERE field_id = ".(int)($search['field'])." ) AS search".
					" ON (s.subscriber_id = search.subscriber_id)" : 
				'').
			
		  " WHERE
				1
				[AND s.subscriber_id IN(%C)]
				[AND s.status=%I]
				[AND s.email IN (%Q)]
				[AND %S LIKE '%%S%']
				[ORDER BY %S] [%S]
				[LIMIT %I, %I]";
		
		// Check if we're sorting against a field.
		//   If so, sort against the "value" column select.
		//   If it's a numeric field, cast the value (string) as an Integer by the DBE for proper sorting.
		if(is_numeric($p['sort'])) {
			Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
			$numericFields = PommoField::getByType(array('date','number'));
			
			$p['sort'] = (in_array($p['sort'],$numericFields)) ?
				'CAST(value as SIGNED)' :
				'value';
		}
		
		
		// If we're searching/filtering, generate the proper SQL
		$searchSQL = NULL;
		if(!empty($search['field']) && !empty($search['string'])) {
			
			// make MySQL LIKE() compliant
			$search['string'] = addcslashes($search['string'],'%_');
			
			$search['field'] = is_numeric($search['field']) ? 
				'search.value' :
				's.'.$search['field'];
		}
			
		$query = $dbo->prepare($query,array($p['id'],$p['status'], $p['email'], $search['field'], $search['string'], $p['sort'], $p['order'], $p['offset'], $p['limit']));
		while ($row = $dbo->getRows($query)) 
			$o[$row['subscriber_id']] = (empty($row['pending_code'])) ?
				PommoSubscriber::makeDB($row) :
				PommoSubscriber::makeDB($row, TRUE);
		
		// fetch data
		if (!empty($o)) {
			
			// get any date fields for conversion. We can't use the MySQL 4.1/5
			// engine, as it doesn't support negative timestamps... !!!
			Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
			$dates = PommoField::getByType('date');
			
			$query = "
				SELECT
					field_id,
					value,
					subscriber_id
				FROM
					" . $dbo->table['subscriber_data']."
				WHERE
					subscriber_id IN(%c)";
			$query = $dbo->prepare($query,array(array_keys($o)));	
			while ($row = $dbo->getRows($query)) {
				$o[$row['subscriber_id']]['data'][$row['field_id']] = (in_array($row['field_id'],$dates)) ?
					PommoHelper::timeToStr($row['value']) :
					$row['value'];
			}
		}
		return $o;
	}
	
	// fetches subscriber emails from the databse
	// accepts filtering array -->
	//   status (str) [0,1,2,'all'(def)]
	//   id (array||str) A single or an array of subscriber IDs
	//   limit (int) limits # subscribers returned
	// returns an array of emails. Array key(s) correlates to subscriber id.
	function & getEmail($p = array()) {
		$defaults = array('status' => 'all', 'id' => null);
		$p = PommoAPI :: getParams($defaults, $p);
		
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if ($p['status'] == 'all')
			$p['status'] = null;

		$o = array();
		
		$query = "
			SELECT
				subscriber_id,
				email
			FROM 
				" . $dbo->table['subscribers']."
			WHERE
				1
				[AND subscriber_id IN(%C)]
				[AND status=%I]";
		$query = $dbo->prepare($query,array($p['id'],$p['status']));
		
		while ($row = $dbo->getRows($query)) 
			$o[$row['subscriber_id']] = $row['email'];
		
		return $o;
	}
	
	// fetches subscriber IDs from passed emails
	// accepts a email address (str) or array of email addresses
	// accepts filtering by subscriber status, or all statuses if not supplied
	// returns an array of subscriber IDs
	function & getIDByEmail($email, $status = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT subscriber_id
			FROM " . $dbo->table['subscribers'] . "
			WHERE email IN (%q)
			[AND status IN (%C)]";
		$query = $dbo->prepare($query,array($email,$status));
		return $dbo->getAll($query, 'assoc', 'subscriber_id');
	}
	
	// fetches subscribers from the database based off their attributes
	// accepts a attribute filtering array. 
	//   array_key == filter table (subscriber_pending, subscriber_data, subscribers)
	//   array_value == array column
	//  Returns an array of subscriber IDs
	/** EXAMPLE
	array(
		'subscriber_pending' => array(
			'pending_code' => array("not: 'abc1234'", "is: 'def123'", "is: '2234'"),
			'pending_email' => array('not: NULL')),
		'subscriber_data' => array(
			12 => array("not: 'Milwaukee'"), // 12 is alias for field_id=12 ...
			15 => array("greater: 15")),
		'subscribers' => array(
			'email' => "not: 'bhb@iceburg.net'"),
			'status' => "equal: active"
		);
		LEGAL LOGIC: (not|is|less|greater|true|false|equal)
	*/
	function & getIDByAttr($f = array('subscriber_pending' => array(), 'subscriber_data' => array(), 'subscribers' => array())) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		$pommo->requireOnce($pommo->_baseDir.'inc/classes/sql.gen.php');
		
		$sql = array('where' => array(), 'join' => array());
		
		if (!empty($f['subscribers']))
			$sql = array_merge_recursive($sql, PommoSQL::fromFilter($f['subscribers'],'s'));
		
		if (!empty($f['subscriber_data']))
			$sql = array_merge_recursive($sql, PommoSQL::fromFilter($f['subscriber_data'],'d'));
		
		$p = null;
		if (!empty($f['subscriber_pending'])) {
			$p = 'p';
			$sql = array_merge_recursive($sql, PommoSQL::fromFilter($f['subscriber_pending'],'p'));
		}
		
		$joins = implode(' ',$sql['join']);
		$where = implode(' ',$sql['where']);
		
		$query = "
			SELECT DISTINCT s.subscriber_id
			FROM ". $dbo->table['subscribers']." s
			[LEFT JOIN ". $dbo->table['subscriber_pending']." %S
				ON (s.subscriber_id = p.subscriber_id)]
			".$joins."
			WHERE 1 ".$where;
		$query = $dbo->prepare($query,array($p));
		die($query);
		return $dbo->getAll($query, 'assoc', 'subscriber_id');
	}
	
	
	// adds a subscriber to the database
	// accepts a subscriber (array)
	// accepts a ID (int typically 0) if set forces the added subscriber ID to a key, ovverriding row
	// returns the database ID of the added field or FALSE if failed
	
	// TODO -> potentially use the REPLACE INTO of this function
	//  as a means for UPDATE?
	function add(&$in, $id = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		// set the registration date if not provided
		if (empty($in['registered']))
			$in['registered'] = time();

		if (!PommoSubscriber::validate($in))
			return false;
		
		$insert = ($id === null) ? 'INSERT' : 'REPLACE';
		$query = "
			$insert INTO " . $dbo->table['subscribers'] . "
			SET
			[subscriber_id=%I,]
			email='%s',
			time_registered=FROM_UNIXTIME(%i),
			flag=%i,
			ip=INET_ATON('%s'),
			status=%i";
		$query = $dbo->prepare($query,@array(
			$id,
			$in['email'],
			$in['registered'],
			$in['flag'],
			$in['ip'],
			$in['status']
		));
		
		// fetch new subscriber's ID
		$id = $dbo->lastId($query);
		
		if (!$id)
			return false;
		
		// insert pending (if exists)
		if ($in['status'] == 2) {
			$query = "
			INSERT INTO " . $dbo->table['subscriber_pending'] . "
			SET
			[pending_array='%S',]
			subscriber_id=%i,
			pending_code='%s',
			pending_type='%s'";
			$query = $dbo->prepare($query,@array(
				$in['pending_array'],
				$id,
				$in['pending_code'],
				$in['pending_type']
			));
			if (!$dbo->query($query))
				return false;
		}
		
		// insert data
		$values = array();
		foreach ($in['data'] as $field_id => $value)
			$values[] = $dbo->prepare("(%i,%i,'%s')",array($field_id,$id,$value));
			
		if (!empty($values)) {
			$query = "
			INSERT INTO " . $dbo->table['subscriber_data'] . "
			(field_id, subscriber_id, value)
			VALUES ".implode(',', $values);
			if (!$dbo->query($query))
				return false;
		}
			
		return $id;
	}
	
	// removes a subscriber from the database
	// accepts a single ID (int) or array of IDs 
	// returns the # of deleted subscribers (int). 0 (false) if none.
	function delete(&$id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			DELETE
			FROM " . $dbo->table['subscribers'] . "
			WHERE subscriber_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		
		$deleted = $dbo->affected($query);
		
		$query = "
			DELETE
			FROM " . $dbo->table['subscriber_pending'] . "
			WHERE subscriber_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		$dbo->query($query);
		
		$query = "
			DELETE
			FROM " . $dbo->table['subscriber_data'] . "
			WHERE subscriber_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		$dbo->query($query);
		
		return $deleted;
	}
	
	// updates a subscriber in the database
	// accepts a subscriber (array)
	// accepts a mode;
	// 		REPLACE_ALL =  Removes all assosiated subscriber field data and replaces with passed Data
	//		REPLACE_ACTIVE = Removes active (non hidden) subscriber field data and replaces with passed data
	//		REPLACE_PASSED = [default] Removes passed subscriber fields and replaces them.
	//		
	// accepts a toggle; TRUE (default) => ALL subscriber_data for this subscriber will be replaced,
	//   FALSE => only passed data will be replaced
	// returns success (bool)
	// NOTE: The passed subscriber field will overwrites all subscriber info 
	//   (including values in subscriber_pending/subscriber_data). Make sure to pass
	//   the entire subscriber!
	// Does not change the subscriber_id -->  paves the path to add manually assign subs to a group?
	function update(&$in, $mode = 'REPLACE_PASSED') {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			UPDATE " . $dbo->table['subscribers'] . "
			SET
			[email='%S',]
			[time_registered='%S',]
			[ip=INET_ATON('%S'),]
			[status=%I,]
			[flag=%I,]
			time_touched=CURRENT_TIMESTAMP
			WHERE subscriber_id=%i";
		$query = $dbo->prepare($query,@array(
			$in['email'],
			$in['registered'],
			$in['ip'],
			$in['status'],
			$in['flag'],
			$in['id']
		));
		if (!$dbo->query($query) || ($dbo->affected() != 1))
				return false;
		
		
		if (!empty($in['data']) || $mode == 'REPLACE_ALL') {

			switch ($mode) {
				case "REPLACE_ACTIVE":
					Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
					$fields = PommoField::get(array('active' => TRUE));
					$select = array_keys($fields);
					break;
				
				case "REPLACE_ALL":
					$select = NULL;
					break;
					
				case "REPLACE_PASSED":
				default: 
					$select = array_keys($in['data']);	
					break;
			}
		
			$query = "
				DELETE
				FROM " . $dbo->table['subscriber_data'] . "
				WHERE subscriber_id=%i
				[AND field_id IN (%C)]";
			$query = $dbo->prepare($query,array($in['id'], $select));
			if (!$dbo->query($query))
					return false;
		}
		
		$values = array();
		foreach ($in['data'] as $field_id => $value)
			if (!empty($value))
				$values[] = $dbo->prepare("(%i,%i,'%s')",array($field_id,$in['id'],$value));
			
		if (!empty($values)) {
			$query = "
			INSERT INTO " . $dbo->table['subscriber_data'] . "
			(field_id, subscriber_id, value)
			VALUES ".implode(',', $values);
			if (!$dbo->query($query))
				return false;
		}
		
		return true;
	}
	
	// flags subscribers to update their records
	// accepts a single ID (int) or array of IDs 
	// returns the # of subscribers successfully flagged (int). 0 (false) if none.
	function flagByID(&$id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			UPDATE " . $dbo->table['subscribers'] ."
			SET flag=9
			WHERE subscriber_id IN (%q)";
		$query = $dbo->prepare($query,array($id));
		
		return $dbo->affected($query);
	}
	
	// gets the number of subscribers
	// accepts filter by status (str) either either 1 (active) (default), 0 (inactive), 2 (pending) or 'all'/NULL (any/all)
	// returns subscriber tally (int)
	function tally($status = 1) {
		global $pommo;
		$dbo =& $pommo->_dbo;

		if ($status === 'all') 
			$status = null;
			
		$query = "
			SELECT count(subscriber_id)
			FROM " . $dbo->table['subscribers'] ."
			[WHERE status=%I]";
		$query=$dbo->prepare($query,array($status));
		return ($dbo->query($query,0));
	}
	
	// returns the activation code for the passed subscriber
	function getActCode($subscriber){
		if (empty($subscriber['id']) || empty($subscriber['registered']))
			Pommo::kill('Invalid Subscriber passed to getActCode!');
			
		return md5($subscriber['id'].$subscriber['registered']);
	}
}
?>
