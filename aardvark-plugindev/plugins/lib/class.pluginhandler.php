<?php
/**
 * Copyright (C) 2005, 2006, 2007  Brice Burgess <bhb@iceburg.net>
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
 * Get essential data for plugin mode
 */
class PluginHandler {
	
	var $dbo;
	
	
	function PluginHandler() {
		$this->dbo = NULL;
	}
	

	/**
	 * Returns the alias for the Administrator if its different than 'admin'
	 * This name is written in the config table of the pommo main db
	 */
	function dbGetAdminAlias() {
		
		global $pommo;
		//$this->dbo =& $pommo->_dbo; 
		$this->dbo = clone $pommo->_dbo;
		
		$a = array();
		
		$query = "SELECT config_value FROM " . $this->dbo->table['config'] . 
			" WHERE config_name = 'admin_username' LIMIT 1 "; 

		$query = $this->dbo->prepare($query);
		
		if ($row = $this->dbo->getRows($query))
			$a = $row['config_value'];
		
		return $a;
		
	} //dbGetAdminAlias


	/**
	 * Checks if a plugin, specified by its uniquename is enabled
	 */
	function dbGetPluginEnabled($pluginname) {
		
		global $pommo;
		//$this->dbo =& $pommo->_dbo; 
		$this->dbo = clone $pommo->_dbo;
		
		$a = array();
		
		$query = "SELECT plugin_active FROM " . $this->dbo->table['plugin'] . 
			" WHERE plugin_uniquename = '". $pluginname ."' LIMIT 1 "; 

		$query = $this->dbo->prepare($query);
		
		if ($row = $this->dbo->getRows($query))
			$a = $row['plugin_active'];
		
		//returns TRUE OR FALSE wether multiuser is active or not
		return $a;
		
	} //dbGetPluginEnabled




	/**
	 * Get the active authentication methods from DB
	 * returns a array of activated auth methods
	 */
	function dbGetAuthMethod() {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$m = array();
		
		// other query
		$query = "SELECT plugin_uniquename FROM " . $dbo->table['plugin'] .
				" WHERE (plugin_uniquename = 'simpleldapauth' AND plugin_active = 1) OR " .
				" (plugin_uniquename = 'queryldapauth' AND plugin_active = 1) OR " .
				" (plugin_uniquename = 'dbauth' AND plugin_active = 1)";

		// Can be a array if more plugins are activated
		
		$query = $dbo->prepare($query);
		
		$i = 0;
		while ($row = $dbo->getRows($query)) {
			$m[$i] = $row['plugin_uniquename'];
			$i++;
		}
		return $m;
		
	} //dbGetAuthMethod
	
	
} //PluginHandler

?>
