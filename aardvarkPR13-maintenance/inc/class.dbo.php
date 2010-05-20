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

// TODO: $sql should always be passed by reference... look @ ways of doing this. ie. see Affected($sql) function
// 

/** 
* Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

/**********************
 * TEXT PROCESSING FUNCTIONS
 **********************/

// decode_htmlchars: removes any htmlspecialchars. PHP4/5 Compatible.
function decode_htmlchars($str, $quote_style = ENT_COMPAT) {
	return strtr($str, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
}

//str2db: Formats input for database insertion. Used on POST/GET data before being inserted to DB.
function str2db(& $string) {
	//if (get_magic_quotes_gpc())
	//	return decode_htmlchars($string);
	return decode_htmlchars(mysql_real_escape_string($string));
}

//db2str: Formats text from DB to be displayed in a browser.
//     Used mainly for printing values from a database or populating form values.
function db2str(& $string) {
	if (get_magic_quotes_runtime())
		return htmlspecialchars(stripslashes($string));
	return htmlspecialchars($string);
}

//db2mail: Formats text from a DB table to be mailed, or viewed in a non browser.
function db2mail(& $string) {
	if (get_magic_quotes_runtime())
		return stripslashes($string);
	return $string;
}

// used to format text being pulled from and reinserted to a database
function db2db(& $string) {
	if (get_magic_quotes_runtime())
		return $string;
	return mysql_real_escape_string($string);
}

// takes an array of input to be sanitized. Type is str (user input), or db (db output) 
function & dbSanitize(& $entryArray, $type = 'str') {
	switch ($type) {
		case 'db' :
			if (!is_array($entryArray))
				return db2db($entryArray);
			foreach (array_keys($entryArray) as $key)
				$entryArray[$key] = db2db($entryArray[$key]);
			return $entryArray;
		case 'str' :
			if (!is_array($entryArray))
				return str2db($entryArray);
			foreach (array_keys($entryArray) as $key)
				$entryArray[$key] = str2db($entryArray[$key]);
			return $entryArray;
	}
	die('Unknown type sent to dbSanitize');
}

/**********************
 * DATABSE CLASS 
 **********************/

// Database Connection Class - holds the link, processes queiries, produces repots, etc.
class dbo {
	var $_link;
	var $_result;
	var $_dieOnQuery;
	var $_debug;
	var $_database; // name of database
	var $table; // array of tables. array_key = nickname, value = table name in DB

	var $_results; // array holding unique results (for use with executing queries within loops & not overwriting the loops conditional resultset)

	function dbo($username = NULL, $password = NULL, $database = NULL, $hostname = NULL, $tablePrefix = NULL) {

		$this->_database = $database;
		$this->table = array ();

		$this->table['config'] = $tablePrefix.'config';
		$this->table['groups'] = $tablePrefix.'groups';
		$this->table['groups_criteria'] = $tablePrefix.'groups_criteria';
		$this->table['mailing_current'] = $tablePrefix.'mailing_current';
		$this->table['mailing_history'] = $tablePrefix.'mailing_history';
		$this->table['queue'] = $tablePrefix.'queue';
		$this->table['queue_working'] = $tablePrefix.'queue_working';
		$this->table['pending'] = $tablePrefix.'pending'; // PHASE OUT (from < PR13.2)
		$this->table['pending_data'] = $tablePrefix.'pending_data'; // PHASE OUT (from < PR13.2)
		$this->table['subscriber_fields'] = $tablePrefix.'subscriber_fields'; // PHASE OUT (from < PR13.2)
		$this->table['subscribers'] = $tablePrefix.'subscribers'; // PHASE OUT (from < PR13.2)
		$this->table['subscribers_data'] = $tablePrefix.'subscribers_data'; // PHASE OUT (from < PR13.2)
		$this->table['subscribers_flagged'] = $tablePrefix.'subscribers_flagged'; // PHASE OUT (from < PR13.2)
		$this->table['updates'] = $tablePrefix.'updates';
		
		$this->table['fields'] = $tablePrefix.'fields';
		
		$this->table['subscribers_active'] = $tablePrefix.'subscribers_active';
		$this->table['subscribers_inactive'] = $tablePrefix.'subscribers_inactive';
		$this->table['subscribers_pending'] = $tablePrefix.'subscribers_pending';
		
		$this->table['data_active'] = $tablePrefix.'data_active';
		$this->table['data_inactive'] = $tablePrefix.'data_inactive';
		$this->table['data_pending'] = $tablePrefix.'data_pending';
		
		

		$this->_dieOnQuery = TRUE;
		$this->_debug = FALSE;
		
		$this->_results = array ();

		// connect to mysql database using config variables from poMMo class (set in setup/config.php). 
		// supress errors to hide login information...
		$this->_link = mysql_connect($hostname, $username, $password);
			
		if (!$this->_link)
			die('<img src="'.bm_baseUrl.'themes/shared/images/icons/alert.png" alt="alert icon" />Could not establish database connection. Verify your config.php settings in the setup directory. See the <a href="'.bm_baseUrl.'docs/readme.html">README</a> file for help.');

		if (!@ mysql_select_db($database, $this->_link))
			die('<img src="'.bm_baseUrl.'themes/shared/images/icons/alert.png" alt="alert icon" />Connected to database server but could not select database: "'.$database.'". Does this database exist? Verify your config.php settings in the setup directory.');
	
	
		// Make sure any results we retrieve or commands we send use the same charset and collation as the database:
		//  code taken from Juliette Reinders Folmer; http://www.adviesenzo.nl/examples/php_mysql_charset_fix/
		//  TODO: Cache the charset?
		$db_charset = mysql_query( "SHOW VARIABLES LIKE 'character_set_database'", $this->_link);
		$charset_row = mysql_fetch_assoc( $db_charset );
		mysql_query( "SET NAMES '" . $charset_row['Value'] . "'", $this->_link );
		unset( $db_charset, $charset_row );

	}

	function debug($val) {
		$this->_debug = $val;
	}

	function dieOnQuery($val) {
		return ($val) ? $this->_dieOnQuery = TRUE : $this->_dieOnQuery = FALSE;
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
	 *      A] query($sql,3) -> returns 4th row of a resultset
	 *      B] query($sql,0,2) -> returns the second column of the first row of a resultset.
	 *   
	 *     A is useful for a result set containing a single column, ie. "SELECT name FROM people";
	 * 
	 * Example invocations from partent script:
	 * 
	 *   $dbo = & $poMMo->_dbo;
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

			// execute query
	$this->_result = mysql_query($query, $this->_link);

		// output debugging info if enabled
		if ($this->_debug) {
			$numRecords = 0;
			// get # of records if a non bool was returned..
			if (!is_bool($this->_result))
				$numRecords = $this->records();
			echo '<br />Query received --> "'.$query.'" <br />Query affected '.$this->affected().' rows and returned '.$numRecords.' records.<br />';
		}

		// check if query was unsuccessful
		if (!$this->_result) {

			if ($this->_debug)
				echo '<p>Query <strong>failed</strong> with error --> '.mysql_error().'</p>';

			if ($this->_dieOnQuery)
				die('MySQL Query Failed.');

			else
				return false;
		}

		if (is_numeric($row))
			return ($this->records()) ? mysql_result($this->_result, $row, $col) : false;

		/*
		// check if specific field requested, return value.
		if (is_numeric($row)) {
			if (is_numeric($col)) {
				return ($this->records()) ? mysql_result($this->_result, $row, $col) : false;
			}
			return mysql_result($this->_result, $row);
		}*/

		// return the result
		return $this->_result;
	}

	function getError() {
		return mysql_error();
	}

	// function affected - returns the amount of affects rows from a INSERT,UPDATE, or DELETE Query.
	// Note, if nothing was changed in an update.. 0 will be returned!
	//    if $sql is passed, a query will be issued. Otherwise examine the saved result.
	function & affected($sql = NULL) {
		if ($sql)
			$this->query($sql);
		$x = mysql_affected_rows($this->_link);
		return ($this->_result) ? mysql_affected_rows($this->_link) : 0;
	}

	// function records - returns the number of rows resultings in a SELECT query -- 0 (false) if none...
	//    if $sql is passed, a query will be issued. Otherwise examine the saved result.
	function & records($sql = NULL) {
		if ($sql)
			$this->query($sql);

		return ($this->_result) ? mysql_num_rows($this->_result) : 0;
	}

	// returns the ID of the pkey from an INSERT Statement
	function & lastId() {
		return mysql_insert_id($this->_link);
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
				'_'.$uniqueId }))
			$this-> {
				'_'.$uniqueId }
			= array ();
			$set = & $this-> {
				'_'.$uniqueId};
		} else
			$set = & $this->_results;

		// stack is empty, push new result set onto stack
		if (empty ($set)) {
			if (!empty ($sql))
				$this->query($sql);
			array_push($set, $this->_result);
		}

		// Fetch row from result set at end of result stack
		 ($enumerated) ? $row = mysql_fetch_row(end($set)) : $row = mysql_fetch_assoc(end($set));

		if ($row)
			return $row;

		// fetching row failed, result set is empty.
		array_pop($set);
		return false;
	}

	// retuns an entire result set as an associative array. pass type 'row' to make it enumerated
	//  if field is given, just that field #/name is added to the array.
	function & getAll($sql = NULL, $type = 'assoc', $field = NULL) {
		$a = array ();
		$type = $type === 'assoc' ? $type : 'row';

		if ($sql)
			$this->query($sql);

		if ($field != NULL) 
			eval ('while(@$r = mysql_fetch_'.$type.'($this->_result)) array_push($a, $r['.$field.']);');
		else
			eval ('while(@$r = mysql_fetch_'.$type.'($this->_result)) array_push($a, $r);');
		return $a;
	}

}
?>