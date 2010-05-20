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
 

class SimpleUser {

	var $_usertype;
	var $_uid;
	var $_username;
	var $_pass;
	var $_permissionLevel;
	
	var $_verifier;
	var $dbauth;
	var $sldapauth;
	var $qldapauth;
	
	function SimpleUser($username, $pass) {

		$this->_usertype = "simpleuser";
		$this->_uid = NULL;
		$this->_username = $username;
		$this->_pass = $pass;
		$this->_permissionLevel = 0;
		
		$this->_verifier = NULL;
		
		global $pommo;
		
		
		$this->dbauth = $pommo->_plugindata['authmethod']['dbauth'];
		$this->sldapauth = $pommo->_plugindata['authmethod']['simpleldapauth'];
		$this->qldapauth = $pommo->_plugindata['authmethod']['queryldapauth'];
		
		if ($pommo->_plugindata['authmethod']['dbauth']) {
			Pommo::requireOnce($pommo->_baseDir.'plugins/lib/auth/methods/class.dbauth.php');
			$this->_verifier['dbauth'] = new DbAuth();
			//$this->dbauth = $pommo->_plugindata['authmethod']['dbauth'];
		}
		if ($pommo->_plugindata['authmethod']['simpleldapauth']) {
			Pommo::requireOnce($pommo->_baseDir.'plugins/lib/auth/methods/class.simpleldapauth.php');
			$this->_verifier['simpleldapauth'] = new SimpleLdapAuth();
			//$this->sldapauth =  $pommo->_plugindata['authmethod']['simpleldapauth'];
		}
		if ($pommo->_plugindata['authmethod']['queryldapauth']) {
			Pommo::requireOnce($pommo->_baseDir.'plugins/lib/auth/methods/class.queryldapauth.php');
			$this->_verifier['queryldapauth'] = new QueryLdapAuth();
			//$this->qldapauth =  $pommo->_plugindata['authmethod']['queryldapauth'];
		}
		
	} //Constructor
	
	function __destruct() {
		unset($this->_uid);
		unset($this->_username);
		unset($this->_pass);
		unset($this->_permissionLevel);
	} //Destructor


	function getPermissionLevel() {
		return $this->_permissionLevel;
	}
	function getUserID() {
		return $this->_uid;
	}




	
	/**
	 *  authenticate with auth methods that are activated in the GENERAL PLUGIN SETUP
	 */
	function authenticate() {

		global $pommo;

			$dba = $sldapa = $qldapa = FALSE;
		
			
			// AUTH METHODS COMBINATIONS
			
			//TRUES
			if ($this->dbauth AND !$this->sldapauth AND !$this->qldapauth) {

				// only dbauth activated
				
				$dba = $this->_verifier['dbauth']->verifyUser($this->_username, md5($this->_pass));
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth active AND {$dba}.</div>');
				
				$this->dbIncreaseLoginTries($this->_username);
				if ($dba) {
					$this->dbWriteLastLogin($this->_username);
				}
				
				$this->_permissionLevel = $this->dbGetPermissionLevel();
				return $dba;
				
			} elseif (!$this->dbauth AND $this->sldapauth AND !$this->qldapauth) {
				
				// only simple ldapauth activated
				
				$sldapa = $this->_verifier['simpleldapauth']->verifyUser($this->_username, $this->_pass);
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: simpleldapauth active AND {$sldapa}.</div>');
				$this->_permissionLevel = $this->dbGetPermissionLevel();
				return $sldapa;
				
			} elseif (!$this->dbauth AND !$this->sldapauth AND $this->qldapauth) {
			
				//only query ldap auth activated
				
				$qldapa = $this->_verifier['queryldapauth']->verifyUser($this->_username, $this->_pass);
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: queryldapauth active AND {$qldapa}.</div>');
				$this->_permissionLevel = $this->dbGetPermissionLevel();
				return $qldapa;
			
			} elseif ($this->dbauth AND $this->sldapauth AND !$this->qldapauth) {
			
				// dbauth AND simple ldapauth activated
				
				$dba = $this->_verifier['dbauth']->verifyUser($this->_username, md5($this->_pass));
				$sldapa = $this->_verifier['simpleldapauth']->verifyUser($this->_username, $this->_pass);
				
				//TRUE
				if ($dba AND $sldapa) {
					
					//passed both
					$this->dbWriteLastLogin($this->_username);
					$this->dbIncreaseLoginTries($this->_username);
					
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, passed both: {$dba} - {$sldapa}.</div>');
					$this->_permissionLevel = $this->dbGetPermissionLevel();
					return TRUE;
					
				} elseif (!$dba AND $sldapa) {
					
					// not in db but ldap passed
					//TODO if (dbauth_writeldapusertodb)
					$this->dbAddLDAPUser($this->_username, md5($this->_pass));
					//$this->dbWriteLastLogin($this->_username);
					
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, ldap passed, db not: {$dba} - {$sldapa}.</div>');
					$this->_permissionLevel = $this->dbGetPermissionLevel();
					return TRUE;

				// FALSE
				} elseif ($dba AND !$sldapa) {
					//in db but not ldapauth
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active AND db passed, ldap not: {$dba} - {$sldapa}.</div>');
					$this->_permissionLevel = 0;
					return FALSE;
					
				} elseif (!$dba AND !$sldapa ) {
					// both not passed
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, both not passed: {$dba} - {$sldapa}.</div>');
					$this->_permissionLevel = 0;
					return FALSE;
					
				} else {
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, something else wrong: {$dba} - {$sldapa}.</div>');
					$this->_permissionLevel = 0;
					return FALSE;
				}
				
			
			
			//FALSES
			} elseif (!$this->dbauth AND !$this->sldapauth AND !$this->qldapauth) {
				$pommo->_logger->addMsg('SimpleUser: No authmethod set. See General Plugin Setup.');
				$this->_permissionLevel = 0;
				return FALSE;
			} else {
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: Corrupted auth methods.</div>');
				$this->_permissionLevel = 0;
				return FALSE;
			}
		
	} //authenticate
	


	function dbGetPermissionLevel() {
		
		global $pommo;
		//$this->dbo =& $pommo->_dbo; 
		$dbo = clone $pommo->_dbo;
		
		$a = array();
		
		$query = "SELECT user_permissionlvl FROM " . $dbo->table['user'] . 
			" WHERE user_name = '". $this->_username ."' LIMIT 1 "; 

		$query = $dbo->prepare($query);
		
		if ($row = $dbo->getRows($query))
			$a = $row['user_permissionlvl'];
		
		return $a;
	
	} //dbGetPermissionLevel



	function dbWriteLastLogin($username) {
	
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = "UPDATE ".$dbo->table['user']." SET user_lastlogin=NOW() WHERE user_name='".$username."' ";
		$query = $dbo->prepare($query);
		$dbo->query($query);
		
	} //dbWriteLastLogin
	
	function dbIncreaseLoginTries($username) {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;

		$query = "SELECT user_logintries FROM ".$dbo->table['user']." WHERE user_name='".$username."' LIMIT 1 ";
		$query = $dbo->prepare($query);
		
		$l = array();
		while ($row = $dbo->getRows($query)) {
			$l = $row['user_logintries'];
		}
		$l = $l + 1;
		$query2 = "UPDATE ".$dbo->table['user']." SET user_logintries=".$l." WHERE user_name='".$username."' ";
		$query2 = $dbo->prepare($query2);
		$dbo->query($query2);
				
	} //dbIncreaseLoginTries
	
	function dbAddLDAPUser($user, $pass) {

		global $pommo;
		$dbo = clone $pommo->_dbo;

		$query = "INSERT INTO ".$dbo->table['user']." (user_name, user_pass, permgroup_id, user_created) " .
				 "VALUES ('".$user."', '".md5($pass)."', NULL, NOW()) ";
		$query = $dbo->prepare($query);
		$dbo->query($query);

	} //addLDAPUser




} //SimpleUser


?>
