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

$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/helpers/fields.php');
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/helpers/subscribers.php');

class PommoHelperPersonalize {
	
	/*
	 * COMPATIBILITY ISSUE WITH REGEX ON WINDOWS (IIS) -- DEPRICATING IN FAVOR OF
	 * PommoHelperPersonalize::search()  and PommoHelperPersonalize::replace()
	 * 
	 * LEAVING IN SOURCE FOR REFERENCE....
	 */
	 
	/*
	// scans a message body and returns an array of applicable personaliztions
	// accepts a message body (str)
	// returns a personalization array (array of 4 arrays) 
	//  array[0] == fulltext replace, array[1] == field_name, array[2] == default value, array[3] == field_id

	e.g.
	 array(4) {
	  [0]=> -- FULLTEXT REPLACE(s)
	  array(2) {
	    [0]=>
	    string(9) "[[smell]]"
	    [1]=>
	    string(17) "[[xyz|defaultZZ]]"
	  }
	  [1]=> -- FIELD NAME(s)
	  array(2) {
	    [0]=>
	    string(5) "smell"
	    [1]=>
	    string(3) "xyz"
	  }
	  [2]=> -- DEFAULT(s)
	  array(2) {
	    [0]=>
	    string(0) ""
	    [1]=>
	    string(9) "defaultZZ"
	  }
	  [3] => -- FIELD_ID(s)
	  	array(2) {
	  		[0] =>
	  		string(1) "1"
	  		[2] =>
	  		string(1) "7"
	  	}
	} 
	function & get(&$body) {
		$fields = PommoField::get();
		
		$matches = array();
		$pattern = '/\[\[([^\]|]+)(?:\|([^\]]+))?]]/';
		
		if (preg_match_all($pattern, $body, $matches) < 1) {
			$a = array();
			return $a;
		}
		
		// add field_id to name
		
		$matches[3] = array();
		foreach($matches[1] as $field) {
			foreach($fields as $f) {
				if ($f['name'] == $field)
					$matches[3][] = $f['id'];
			}
		}
		return $matches;
	}
	
	// personalizes a message body || subject
	// accepts message
	// accepts subscriber object (single subscriber)
	// accepts personalization array
	// returns a personalized body
	function body(&$msg, &$s, &$p) {
		$body = $msg;
		foreach($p[0] as $key => $search) {
		
			// lookup replace string
			
			switch (strtolower($p[1][$key])) {
				case 'email':
					$replace = $s['email'];
					break;
				case 'ip':
					$replace = $s['ip'];
					break;
				case 'registered':
					$replace = $s['registered'];
					break;
				case '!unsubscribe':
					$replace = $GLOBALS['pommo']->_http.$GLOBALS['pommo']->_baseUrl.'user/update.php?email='.$s['email'].'&code='.md5($s['id'].$s['registered']);
					break;
				case '!weblink':
					$replace = $GLOBALS['pommo']->_http.$GLOBALS['pommo']->_baseUrl.'user/mailings.php?mail_id='.$_GET['id'];
					break;
				case '!subscriber_id':
					$replace = $s['id'];
					break;
				case '!mailing_id':
					$replace = $_GET['id'];
					break;
				default:
					$replace = $s['data'][ ($p[3][$key]) ];
					break;
			}
			
			// attempt to add default if replacement is empty
			if (empty($replace))
				$replace = $p[2][$key];
				
			$body = str_replace($search, $replace, $body);
		}
		return $body;
	}
	
	*/
	
	// scan a body and return an array of applicable personaliztions
	// accepts a message body (str)
	// returns a personalization array (array of personalizations)
	//  e.g. array('search' => full_text_replace, 'field' => field_name, 'default' => default_value, 'field_id' => field_id);
	function & search(&$body) {
		$personalizations = array();
			
		$matches = array();
		$pattern = '/\[\[[^\]]+\]\]/';
		if (preg_match_all($pattern, $body, $matches) < 1)
			return $personalizations;
		
		$fields = PommoField::get();
		foreach($matches[0] as $str) {
			$p = array();
			$p['search'] = $str;
			$a = explode('|',trim($str,'[]'));
			$p['field'] = $a[0];
			$p['default'] = (isset($a[1])) ? $a[1] : false;
			foreach($fields as $f) 
				if ($f['name'] == $p['field'])
					$p['field_id'] = $f['id'];
			array_push($personalizations,$p);
		}
		return $personalizations;
	}
	
	// personalizes a body
	// accepts message
	// accepts subscriber object (single subscriber)
	// accepts personalization array
	// returns a personalized body
	function replace(&$msg, &$s, &$personalizations) {
		$body = $msg;
		foreach($personalizations as $p) {
		
			// lookup replace string
			switch (strtolower($p['field'])) {
				case 'email':
					$replace = $s['email'];
					break;
				case 'ip':
					$replace = $s['ip'];
					break;
				case 'registered':
					$replace = $s['registered'];
					break;
				case '!unsubscribe':
					$replace = $GLOBALS['pommo']->_http.$GLOBALS['pommo']->_baseUrl.'user/update.php?email='.$s['email'].'&code='.PommoSubscriber::getActCode($s);
					break;
				case '!weblink':
					$replace = $GLOBALS['pommo']->_http.$GLOBALS['pommo']->_baseUrl.'user/mailings.php?mail_id='.$_GET['id'].'&email='.$s['email'].'&code='.PommoSubscriber::getActCode($s);
					break;
				case '!subscriber_id':
					$replace = $s['id'];
					break;
				case '!mailing_id':
					$replace = $_GET['id'];
					break;
				default:
					$replace = $s['data'][$p['field_id']];
					break;
			}
			
			// attempt to add default if replacement is empty
			if (empty($replace))
				$replace = $p['default'];
				
			$body = str_replace($p['search'], $replace, $body);
		}
		return $body;
	}
	
	
	
}
?>