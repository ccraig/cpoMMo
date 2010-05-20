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

// MultiUser Authentication class
// this class exchanges the inc/classes/auth.php class PommoAuth {}


Pommo::requireOnce($this->_baseDir . 'plugins/lib/class.pluginhandler.php');
Pommo::requireOnce($this->_baseDir . 'plugins/lib/auth/class.simpleuser.php');
Pommo::requireOnce($this->_baseDir . 'plugins/lib/auth/class.adminuser.php');


/**
 * Defines the authentication method use 
 * database auth, LDAP auth ....
 * This is defined in the GENERAL PLUGIN SECTION, where you can choose
 * one or more of these options (all methods for very restrictive and 
 * intensive checks). Although a standard pommo installation probably
 * does not need LDAP authentication.
 */
class MultAuth { 

	var $user;
	var $authenticated;
	var $dbhelper;

	var $_username;	// current logged in user (default: null|session value)
	var $_permissionLevel; // permission level of logged in user
	var $_requiredLevel; // required level of permission (default: 1)
	
	

	function MultAuth($args = array ()) {

		global $pommo;

		$this->user = null;
		$this->authenticated = FALSE;
		$this->dbhelper = new PluginHandler();

		$defaults = array (
			'username' => null,
			'requiredLevel' => 0
		);
		
		$p = PommoAPI :: getParams($defaults, $args);
		
		
		if (empty($pommo->_session['username']))
			$pommo->_session['username'] = $_SESSION['pommo123456']['username'];//$p['username'];
		

		
		$this->_username = & $pommo->_session['username'];
		$this->_permissionLevel = $_SESSION['pommo123456']['permlvl'];//$this->getPermissionLevel($this->_username);
		$this->_requiredLevel = $p['requiredLevel'];
		

		if ($p['requiredLevel'] > $this->_permissionLevel) {
			global $pommo;
			Pommo::kill(sprintf(Pommo::_T('Denied access. You must %slogin%s to access this page...'), '<a href="' . $pommo->_baseUrl . 'index.php?referer=' . $_SERVER['PHP_SELF'] . '">', '</a>'));
		}
		
	} //constructor






	function authenticate($username, $pass) {
		
		// auth process dann in dbauth und so
		
		global $pommo;


		$alias = $this->dbhelper->dbGetAdminAlias();
		

		//construct the user object	
		if ($username == $alias) {
			$this->user = new AdminUser($alias, md5($pass));
		} else {

			if ($pommo->_plugindata['pluginmultiuser']) {
				$this->user = new SimpleUser($username, $pass);
			} else {
				$pommo->_logger->addMsg("MultAuth: plugins not enabled. try to enable in config.php");
			}

		}
		
		// if userobject is constructed do the authentication
		if ($this->user) {
			if ( $this->user->authenticate() ) {
				$key = '123456';
				$_SESSION['pommo'.$key]['username'] = $username;
				$_SESSION['pommo'.$key]['md5pass'] = md5($pass);
				$_SESSION['pommo'.$key]['id'] = $this->user->getUserID();
				$_SESSION['pommo'.$key]['permlvl'] = $this->user->getPermissionLevel();
				//$this->user->dbWriteLastLogin($username);
				//$this->user->dbIncreaseLoginTries($username);
				$this->authenticated = TRUE;
				return TRUE;
			} else {
				$this->authenticated = FALSE;
				session_destroy();
				return FALSE;
			}
		} else {
			$pommo->_logger->addMsg("MultAuth: authenticate: No user object found.");
			$this->authenticated = FALSE;
			return FALSE;
		}
		
			
	} //authenticate


	function isAuthenticated() {
		return $this->authenticated;
	}


	function getPermissionLevel($username = null) {
		/*if ($username)
			return 5;
		return 0;*/
		
		if ($this->username AND $this->authenticated) {
			$key = '123456';
			$permlvl = $_SESSION['pommo'.$key]['id'];
			
			return $permlvl;
		}
		
		// no permission
		return 0;
	}
	
	function logout() {
		$this->_username = null;
		$this->_permissionLevel = 0;
		session_destroy();
		return;
	}
	
	function login($username) {
		$this->_username = $username;
		return;
	}
	


	/**
	 * permissiontype is a STRING that denotes the permission needed/enabled for a user to enter the site
	 */
	/**
	 all permissions table:
	 SELECT user_name, permgroup_name, perm_name FROM pommomod_user AS u RIGHT JOIN pommomod_permgroup AS pg ON u.permgroup_id=pg.permgroup_id
RIGHT JOIN pommomod_pg_perm AS pgp ON pg.permgroup_id=pgp.permgroup_id 
RIGHT JOIN pommomod_permission AS p ON pgp.perm_id=p.perm_id
ORDER BY user_name


1 permission for a user:
SELECT user_name, permgroup_name, perm_name FROM pommomod_user AS u RIGHT JOIN pommomod_permgroup AS pg ON u.permgroup_id=pg.permgroup_id
RIGHT JOIN pommomod_pg_perm AS pgp ON pg.permgroup_id=pgp.permgroup_id 
RIGHT JOIN pommomod_permission AS p ON pgp.perm_id=p.perm_id
WHERE u.user_name='corinna' AND p.perm_name='PLUGINADMIN'
ORDER BY user_name
	 */
	function dbCheckPermission($permissiontype) {
	
		$adminalias = $this->dbhelper->dbGetAdminAlias();
		if ($this->_username == $adminalias){ // AND $this->isAuthenticated()) {
		
			return TRUE;
		
		} else {

			if (is_array($permissiontype)) {
				
				global $pommo;
				$dbo = clone $pommo->_dbo;
				
				$query = "SELECT user_name, permgroup_name, perm_name " .
						"FROM ".$dbo->table['user']." AS u RIGHT JOIN ".$dbo->table['permgroup']." AS pg ON u.permgroup_id=pg.permgroup_id " .
						"RIGHT JOIN ".$dbo->table['pg_perm']." AS pgp ON pg.permgroup_id=pgp.permgroup_id " .
						"RIGHT JOIN ".$dbo->table['permission']." AS p ON pgp.perm_id=p.perm_id " .
						"WHERE u.user_name='".$this->_username."' AND ";
								for ($i=0; $i<count($permissiontype); $i++) {
									$query .= " p.perm_name='" . $permissiontype[$i] . "' ";
									if ($i != count($permissiontype)-1) {
										$query .= "OR ";
									}
								}

				$query = $dbo->prepare($query);
				
				if ($row = $dbo->getRows($query)) {
					if ($dbo->affected() == 1) {
						$pommo->_logger->addMsg("PERMISSION found for this plugin");
						return TRUE;	
					} else {
						Pommo::kill("Permission not found");
						return FALSE;
					}
				}
				
				
			} else {
				global $pommo;
				//$this->dbo =& $pommo->_dbo; 
				$dbo = clone $pommo->_dbo;
				
				//a = array();
				
				$query = "SELECT user_name, permgroup_name, perm_name " .
						"FROM ".$dbo->table['user']." AS u RIGHT JOIN ".$dbo->table['permgroup']." AS pg ON u.permgroup_id=pg.permgroup_id " .
						"RIGHT JOIN ".$dbo->table['pg_perm']." AS pgp ON pg.permgroup_id=pgp.permgroup_id " .
						"RIGHT JOIN ".$dbo->table['permission']." AS p ON pgp.perm_id=p.perm_id " .
						"WHERE u.user_name='".$this->_username."' AND p.perm_name='".$permissiontype."' ";
		
				$query = $dbo->prepare($query);
				
				if ($row = $dbo->getRows($query)) {
					if ($dbo->affected() == 1) {
						$pommo->_logger->addMsg("PERMISSION found for this plugin");
						return TRUE;	
					} else {
						Pommo::kill("Permission not found");
						return FALSE;
					}
				}
				
				Pommo::kill("Permission not found!");
				return FALSE;
			}
			
			Pommo::kill("Permission error");
			return FALSE;
		}
		return FALSE;
		
	} //dbCheckPermission

} //MultAuth
