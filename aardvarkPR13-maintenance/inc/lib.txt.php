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

// holds some useful text/input parsing/validating functions

/** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

 /* 
  * Formatting Functions -
  ****************************************************************/
  
/**
 * str2display: Formats user inputted text from forms to be displayed ("previewed").
 *    note: for populating form values, simply use: "value=htmlspecialchars($str);" 
 */
 
function str2display(& $string) {

	if (!get_magic_quotes_gpc())
		return nl2br(htmlspecialchars($string));
	return nl2br(htmlspecialchars(stripslashes($string)));
}

/**
 * str2str: Formats user inputted text from forms to be checked against other input.
 *    if magic quotes is on, strip slashes, if not, return string.
 */
 
 // TODO .. PHASE OUT -- as _GET & _POST are being removed.
function str2str(& $string) {
	if (!get_magic_quotes_gpc())
		return $string;
	return stripslashes($string);
}


/**
 *  array2csv: Takes an array, and returns a csv compliant string from its contents.
 *   If an array is not supplied, the argument will be returned in tact.
 */
function & array2csv(&$array) {
	$str = '';
	if (is_array($array)) {
		$str = implode(',', $array);
		return $str;
	}
	return $array;
}

 /* 
  * Validation functions - returns boolean based on rule matching input. 
  ****************************************************************/

/**
 * isEmail: returns true if $str looks like an email address
 */

function isEmail(& $string) {
    $p = '/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*';
    $p.= '@([-a-z0-9]+\.)+([a-z]{2,3}';
    $p.= '|info|arpa|aero|coop|name|museum)$/ix';
    return preg_match($p, $string);
}

function isChecked($string = NULL, $returnArray = NULL) {
		// returns true or false to designate if a string means a value is checked/TRUE. 
		// returns an array of valid Trues and Falses if designated

	$truth = array ("1", "YES", "yes", "Yes", "Y", "n", "CHECKED", "Checked", "checked", "CHECK", "Check", "check", "on", "ON", "On", "SELECTED", "Selected", "selected", "TRUE", "True", "true");
	$untruth = array ("0", "NO", "No", "no", "N", "n", "OFF", "Off", "off", "NULL", "FALSE", "False", "false");

	if (!empty ($returnArray))
		return array_merge($truth, $untruth);
	else {
		if (in_array($string, $truth))
			return true;
		return false;
	}
}



 /* 
  * Misc string functions
  ****************************************************************/


/**
 * quotesplit: for putting CSV-Like data into an array --> author: moritz @ php.net 
 * 
 * ie:
 * 1 , 3, 4
 * -> [1,3,4]
 * 
 * one; two;three
 * -> ['one','two','three']
 * 
 * "this is a string", "this is a string with , and ;", 'this is a string with quotes like " these', "this is a string with escaped quotes \" and \'.", 3
 * -> ['this is a string','this is a string with , and ;','this is a string with quotes like " these','this is a string with escaped quotes " and '.',3]
 */

function & quotesplit($s) {
	$r = Array ();
	$p = 0;
	$l = strlen($s);
	while ($p < $l) {
		while (($p < $l) && (strpos(" \r\t\n", $s[$p]) !== false))
			$p ++;
		if ($s[$p] == '"') {
			$p ++;
			$q = $p;
			while (($p < $l) && ($s[$p] != '"')) {
				if ($s[$p] == '\\') {
					$p += 2;
					continue;
				}
				$p ++;
			}
			$r[] = stripslashes(substr($s, $q, $p - $q));
			$p ++;
			while (($p < $l) && (strpos(" \r\t\n", $s[$p]) !== false))
				$p ++;
			$p ++;
		} else
			if ($s[$p] == "'") {
				$p ++;
				$q = $p;
				while (($p < $l) && ($s[$p] != "'")) {
					if ($s[$p] == '\\') {
						$p += 2;
						continue;
					}
					$p ++;
				}
				$r[] = stripslashes(substr($s, $q, $p - $q));
				$p ++;
				while (($p < $l) && (strpos(" \r\t\n", $s[$p]) !== false))
					$p ++;
				$p ++;
			} else {
				$q = $p;
				while (($p < $l) && (strpos(",;", $s[$p]) === false)) {
					$p ++;
				}
				$r[] = stripslashes(trim(substr($s, $q, $p - $q)));
				while (($p < $l) && (strpos(" \r\t\n", $s[$p]) !== false))
					$p ++;
				$p ++;
			}
	}
	return $r;
}
	
?>