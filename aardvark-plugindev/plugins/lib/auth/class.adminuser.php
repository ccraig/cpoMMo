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


/*
 * Having a separate Admin class makes sense: (linux way)
 * - there should only be this authentification with the config values,
 * 	so that in case someone corrupts the authentication plugin
 * 	the "real" administrator can always reset the authentication methods 
 * 	to the intended method. You could not login into the system anymore if
 *  the auth goes only through the user management system
 * - You can define other administrators as well with the user functionality
 * 	with all the necessary rights/permissions.
 *  If these permissions are somehow corrupted you have the option to reset 
 *  them with to the intended state through this separated Admin class because 
 *  the admin has always all privileges and these are not checked when you log 
 *  on with the config admin. 
 * - OR ldap server hangs/is broken and you don't reach pommo anymore
 * - This is some kind of linux style root user, whitch is a common way.
 */

class AdminUser {
	
	var $_usertype;
	var $_uid;
	var $_username;
	var $_md5pass;
	var $_permissionLevel;



	function AdminUser($username, $md5pass) {
		$this->_usertype = "adminuser";
		$this->_uid = NULL;
		$this->_username = $username;
		$this->_md5pass = $md5pass;
		$this->_permissionLevel = 0;
	} //Constructor

	// afaik destructors in php4 are not used
	function __destruct() {
		unset($this->_uid);
		unset($this->_username);
		unset($this->_md5pass);
		unset($this->_permissionLevel);
	} //Destructor
	

	/**
	 * authenticate standard administrator way, data lies in the config table
	 */
	function authenticate() {

		global $pommo;

		if ($this->dbVerifyAdmin()) {
			$pommo->_logger->addMsg("Administrator found. Welcome.");
			return TRUE;
		} else {
			return FALSE;
		}
		
	} //authenticate

	
	function getPermissionLevel() {
		return $this->_permissionLevel;
	}
	
	function getUserID() {
		return $this->_uid;
	}


	/**
	 *  don' allow access from outside: private
	 *	maybe split up in 2 db / data management classes
	 *	i let it compact at this time we have no other database system
	 *
	 *	returns TRUE if administrator is ok, else FALSE
	 */ 
	function dbVerifyAdmin() {

		global $pommo;
		//TODO clone question
		$dbo = clone $pommo->_dbo;

		$a = array();
		
		$query = "SELECT config_value FROM ".$dbo->table['config'].
				 " WHERE config_name = 'admin_password' AND config_value = '".$this->_md5pass."' LIMIT 1  "; 

		$query = $dbo->prepare($query);
		
		if ($row = $dbo->getRows($query)) {
		
					if ($dbo->affected() == 1) {
						$this->_permissionLevel = 5;
						$this->_uid = 0; // Administrator has Id:0 (unix way);
						return TRUE;
					}
		}
		
		$pommo->_logger->addMsg("AdminUser: Login failed.");
		return FALSE;
		
	} //dbVerifyAdmin
	

	
} //AdminUser

?>
