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

/* Collection of useful helper functions/"utilities"
 * NOTE: Should be called statically e.g. PommoHelper::helperFunction($arg1,...);
 */

class PommoHelper {
	
	// deeply strips slashes added by magic quotes. Generally used on $_POST & $_GET.
	function slashStrip($input) {
		if (is_array($input)) {
			foreach ($input as $key => $value) {
				$input[$key] = PommoHelper::slashStrip($value);
			}
			return $input;
		} else {
			return stripslashes($input);
		}
	}
	
	/**
	 * Parse a config file, return an array containing key: value
	 * 
	 * Grammar of config file is;
	 * [key] = "value"
	 *   or
	 * [key] = i am a value
	 * 
	 * If parser comes across a trimmed line not beginning with [, the line will be ignored.
	 *   this flexible grammar allows for commets and user error (non homogenous syntax)
	 */
	function parseConfig($file) {
		$a = array();
		
		@$file_content = file($file);
		if (empty($file_content))
			Pommo::kill('Could not read config file ('.$file.')');
		
		foreach ($file_content as $rawLine) {
			$line = trim($rawLine);
			if (substr($line,0,1) == '[') { // line should be traded as a key:value pair
				$matches = array();
				preg_match('/^\[(\w+)\]\s*=\s*\"?([^\"]*)\"?.*$/i',$line,$matches);

				// check if a key:value was extracted
				if (!empty($matches[2]))
					// merge key:value onto return array
					$a = array_merge($a, array($matches[1] => $matches[2]));
			}
		}
		return $a;
	}
	
	// check an email. Function lifted from Monte's SmartyValidate class for consistency.
	// accepts an email address (str)
	// returns email legitimacy (bool)
	function isEmail($_address) {
		return (!(preg_match('!@.*@|\.\.|\,|\;!', $_address) || !preg_match('!^.+\@(\[?)[a-zA-Z0-9\.\-]+\.([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$!', $_address))) ? true : false;
	}
	
	function isEmailS($value) {
		
		// in case value is several addresses separated by newlines
    $_addresses = preg_split('![\n\r]+!', $value);

    foreach($_addresses as $_address) {
		$_is_valid = !(preg_match('!@.*@|\.\.|\,|\;!', $_address) ||
	        !preg_match('!^.+\@(\[?)[a-zA-Z0-9\.\-]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$!', $_address));
        
        if(!$_is_valid)
            return false;
    }
    return true;
    
	}
	
	// generates a unique code to be used as a confirmation key.
	// returns code (str)
	function makeCode($length = false) {
		if (!$length)
			return md5(rand(0, 5000).time());
		return substr(md5(rand(0, 5000).time()),0,$length-1).rand(0,9);
	}
	
	function makePassword() {
		return substr(md5(rand()), 0, 5);
	}
	
	// checks to see if an email address exists in the system
	//  only includes active && pending subscribers by DEFAULT
	// accepts a single email (str) or array of emails
	//  accepts a flag to include unsubcribed subscribers
	// returns an array of duplicate found emails. FALSE if no dupes were found. 
	function & isDupe(&$in,$includeUnsubscribed = false) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if(empty($in))
			return false;

		$query = "
			SELECT ".(($includeUnsubscribed) ? 'DISTINCT ' : '')."email
			FROM " . $dbo->table['subscribers'] ."
			WHERE email IN (%q)
			AND status IN(".(($includeUnsubscribed) ? '0,' : '')."1,2)";
		$query = $dbo->prepare($query,array($in));
		$o = $dbo->getAll($query, 'assoc', 'email');
		if (empty($o))
			$o = false;
		
		return $o;
	}
	
	// array_intersect_key requires PHP 5.1 +, here's a compat function --> (limited to 2 arrs)
	// returns an array containing all the values of array1  which have matching keys that are present in a2
	function & arrayIntersect(&$a1, &$a2) {		
		$o = array();
		if (!is_array($a1) || !is_array($a2))
			return $o;
			
		foreach(array_keys($a2) as $key) {
			if (isset($a1[$key]))
				$o[$key] = $a1[$key];
		}
		return $o;
	}
	
	// trims an array of whitespace
	function & trimArray(&$a) {array_walk($a,array('PommoHelper','trimValue')); return $a;}
	function trimValue(&$value){$value = trim($value);}
	
	// returns true if the page has been requested via a browser XMLHTTPRequest (AJAX call)
	function isAjax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER ['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}
	
	// Returns the current dateformat as a string
	function timeGetFormat() {
		global $pommo;
		switch ($pommo->_dateformat) {
			case 1: $format = 'YYYY/MM/DD'; break;
			case 2: $format = 'MM/DD/YYYY'; break;
			case 3: $format = 'DD/MM/YYYY'; break;
			default: Pommo::kill('Unknown dateformat', TRUE);
		}
		return $format;
	}
	// converts a timestamp to human readable string. 
	// Function will allow overidding of default date display format (MM/DD/YY vs. DD/MM/YY vs. YYYY/MM/DD etc.)
	function timeToStr($int) {
		global $pommo;
		
		if (!defined('ADODB_DATE_VERSION')) // safely load ADODB date library
			Pommo::requireOnce($pommo->_baseDir.'inc/lib/adodb/adodb-time.inc.php');

		switch ($pommo->_dateformat) {
			case 1: $format = '%Y/%m/%d'; break;
			case 2: $format = '%m/%d/%Y'; break;
			case 3: $format = '%d/%m/%Y'; break;
			default: Pommo::kill('Unknown dateformat', TRUE);
		}
		
		return adodb_strftime($format,$int);
	}
	
	// convert human readable date strings to unix timestamps
	// piggy backs on adodb's excellent time library -- supporting a wide range of dates (100AD +)
	function timeFromStr($str)
	{
		global $pommo;
		
		if (!defined('ADODB_DATE_VERSION')) // safely load ADODB date library
			Pommo::requireOnce($pommo->_baseDir.'inc/lib/adodb/adodb-time.inc.php');
		
		// normalize delimiter
		str_replace('-','/',$str);
		
		// Extract Year, Month, and Day from a string like "2007/08/03"
		$a = explode("/", $str);
		
		// Validate the string
		if (count($a) != 3 || !is_numeric($a[0]) || !is_numeric($a[1]) || !is_numeric($a[2]))
			return false;
			
		switch($pommo->_dateformat) {
			case 1: 
				$year = substr($a[0],0,4);
				$month = substr($a[1],0,2);
				$day = substr($a[2],0,2);
				break;
			case 2: 
				$year = substr($a[2],0,4);
				$month = substr($a[0],0,2);
				$day = substr($a[1],0,2);
				break;
			case 3: 
				$year = substr($a[2],0,4);
				$month = substr($a[1],0,2);
				$day = substr($a[0],0,2);
				break;
			default:
				Pommo::kill('Unknown date_format', TRUE);
		}
		
		// Y-M-D validation	
		if($month < 1 || $month > 12)
			return false;
			
		if($day < 1 || $day > 31)
			return false;
		
		// correction heuristic for short year @ end of century...
		if (strlen($year) == 2)
			$year = ($year < 50) ? 
				'20'.$year :
				'19'.$year;
	
		return adodb_mktime(0, 0, 0, $month, $day, $year);
	}
}
?>
