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

// TODO: $sql should always be passed by reference... look @ ways of doing this. ie. see Affected($sql) function
//   Write better documentation &/or change to ezSQL ;)

/**********************
 * BRICE'S DATABSE CLASS 
 **********************/

// Database Connection Class - holds the link, processes queiries, produces repots, etc.
class PommoDB {
	var $_link;
	var $_result;
	var $_dieOnQuery;
	var $_debug;
	var $_database; // name of database
	var $_prefix; // table prefix
	var $table; // array of tables. array_key = nickname, value = table name in DB

	var $_safeSQL; // holds Monte's SafeSQL Class , referenced via prepare()
	var $_results; // array holding unique results (for use with executing queries within loops & not overwriting the loops conditional resultset)

	function PommoDB($username = NULL, $password = NULL, $database = NULL, $hostname = NULL, $tablePrefix = NULL) {

		// turn off magic quotes runtime
		if (get_magic_quotes_runtime())
			if (!set_magic_quotes_runtime(0))
				Pommo::kill('Could not turn off PHP\'s magic_quotes_runtime');
				
		$this->_prefix = $tablePrefix;
		$this->_database = $database;
		$this->table = array (
			'config' => '`'.$tablePrefix.'config`',
			'fields' => '`'.$tablePrefix.'fields`',
			'group_rules' => '`'.$tablePrefix.'group_rules`',
			'groups' => '`'.$tablePrefix.'groups`',
			'mailing_notices' => '`'.$tablePrefix.'mailing_notices`',
			'mailing_current' => '`'.$tablePrefix.'mailing_current`',
			'mailings' => '`'.$tablePrefix.'mailings`',
			'scratch' => '`'.$tablePrefix.'scratch`',
			'subscriber_data' => '`'.$tablePrefix.'subscriber_data`',
			'subscriber_pending' => '`'.$tablePrefix.'subscriber_pending`',
			'subscriber_update' => '`'.$tablePrefix.'subscriber_update`', // PHASE OUT (PR15)
			'subscribers' => '`'.$tablePrefix.'subscribers`',
			'templates' => '`'.$tablePrefix.'templates`',
			'queue' => '`'.$tablePrefix.'queue`',
			'updates' => '`'.$tablePrefix.'updates`');		

		$this->_dieOnQuery = TRUE;
		$this->_debug = FALSE;

		$this->_results = array ();

		// connect to mysql database using config variables from poMMo class (set in setup/config.php). 
		// supress errors to hide login information...
		$this->_link = mysql_connect($hostname, $username, $password);

		if (!$this->_link)
			Pommo::kill(Pommo::_T('Could not establish database connection.').' '.Pommo::_T('Verify your settings in config.php'));

		if (!@ mysql_select_db($database, $this->_link))
			Pommo::kill(sprintf(Pommo::_T('Connected to database server but could not select database (%s). Does it exist?'),$database).' '.Pommo::_T('Verify your settings in config.php'));

		// Make sure any results we retrieve or commands we send use the same charset and collation as the database:
		//  code taken from Juliette Reinders Folmer; http://www.adviesenzo.nl/examples/php_mysql_charset_fix/
		//  TODO: Cache the charset?
		$db_charset = mysql_query("SHOW VARIABLES LIKE 'character_set_database'", $this->_link);
		$charset_row = mysql_fetch_assoc($db_charset);
		mysql_query("SET NAMES '" . $charset_row['Value'] . "'", $this->_link);
		unset ($db_charset, $charset_row);
		
		// setup safeSQL class
		$this->_safeSQL = new SafeSQL_MySQL($this->_link);
	}

	function debug($val) {
		$this->_debug = $val;
	}

	function dieOnQuery($val) {
		if (is_bool($val))
			return $this->_dieOnQuery = $val;
		return false;
	}

	// alias to SafeSQL->Query(); See inc/lib/safesql/README for usage
	//  if second parameter is not passed, pass a blank one (avoids safeSQL throwing a warning)
	function prepare() {
		$a = func_get_args();
		if (count($a) < 2)
			$a[] = array();
		return call_user_func_array( array($this->_safeSQL,'query'), $a );
	}

	/** query function. 
	 * 
	 * Returns true if the query was successful, or false if not*. 
	 *    * If $this->_dieOnQuery is set, a die() will be issued and the script halted. If _debug is set,
	 *       the mysql_error will be appended to the die() call. _dieOnQuery is enabled by default.
	 * 
	 * If query is called with numeric arguments, a specific field is returned. This is useful for
	 * SQL statements that return a single row, or multiple rows of a single column. 
	 *   ex. 
	 *      A] query($sql,3) -> returns 4th column of a resultset
	 *      B] query($sql,0,2) -> returns the second column of the first row of a resultset.
	 *   
	 *     A is useful for a result set containing a single column, ie. "SELECT name FROM people";
	 * 
	 * Example invocations from partent script:
	 * 
	 *   $dbo = & $pommo->_dbo;
	 *   $dbo->dieOnQuery(TRUE);
	 *   $dbo->debug(TRUE);
	 * 
	 *   $sql = "SOME SQL QUERY";
	 *   if ($DB->query($sql)) {
	 *   while ($row = mysql_fetch_assoc($dbo->_result)) { echo $row[fieldname]; }
	 *   }
	 *   
	 *  $dbo->dieOnQuery(FALSE);
	 * 
	 *   $firstname = $dbo->query(SELECT phone,name FROM users,0,2);
	 *   if (!$firstname) { echo "INVALID QUERY"; }
	 *  
	 *   $numRowsInSet = $dbo->records()
	 *   $numRecordsChanged = $dbo->affected()
	 * 
	 *  :: EXAMPLE OF ITERATING THROUGH A RESULT SET (ROWS) ::
	 * 
	 * $sql = "SELECT name FROM users WHERE group='X'";
	 * 	while ($row = $dbo->getRows($sql)) {
	 *   	$sql = "UPDATE name SET group='Y' WHERE config_name='".$row['name']."'";
	 * 			if (!$dbo->query($sql))
	 * 				die('Error updating group for '.$row['name']);	
	 * 		}
	 * 	}
	 */

	function & query(& $query, $row = NULL, $col = NULL) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		// execute query
		$this->_result = mysql_query($query, $this->_link);

		// output debugging info if enabled
		if ($this->_debug) {
			$numRecords = 0;
			// get # of records if a non bool was returned..
			if (!is_bool($this->_result))
				$numRecords = $this->records();
		
			$logger->addMsg('[DB] Received query affecting '.$this->affected().' rows and returning '.$numRecords.' results. Query: '.$query);
		}

		// check if query was unsuccessful
		if (!$this->_result) {
			if ($this->_debug)
				$logger->addMsg('Query failed with error --> ' . mysql_error()); 

			if ($this->_dieOnQuery)
				Pommo::kill('MySQL Query Failed.'.$query);
		}
		
		if (is_numeric($row)) {
			$this->_result = ($this->records() === 0) ? false :
				mysql_result($this->_result, $row, $col);
		}
		
		// return the result
		return $this->_result;
	}

	function getError() {
		return mysql_error();
	}

	// function affected - returns the amount of affects rows from a INSERT,UPDATE, or DELETE Query.
	// Note, if nothing was changed in an update.. 0 will be returned!
	//    if $sql is passed, a query will be issued. Otherwise examine the saved result.
	function affected($sql = NULL) {
		if ($sql)
			$this->query($sql);
			
		return ($this->_result) ? mysql_affected_rows($this->_link) : 0;
	}

	// function records - returns the number of rows resultings in a SELECT query -- 0 (false) if none...
	//    if $sql is passed, a query will be issued. Otherwise examine the saved result.
	function records($sql = NULL) {
		if ($sql)
			$this->query($sql);
			
		return ($this->_result) ? mysql_num_rows($this->_result) : 0;
	}

	// returns the ID of the pkey from an INSERT Statement FALSE if bad result
	function lastId($sql = NULL) {
		if ($sql)
			$this->query($sql);
			
		return ($this->_result) ? mysql_insert_id($this->_link) : false;
	}

	// closes the mySql link & frees the resources
	function close() {
		mysql_free($this->_link);
		mysql_close($this->_link);
	}

	// returns an array representing 1 row in a resultset. Returns an assosiative array by default
	//  this function pushes its result on a seperate stack, and is therefore intended for LOOPs (while) ONLY

	/* useful for stuff like; 
		while ($row = $dbo->getRows($sql)) 
				$config[$row['config_name']] = $row['config_value'];
	*/

	function & getRows($sql = NULL, $enumerated = FALSE, $uniqueId = NULL) {

		// check for uniqueIdentifier (used to seperate result stacks when embedding loops within loops)
		if ($uniqueId) {
			if (!isset ($this-> {
				'_' . $uniqueId }))
			$this-> {
				'_' . $uniqueId }
			= array ();
			$set = & $this-> {
				'_' . $uniqueId };
		} else
			$set = & $this->_results;

		// stack is empty, push new result set onto stack
		if (empty ($set)) {
			if (!empty ($sql))
				if ($this->query($sql))
					array_push($set, $this->_result);
				else {
					$set = false;
					return $set;
				}
		}
		
		// Fetch row from result set at end of result stack
		 ($enumerated) ? $row = mysql_fetch_row(end($set)) : $row = mysql_fetch_assoc(end($set));

		if (!$row)
			array_pop($set); // fetching row failed, result set is empty.
		
		return $row;
	}

	// retuns an entire result set as an associative array. pass type 'row' to make it enumerated
	//  if field is given, just that field #/name is added to the array.
	function & getAll($sql = NULL, $type = 'assoc', $field = NULL) {
		$a = array ();
		$type = $type === 'assoc' ? $type : 'row';

		if ($sql)
			$this->query($sql);

		if ($field != NULL)
			eval ('while(@$r = mysql_fetch_' . $type . '($this->_result)) array_push($a, $r[\'' . $field . '\']);');
		else
			eval ('while(@$r = mysql_fetch_' . $type . '($this->_result)) array_push($a, $r);');
		return $a;
	}

}
?>