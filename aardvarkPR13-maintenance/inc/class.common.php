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

/** 
 * Common class. Holds Configuration values, authentication state, etc.. (revived from session)
*/

class Common {
	
	var $_config; // configuration array to hold values loaded from the DB
	var $_authenticated; // TRUE if user has successfully logged on.
	var $_data; // Used to hold temporary data (such as an uploaded file's contents).. accessed via set (sets), get (returns), clear(deletes)
	var $_state; // Used to hold the state of pages -- e.g. variables that should be stored like 'limit, order, etc'
	var $_logger; // holds the logger class object
	var $_dbo; // the database object
	
	// default constructor
	function Common() {
		$this->_config = array ();
		
		if (empty($_SESSION['pommo']['authenticated'])) {
			$_SESSION['pommo']['authenticated'] = FALSE;
		}
		$this->_authenticated = & $_SESSION['pommo']['authenticated'];
		
		if (empty($_SESSION['pommo']['data'])) {
			$_SESSION['pommo']['data'] = array();
		}
		$this->_data = & $_SESSION['pommo']['data'];
		
		// initialize logger
		$this->_logger = new bmLogger(); // NOTE -> this clears messages that may have been retained (not outputted) from logger.
		
		// initialize database object
		global $bmdb;
		$this->_dbo = new dbo($bmdb['username'], $bmdb['password'], $bmdb['database'], $bmdb['hostname'], $bmdb['prefix']);
		
		// if debugging is set in config.php, enable debugging on the database.
		if (bm_debug == 'on') {
			$this->_dbo->debug(TRUE);
		}
	}

	// Loads configuration data from SESSION. If optional argument is supplied, configuration will be loaded from
	// the database & stored in SESSION.
	
	// NOTE: must be called after a proper session_start
	function loadConfig($fromDB = FALSE) {
		
		// if fromDB is passed, or config data is not in SESSION, attempt to load.
		if ($fromDB || empty($_SESSION['pommo']['config'])) {
			
			$_SESSION['pommo']['config'] = array();
			$dbo = & $this->_dbo;
			
			$dbo->dieOnQuery(FALSE);	
			$sql = 'SELECT * FROM '.$dbo->table['config'].' WHERE autoload=\'on\'';
			if ($dbo->query($sql)) {
				while ($row = mysql_fetch_assoc($dbo->_result))
					$_SESSION['pommo']['config'][$row['config_name']] = $row['config_value'];
			}
			$dbo->dieOnQUery(TRUE);		
		}
		
		$this->_config = & $_SESSION['pommo']['config'];
		
		return (!empty ($this->_config['version'])) ? true : bmKill('poMMo does not appear to be set up.' .
					'Have you <a href="'.bm_baseUrl.'install/install.php">Installed?</a>');
	}
	
	// Gets specified config value(s) from the DB. 
	// Pass a single or array of config_names, returns array of their name>value.
	function getConfig($arg) {
		$dbo = & $this->_dbo;
		$dbo->dieOnQuery(FALSE);
		if (!is_array($arg))
			$arg = array($arg);
			
		$config = array();
		if ($arg[0] == 'all')
			$sql = 'SELECT config_name,config_value FROM '.$dbo->table['config'];
		else
			$sql = 'SELECT config_name,config_value FROM '.$dbo->table['config'].' WHERE config_name IN (\''.implode('\',\'',$arg).'\')';
		
		while ($row = $dbo->getRows($sql)) 
				$config[$row['config_name']] = $row['config_value'];
	
		$dbo->dieOnQUery(TRUE);
		return $config;
	}

	// Check if user has sucessfully logged on.
	function isAuthenticated() {
		return ($this->_authenticated) ? true : false;
	}

	// Set's authentication variable. TRUE = authenticated, FALSE/NULL = NOT... 
	// NOTE: must be called after proper session_start()
	// $this->_authenticated references $_SESSION['pommo']['authenticated'] in class constructor
	function setAuthenticated($var) {
		return ($this->_authenticated = $var) ? true : false;
	}


	// deletes stored data in SESSION [not authentication state or config values]
	function clear() {
		return ($this->_data = array()) ? true : false;
	}
	
	// merges data into SESSION ($this->_data references $_SESSION['pommo']['data'] in class constructor)
	function set($value) {
		if (!is_array($value))
			$value = array($value);
		return (empty($this->_data)) ? $this->_data = $value : $this->_data = array_merge($this->_data,$value);
	}
	
	function &get($name = NULL) {
		if ($name) {
			return (empty($this->_data[$name])) ? false : $this->_data[$name];
		}
		return $this->_data;
	}
	
	function stateInit($name = 'default', $state = array()) {
		if (empty($_SESSION['state_'.$name])) {
			$_SESSION['state_'.$name] = $state;
		}
		$this->_state =& $_SESSION['state_'.$name];
		return;
	}
	
	// used to access or set state Vars
	// TODO -> remove str2db (dbSanitize) when queries are made safe by DB abstraction class
	function stateVar($varName, $varValue = NULL) {
		if (!empty($varValue)) {
			$this->_state[$varName] = dbSanitize($varValue);
		}
		return (isset($this->_state[$varName])) ? $this->_state[$varName] : false;
	}
}
?>