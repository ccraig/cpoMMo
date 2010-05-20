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

// authentication object. Handles logged in user, permission level.
class PommoAuth {

	var $_username;	// current logged in user (default: null|session value)
	var $_permissionLevel; // permission level of logged in user
	var $_requiredLevel; // required level of permission (default: 1)


	// default constructor. Get current logged in user from session. Check for permissions.
	function PommoAuth($args = array ()) {
		global $pommo;
		
		$defaults = array (
			'username' => null,
			'requiredLevel' => 0
		);
		$p = PommoAPI :: getParams($defaults, $args);
		
		if (empty($pommo->_session['username']))
			$pommo->_session['username'] = $p['username'];
		
		$this->_username = & $pommo->_session['username'];
		$this->_permissionLevel = $this->getPermissionLevel($this->_username);

		if ($p['requiredLevel'] > $this->_permissionLevel) {
			global $pommo;
			Pommo::kill(sprintf(Pommo::_T('Denied access. You must %slogin%s to access this page...'), '<a href="' . $pommo->_baseUrl . 'index.php?referer=' . $_SERVER['PHP_SELF'] . '">', '</a>'));
		}

	}

	// TODO -> extend this when multi-user support is implemented. For now default to 5 if a user
	// is logged in (successfully authenticated). 5 should be max (administrator/superuser privileges)
	
	/*
	 * corinna: OO Design proposal:
	 * Classes for the User type instead of a permission level
	 * interface iUser (maybe add this later -> less administration effort? for all this classes)
	 * admins are a Object AdminUser(derived from iUser)
	 * When multi user support is enabled the users are of the type class ExtendedUser, and this class has overloaded existing functions 
	 * and extended functionality (Permission Handling, ...)
	 * The problem with PHP is the non persistence, e.g. that the objects are deleted when the 
	 * requested files are interpreted... With servlets,... the user objects are organzized as i describe
	 * above but i don't know if there is a standard way for php?
	 */
	function getPermissionLevel($username = null) {
		if ($username)
			return 5;
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
	
	// Check if a user is authenticated (logged on)
	function isAuthenticated() {
		return (empty($this->_username)) ? false : true;
	}
}
?>
