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
class ConfigDBHandler { //implements iDbHandler {


	function ConfigDBHandler() {
	}

	/** Returns if the Plugin itself is active */
	function & dbPluginIsActive($pluginname) {
		// The plugin administration plugin should always be active
		return TRUE;
	}
	

	/* Get all active Plugins + configuration in a Matrix */
	function dbGetPluginMatrix() {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		
		$query = "SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, " .
				"c.cat_id, cat_name, cat_active, plugin_version " .
				"FROM ".$dbo->table['plugin']." AS p RIGHT JOIN ".$dbo->table['plugincategory']." AS c ON p.cat_id=c.cat_id WHERE c.cat_active=1 " .
				"ORDER BY cat_name, plugin_id";
		
		$query = $dbo->prepare($query);
		
		$i=0; $plugins = array();
		
		while ($row = $dbo->getRows($query)) {
			$plugins[$i] = array(
				'pid' 		=> $row['plugin_id'],
				'uniquename'=> $row['plugin_uniquename'],
				'name'		=> $row['plugin_name'],
				'desc'		=> $row['plugin_desc'],
				'pactive'	=> $row['plugin_active'],
				'version'	=> $row['plugin_version'],
				'cid'		=> $row['cat_id'],
				'category'	=> $row['cat_name'],
				'cactive'	=> $row['cat_active'],
				);
			$i++;
		}
		
		return $plugins;
	}

	/* Get categories, that are active Or inactive, or all */
	function dbGetCategories($active = NULL) {	
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = NULL;
		if ($active == 'inactive') {	//ALL INACTIVE
			$query = "SELECT cat_id, cat_name, cat_desc, cat_active FROM ".$dbo->table['plugincategory']." WHERE cat_active=0 ";
		} elseif ($active == 'active') {	//ACTIVE ONES
			$query = "SELECT cat_id, cat_name, cat_desc, cat_active FROM ".$dbo->table['plugincategory']." WHERE cat_active=1 ";
		} else {	//ALL CATEGORIES
			$query = "SELECT cat_id, cat_name, cat_desc, cat_active FROM ".$dbo->table['plugincategory']." ORDER BY cat_active ";
		}
		
		$query = $dbo->prepare($query);
		
		$i=0; $cat = NULL;
		while ($row = $dbo->getRows($query)) {
			$cat[$i] = array(
				'cid' 		=> $row['cat_id'],
				'name'		=> $row['cat_name'],
				'desc'		=> $row['cat_desc'],
				'cactive'	=> $row['cat_active'],
				);
			$i++;
		}
		return $cat;
	}



	/** Get the setup values for one given Plugin ID */
	function & dbGetPluginSetup($pluginid) {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = "SELECT data_id, data_name, data_value, data_type, data_desc, plugin_id " .
				"FROM ".$dbo->table['plugindata']." WHERE plugin_id=".$pluginid;

		$query = $dbo->prepare($query);

		$i=0;
		while ($row = $dbo->getRows($query)) {
			$data[$i] = array(
				'data_id' 		=> $row['data_id'],
				'data_name'		=> $row['data_name'],
				'data_value'	=> $row['data_value'],
				'data_type'		=> $row['data_type'],
				'data_desc'		=> $row['data_desc'],
				'plugin_id'		=> $row['plugin_id'],
				);
			$i++;
		}
		return $data;	// Should be written in 'data' field of the plugins	
	}	
	
	
	
	/************************ PLUGIN USE CASES ************************/

	/**
	 * Update posted parameter changes in the database
	 * $nevval is a array with all the information
	 * returns the amount of changed "items"
	 */
	function dbUpdatePluginData($id, $newval) {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = "UPDATE ".$dbo->table['plugindata']." SET data_value='".$newval."' WHERE data_id=".$id;
		$query = $dbo->prepare($query);
		
		return $dbo->query($query);

	}
	

	function dbSwitchPlugin($pluginid, $setto) {	// = NULL

		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = NULL;
		// TODO -> This feature below is not needed. I want to be able to activate the options independently e.g. 
		//			if one wants to activate db auth and ldap auth he hast du activate both this plugins
		// Switch all other options from this category. (Mostly we want only 1 configuration active e.g. the authentication method)
		/*$sql = $this->safesql->query("UPDATE %s SET NOT(plugin_active) WHERE plugin_id!=%i", array('pommomod_plugin', $pluginid ) );
		$countoff = $this->dbo->query($sql);*/
		/*if (!setto) {
			$sql = $this->safesql->query("UPDATE %s SET NOT(plugin_active) WHERE plugin_id=%i",
				array('pommomod_plugin', $pluginid ) );
		*/

		$query = "UPDATE ".$dbo->table['plugin']." SET plugin_active=".$setto." WHERE plugin_id=".$pluginid;
		$query = $dbo->prepare($query);
			
		return $dbo->query($query);
	}
	
	
	function dbSetPlugins($state) {	// = NULL

		global $pommo;
		$dbo = clone $pommo->_dbo;

		if (($state == "ON") OR ($state == "OFF")) {
			
			if ($state == "ON") $to = "TRUE"; else $to = "FALSE";
			
			$query = NULL;
			$query = "UPDATE ".$dbo->table['plugin']." SET plugin_active=".$to;
			$query = $dbo->prepare($query);
			
			return $dbo->query($query);
		
		} 
		return FALSE;
	
	}




	/**************************** CATEGORY USE CASES ******************************/
	
	/**
	 * Updates category data, sets the given category as active/inactive 
	 * and returns the amaount of changed data values.
	 */
	function dbSwitchCategory($catid, $setto) {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = "UPDATE ".$dbo->table['plugincategory']." SET cat_active=".$setto." WHERE cat_id=".$catid;
		$query = $dbo->prepare($query);
		
		return $dbo->query($query);
	}



} //ConfigDBHandler

?>
