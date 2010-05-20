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

// common API

class PommoAPI {

	function & getParams(& $defaults, & $args) {
		$p = array_merge($defaults, $args);

		// make sure all submitted parameters are "known" by verifying size of final array
		if (count($p) > count($defaults)) {
			global $pommo;
			if ($pommo->_verbosity < 3)
				var_dump($defaults,$args);
			Pommo::kill('Unknown argument passed to PommoAPI::getParams()', TRUE);
		}

		return $p;
	}


	// Returns Base Configuration Data
	function & configGetBase() {
		global $pommo;
		$dbo = & $pommo->_dbo;
		$dbo->dieOnQuery(FALSE);
		
		$config = array();
		
		$query = "
			SELECT config_name, config_value
			FROM ".$dbo->table['config'] ."
			WHERE autoload='on'";
		$query = $dbo->prepare($query);
		
		while ($row = $dbo->getRows($query))
			$config[$row['config_name']] = $row['config_value'];

		$dbo->dieOnQUery(TRUE);
		return $config;
	}

	// Gets specified config value(s) from the DB. 
	// Pass a single or array of config_names, returns array of their name>value.
	function configGet($arg) {
		global $pommo;
		$dbo = & $pommo->_dbo;
		$dbo->dieOnQuery(FALSE);


		if ($arg == 'all')
			$arg = null;
			
		$query = "
			SELECT config_name,config_value
			FROM ". $dbo->table['config']."
			[WHERE config_name IN(%Q)]";
		$query = $dbo->prepare($query,array($arg));
		
		while ($row = $dbo->getRows($query))
			$config[$row['config_name']] = $row['config_value'];

		$dbo->dieOnQUery(TRUE);
		return $config;
	}

	// update the config table. 
	//  $input must be an array as key:value ([config_name] => config_value)
	function configUpdate($input, $force = FALSE) {
		global $pommo;
		$dbo = & $pommo->_dbo;

		if (!is_array($input))
			Pommo :: kill('Bad input passed to updateConfig', TRUE);
			
		// if this is password, skip if empty
		if (isset($input['admin_password']) && empty($input['admin_password']))
			unset($input['admin_password']);

		// get eligible config rows/options to change
		$force = ($force) ? null : 'on';
		$query = "
			SELECT config_name
			FROM " . $dbo->table['config'] . "
			WHERE config_name IN(%q)
			[AND user_change='%S']";
		$query = $dbo->prepare($query, array (array_keys($input), $force));

		// update rows/options
		$when = '';
		while ($row = $dbo->getRows($query)) { // multi-row update in a single query syntax
			$when .= $dbo->prepare("WHEN '%s' THEN '%s'",array($row['config_name'],$input[$row['config_name']])).' ';
			$where[] = $row['config_name']; // limits multi-row update query to specific rows (vs updating entire table)
		}
		$query = "
			UPDATE " . $dbo->table['config'] . "
			SET config_value =
				CASE config_name ".$when." ELSE config_value END
			[WHERE config_name IN(%Q)]";
		if (!$dbo->query($dbo->prepare($query,array($where))))
			Pommo::kill('Error updating config');
		return true;
	}
	
	// initializes a page state
	// accepts name of page state (usually unique per page)
	// accepts array of default state variables
	// accepts array of ovverriding variables
	// returns the current page state (array)
	function & stateInit($name = 'default', $defaults = array (), $source = array()) {
		global $pommo;
				
		if (empty($pommo->_session['state'][$name]))
			$pommo->_session['state'][$name] = $defaults;
		
		$state =& $pommo->_session['state'][$name];
		
		if(empty($defaults))
			return $state;

		foreach(array_keys($state) as $key)
			if (array_key_exists($key,$source))
				$state[$key] = $source[$key];
		
		// normalize the page state
		if (count($state) > count($defaults)) 
			$state = PommoHelper::arrayIntersect($state, $defaults);
			
		return $state;
	}
	
	// clears page state(s)
	// accepts a state name or array of state names to clear
	//   if not supplied, ALL page states are cleared
	// returns (bool)
	function stateReset($state = array()) {
		global $pommo;
		
		if (!is_array($state))
			$state = array($state);
		
		if (empty($state))
			$pommo->_session['state'] = array();
		else
			foreach($state as $s)
				unset($pommo->_session['state'][$s]);
				
		return true;
	}
}
?>
