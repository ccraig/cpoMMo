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

/** 
 * Common class. Holds Configuration values, authentication state, etc.. (revived from session)
*/

class Pommo {
	var $_revision = 42; // poMMo's current "file" revision #

	var $_dbo; // holds the database object
	var $_logger; // holds the logger (messaging) object
	var $_auth; // holds the authentication object
	var $_escaping; // (bool) if true, responses from logger and translation functions will be wrapped through htmlspecialchars

	var $_baseDir; // poMMo's base directory (e.g. /home/www/site1/pommo/)
	var $_baseUrl; // poMMo's base URL (e.g. http://www.site1.com/pommo/) - null = autodetect
	var $_workDir; // poMMo's working (writable) directory (e.g. /home/www/site1/pommo/cache/)
	var $_hostname; // WebServer hostname (e.g. www.site1.com) - null = autodetect
	var $_hostport; // WebServer port (e.g. 80) - null = autodetect
	var $_ssl; // bool - true if accessed via HTTPS
	var $_http; // the "http(s)://hostname(:port)" full connection string
	var $_language; // language to translate to (via Pommo::_T())
	var $_slanguage; // the "session" language (if set)
	var $_debug; // debug status (bool)
	var $_verbosity; // logging + debugging verbosity (1(most)-3(less|default))
	var $_dateformat; // prefered; 1: YYYY/MM/DD, 2: MM/DD/YYYY, 3: DD/MM/YYYY
	var $_default_subscriber_sort; // what column (or field_id) should be used to sort the subscribers when managing them?

	var $_config; // configuration array to hold values loaded from the DB
	var $_session;  // pointer to this install's/instance values in $_SESSION
	
	// default constructor
	function Pommo($baseDir) {
		$this->_baseDir = $baseDir;
		$this->_config = array ();
		$this->_auth = null;
		$this->_escaping = false;
	}

	// preInit() populates poMMo's core with values from config.php 
	//  initializes the logger + database
	function preInit() {
		Pommo::requireOnce($this->_baseDir . 'inc/classes/log.php');
		Pommo::requireOnce($this->_baseDir . 'inc/lib/safesql/SafeSQL.class.php');
		Pommo::requireOnce($this->_baseDir . 'inc/classes/db.php');
		Pommo::requireOnce($this->_baseDir . 'inc/classes/auth.php');
		
		// initialize logger
		$this->_logger = new PommoLog(); // NOTE -> this clears messages that may have been retained (not outputted) from logger.
		
		// read in config.php (configured by user)
		// TODO -> write a web-based frontend to config.php creation
		$config = PommoHelper::parseConfig($this->_baseDir . 'config.php');
		
		// check to see if config.php was "properly" loaded
		if (count($config) < 5)
			Pommo::kill('Could not read config.php');

		$this->_workDir = (empty($config['workDir'])) ? $this->_baseDir . 'cache' : $config['workDir'];
		$this->_debug = (strtolower($config['debug']) != 'on') ? false : true; 
		$this->_default_subscriber_sort = (empty($config['default_subscriber_sort'])) ? 'email' : $config['default_subscriber_sort'];
		$this->_verbosity = (empty($config['verbosity'])) ? 3 : $config['verbosity'];
		$this->_logger->_verbosity = $this->_verbosity;
		$this->_dateformat = ($config['date_format'] >= 1 && $cofig['date_format'] <= 3) ?
			intval($config['date_format']) : 1;
		
		// the regex strips port info from hostname
		$this->_hostname = (empty($config['hostname'])) ? preg_replace('/:\d+$/i', '', $_SERVER['HTTP_HOST']) : $config['hostname'];
		$this->_hostport = (empty($config['hostport'])) ? $_SERVER['SERVER_PORT'] : $config['hostport'];
		$this->_ssl = (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on') ? false : true;
		$this->_http = (($this->_ssl) ? 'https://' : 'http://') . $this->_hostname;
		if ($this->_hostport != 80 && $this->_hostport != 443)
			$this->_http .= ':'.$this->_hostport;
			
		$this->_language = (empty($config['lang'])) ? 'en' : strtolower($config['lang']);
		$this->_slanguage = (defined('_poMMo_lang')) ? _poMMo_lang : false;
		
		// include translation (l10n) methods if language is not English
		$this->_l10n = FALSE;
		if ($this->_language != 'en') {
			$this->_l10n = TRUE;
			Pommo::requireOnce($this->_baseDir . 'inc/helpers/l10n.php');
			PommoHelperL10n::init($this->_language, $this->_baseDir);
		}
		
		// set base URL (e.g. http://mysite.com/news/pommo => 'news/pommo/')
		// TODO -> provide validation of baseURL ?
		if (isset ($config['baseURL'])) {
			$this->_baseUrl = $config['baseURL'];
		} else {
			// If we're called from an outside (embedded) script, read baseURL from "last known good".
			// Else, set it based off of REQUEST
			if (defined('_poMMo_embed')) {
				Pommo::requireOnce($this->_baseDir . 'inc/helpers/maintenance.php');
				$this->_baseUrl = PommoHelperMaintenance :: rememberBaseURL();
			} else {
				$baseUrl = preg_replace('@/(inc|setup|user|install|support(/tests)?|admin(/subscribers|/user|/mailings|/setup)?(/ajax|/mailing|/config)?)$@i', '', dirname($_SERVER['PHP_SELF']));
				$this->_baseUrl = ($baseUrl == '/') ? $baseUrl : $baseUrl . '/';
			}
		}
		
		
		// make sure workDir is writable
		if (!is_dir($this->_workDir . '/pommo/smarty')) {
				
			$wd = $this->_workDir; $this->_workDir = null;
			if (!is_dir($wd))
				Pommo::kill(sprintf(Pommo::_T('Work Directory (%s) not found! Make sure it exists and the webserver can write to it. You can change its location from the config.php file.'), $wd));
			if (!is_writable($wd))
				Pommo::kill(sprintf(Pommo::_T('Cannot write to Work Directory (%s). Make sure it has the proper permissions.'), $wd));
			if (ini_get('safe_mode') == "1")
				Pommo::kill(sprintf(Pommo::_T('Working Directory (%s) cannot be created under PHP SAFE MODE. See Documentation, or disable SAFE MODE.'), $wd));
			if (!is_dir($wd . '/pommo'))
				if (!mkdir($wd . '/pommo'))
					Pommo::kill(Pommo::_T('Could not create directory') . ' ' . $wd . '/pommo');
			if (!mkdir($wd . '/pommo/smarty'))
				Pommo::kill(Pommo::_T('Could not create directory') . ' ' . $wd . '/pommo/smarty');
			$this->_workdir = $wd;
		}

		// set the current "section" -- should be "user" for /user/* files, "mailings" for /admin/mailings/* files, etc. etc.
		$this->_section = preg_replace('@^admin/?@i', '', str_replace($this->_baseUrl, '', dirname($_SERVER['PHP_SELF'])));
		
		// initialize database link
		$this->_dbo = @new PommoDB($config['db_username'], $config['db_password'], $config['db_database'], $config['db_hostname'], $config['db_prefix']);

		// turn off debugging if in user area
		if($this->_section == 'user') {
			$this->_debug = false;
			$this->_dbo->debug(FALSE);
		}
		
		// if debugging is set in config.php, enable debugging on the database.
		if ($this->_debug)  {
			
			// don't enable debugging in ajax requests unless verbosity is < 3 
			if (PommoHelper::isAjax() && $this->_verbosity > 2)
				$this->_debug = false;
			else
				$this->_dbo->debug(TRUE);
		}

	}

	/** 
	 * init -> called by page to load page state, populate config, and track authentication. 
	 *	valid args [ passed as Pommo::init(array('arg' => val, 'arg2' => val)) ] ->
	 *		authLevel	:	check that authenticated permission level is at least authLevel (non authenticated == 0). exit if not high enough. [default: 1]
	 *		keep		:	keep data stored in session. [default: false]
	 *		session		:	explicity set session name. [default: null]
	 * 		install		:	bypass loading of config/version checking [default: false]
     */

	function init($args = array ()) {
		
		$defaults = array (
			'authLevel' => 1,
			'keep' => FALSE,
			'noSession' => FALSE,
			'sessionID' => NULL,
			'install' => FALSE
		);
	
		// merge submitted parameters
		$p = PommoAPI :: getParams($defaults, $args);
		
		// Bypass Reading of Config, SESSION creation, and authentication checks and return
		//  if 'install' passed
		if ($p['install'])
			return;
			
		// load configuration data
		// note; cannot save in session, as session needs unique key -- this is simplest method.
		$this->_config = PommoAPI::configGetBase();
		
		// check current ("file") revision against database ("last") revision
		$revision = isset($this->_config['revision']) ?
			$this->_config['revision'] :
			false;
		
		if(!defined('_poMMo_support'))
			if (!$revision)
				$this->kill(sprintf(Pommo :: _T('Error loading configuration. Has poMMo been installed? %sClick Here%s to install.'), '<a href="' . $this->_baseUrl . 'install/install.php">', '</a>'));
			elseif ($this->_revision != $revision) $this->kill(sprintf(Pommo :: _T('Version Mismatch. %sClick Here%s to upgrade.'), '<a href="' . $this->_baseUrl . 'install/upgrade.php">', '</a>'));
		
		// toggle DB debugging
		if ($this->_debug)
			$this->_dbo->debug(TRUE);
		
		// Bypass SESSION creation, reading of config, authentication checks and return
		//  if 'noSession' passed
		if ($p['noSession'])
			return;

		// start the session
		if (!empty($p['sessionID']))
			session_id($p['sessionID']);
		$this->startSession();
		
		// check for "session" language -- user defined language on the fly.
		if ($this->_slanguage) 
			$this->_session['slanguage'] = $this->_slanguage;
			
		if(isset($this->_session['slanguage'])) {
			if($this->_session['slanguage'] == 'en')
				$this->_l10n = FALSE;
			else {
				$this->_l10n = TRUE;
				Pommo::requireOnce($this->_baseDir . 'inc/helpers/l10n.php');
				PommoHelperL10n::init($this->_session['slanguage'], $this->_baseDir);
			}
			$this->_slanguage = $this->_session['slanguage'];
		}
		
		// if authLevel == '*' || _poMMo_support (0 if poMMo not installed, 1 if installed)
		if (defined('_poMMo_support')) {
			Pommo::requireOnce($this->_baseDir.'inc/classes/install.php');
			$p['authLevel'] = (PommoInstall::verify()) ? 1 : 0;
		}
		
		// check authentication levels
		$this->_auth = new PommoAuth(array (
			'requiredLevel' => $p['authLevel']
		));

		// clear SESSION 'data' unless keep is passed.
		// TODO --> phase this out in favor of page state system? 
		// -- add "persistent" flag & complicate state initilization...
		if (!$p['keep'])
			$this->_session['data'] = array ();
	}
	
	// reload base configuration from database
	function reloadConfig() {
		return $this->_config = PommoAPI :: configGetBase(TRUE);
	}
	
	function toggleEscaping($toggle = TRUE) {
		$this->_escaping = $toggle;
		$this->_logger->toggleEscaping($this->_escaping);
		return $toggle;
	}
	
	/**
	 *  Translation (l10n) Function
	 */
	 
	 function _T($msg) {
		global $pommo;
		if($pommo->_escaping)
			return ($pommo->_l10n) ? htmlspecialchars(PommoHelperL10n::translate($msg)) : htmlspecialchars($msg);
		return ($pommo->_l10n) ? PommoHelperL10n::translate($msg) : $msg;
	}

	function _TP($msg, $plural, $count) { // for plurals
		global $pommo;
		if($pommo->_escaping)
			return ($pommo->_l10n) ? htmlspecialchars(PommoHelperL10n::translatePlural($msg, $plural, $count)) : htmlspecialchars($msg);
		return ($pommo->_l10n) ? PommoHelperL10n::translatePlural($msg, $plural, $count) : $msg;
	}


	/**
	 *  _data Handler functions ==>
	 *    (got rid of _data reference...)
	 *    XXXX $pommo->_data is a reference to $_SESSION['pommo']['data'], an array in the Session
	 *    which holds any data we'd like to persist through pages. This array is cleared by default 
	 *    unless explicity saved by passing the 'keep' argument to the $pommo->init() function.
	 */

	function set($value) {
		if (!is_array($value))
			$value = array (
				$value => TRUE
			);
		return (empty ($this->_session['data'])) ? 
			$this->_session['data'] = $value : 
			$this->_session['data'] = array_merge($this->_session['data'], $value);
	}

	function get($name = FALSE) {
		if ($name)
			return (isset($this->_session['data'][$name])) ? 
				$this->_session['data'][$name] :
				array();
		return $this->_session['data'];
	}
	

	// redirect, require, kill base Functions
	
	function redirect($url, $msg = NULL, $kill = true) {
	global $pommo;
		// adds http & baseURL if they aren't already provided... allows code shortcuts ;)
		//  if url DOES NOT start with '/', the section will automatically be appended
		if (!preg_match('@^https?://@i', $url)) {
			if (strpos($url, $pommo->_baseUrl) === false) { 
				if (substr($url, 0, 1) != '/') {
					if ($pommo->_section != 'user' && $pommo->_section != 'admin') {
						$url = $pommo->_http . $pommo->_baseUrl . 'admin/' . $pommo->_section . '/' . $url;
					} else {
						$url = $pommo->_http . $pommo->_baseUrl . $pommo->_section . '/' . $url;
					}
				} else {
					$url = $pommo->_http . $pommo->_baseUrl . str_replace($pommo->_baseUrl,'',substr($url,1)); 
				}
			} else {
				$url = $pommo->_http . $url;
			}
		}
		header('Location: ' . $url);
		if ($kill)
			if ($msg)
				$pommo->kill($msg);
			else
				$pommo->kill($pommo->_T('Redirecting, please wait...'));
		return;
	}
	
	// kill => used to terminate a script
	function kill($msg = NULL, $backtrace = FALSE) {
		global $pommo;
		
		// output passed message
		if ($msg || !ob_get_length()) {
			
			if (empty($pommo->_workDir)) {
				echo ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
				echo ('<title>poMMo Error</title>'); // Very basics added for valid output
				echo '<div><img src="' . $pommo->_baseUrl . 'themes/shared/images/icons/alert.png" alt="alert icon" style="vertical-align: middle; margin-right: 20px;"/> ' . $msg . '</div>';
			}
			else {
				$logger =& $pommo->_logger;
				$logger->addErr($msg);
				Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
				$smarty = new PommoTemplate();
				$smarty->assign('fatalMsg',TRUE);
				$smarty->display('message.tpl');
			}	
		}
		
		// output debugging info if enabled (in config.php)
		if ($pommo->_debug) {
			if (is_object($pommo)) {
				Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/debug.php');
				$debug = new PommoHelperDebug();
				$debug->bmDebug();
			}
		}
		
		if ($backtrace) {
			$backtrace = debug_backtrace();
			echo @ '<h2>BACKTRACE</h2>'
				.'<p>'.@str_ireplace($pommo->_baseDir,'',$backtrace[1]['file']).':'.$backtrace[1]['line'].' '.$backtrace[1]['function'].'()</p>'
				.'<p>'.@str_ireplace($pommo->_baseDir,'',$backtrace[2]['file']).' '.$backtrace[2]['function'].'()</p>'
				.'<p>'.@str_ireplace($pommo->_baseDir,'',$backtrace[3]['file']).' '.$backtrace[3]['function'].'()</p>';
		}

		// print and clear output buffer
		ob_end_flush();
		
		// kill script
		die();
	}
	
	// faster performance than standard require_once
	// TODO -> extend function to make "smart" -- auto paths, jail to poMMo directory, etc.
	function requireOnce($file) {
		static $files;

		if (!isset ($files[$file])) {
			require ($file);
			$files[$file] = TRUE;
		}
	}
	
	function startSession($name = null) {
		static $start=false;
		if (!$start)
			session_start();
		$start = true;
		
		// generate unique session name
		$key =& $this->_config['key'];
		
		if(empty($key))
			$key = '123456';
		
		// create SESSION placeholder for if this is a new session
		if (empty ($_SESSION['pommo'.$key])) {
			$_SESSION['pommo'.$key] = array (
				'data' => array (),
				'state' => array (),
				'username' => null
			);
		}
		
		$this->_session =& $_SESSION['pommo'.$key];
	}
	
	// error log, E_ERROR trapping
	//  CAN NOT BE CALLED STATICALLY!
	function logErrors() {
		
		// ignore call if verbosity maximum.
		if($this->_verbosity < 2)
			return;
			
		// error handling
		error_reporting(E_ALL);
		ini_set('display_errors',0);
		ini_set('log_errors',1);
		ini_set('log_errors_max_len',0);
		ini_set('html_errors',0);
		
		// set log file
		ini_set('error_log',$this->_workDir . '/ERROR_LOG');
	}
}
?>
