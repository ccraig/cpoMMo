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

require_once (bm_baseDir . '/inc/db_fields.php');
require_once (bm_baseDir . '/inc/db_subscribers.php');


// scans body returns an array with three arrays. array[0] == fulltext replace, array[1] == field_name, array[2] == default value, array[3] == field_id
/* e.g.
 * array(3) {
  [0]=> -- FULLTEXT REPLACE
  array(2) {
    [0]=>
    string(9) "[[smell]]"
    [1]=>
    string(17) "[[xyz|defaultZZ]]"
  }
  [1]=> -- FIELD NAME
  array(2) {
    [0]=>
    string(5) "smell"
    [1]=>
    string(3) "xyz"
  }
  [2]=> -- DEFAULT
  array(2) {
    [0]=>
    string(0) ""
    [1]=>
    string(9) "defaultZZ"
  }
  [3] => -- FIELD_ID
  	array(2) {
  		[0] =>
  		string(1) "1"
  		[2] =>
  		string(1) "7"
  	}
} */
function getPersonalizations($body) {
	$matches = array();
	$pattern = '/\[\[([^\]|]+)(?:\|([^\]]+))?]]/';
	
	preg_match_all($pattern, $body, $matches);
	
	// add field_id to name
	
	$matches[3] = array();
	foreach($matches[1] as $field) {
		$matches[3][] = dbGetFieldId($field);
	}
	
	return $matches;
}

// personalizes a message body. Reads in body, email address (person), and personalizations global
function personalizeBody($body, $person, &$personalizations) {

	global $dbo;
	
	// get the subscriber info
	$subscriber = current(dbGetSubscriber($dbo,$person));
	
	foreach($personalizations[0] as $key => $search) {
		
		// lookup replace string (or if it is Email, replace with email address)
		$replace = ($personalizations[1][$key] == 'Email') ? $person : 
			$subscriber['data'][ ($personalizations[3][$key]) ];
		
		// attempt to add default if replacement is empty
		if (empty($replace))
			$replace = $personalizations[2][$key];
			
		$body = str_replace($search, $replace,$body);
	}
	
	return $body;
}
?>