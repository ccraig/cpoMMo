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

class QueryLdapAuth {
	
	var $name = "queryldapauth";
	var $ldapconf;	


	function QueryLdapAuth() {
		$this->ldapconf = array();
		$this->getConfigFromDb();
	}
	function __destruct() {
		unset($this->ldapconf);
	}

	function getName() {
		return $this->name;
	}
	
	
 	/**
 	 * Query from a LDAP server
 	 * TODO: implement query
 	 */
 	function verifyUser($user, $md5pass) {

		global $pommo;

		$ldapconn = "";
		
		//$pommo->_logger->addMsg("<br> Testing user {$user} with pass {$md5pass}<br>");												//TODO weg

		//Construct server url 
		//TODO ADD MORE CHECKS
		$server = $this->ldapconf['queryldap_server'];
		if(stristr($server, $this->ldapconf['queryldap_port']) === FALSE) {
			$server .= $this->ldapconf['queryldap_port'];
		} 
		//echo "New server: {$server}<br>";

		// Connect to server
		//TODO Add more checks
		if ($this->ldapconf['queryldap_server']) {
			$ldapconn = ldap_connect($this->ldapconf['queryldap_server']);		// or die( "connect: Connection to {$this->ldapuri} unavailable.<br>" );	// is dirty
		} elseif ($server) {
			$ldapconn = ldap_connect($server);
		} else {
			$pommo->_logger->addErr("Host not reachable: {$this->ldapconf['queryldap_server']}. Check queryldapauth config.");
			return FALSE;
		}
		
		
		if ($ldapconn) {
			ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);

			//Set Protocol to LDAPv3
			if (!ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3)) {
				$pommo->_logger->addErr("Query LDAP Failed to set LDAP Protocol version to 3, TLS not supported.");
				return FALSE;
			}
			
			// if user or pass is empty
			if ( (empty($user)) || (empty($md5pass)) )  {

				$pommo->_logger->addMsg("Query LDAP Password or user field empty.");
				return FALSE;
				
			} else {

				// Check if DN Substring is provided in the input
				if(stristr($user, $this->ldapconf['queryldap_dn']) === FALSE) {
					//echo "-> {$this->ldapconf['simpleldap_dn']} not found. concatenating.<br>";				//TODO weg
					$user .= $this->ldapconf['queryldap_dn'];
				} /* else {
					echo "-> {$this->ldapconf['simpleldap_dn']} found.<br>";								//TODO weg
				}*/


				//What to do to get rid of the warning? 
				//Warning: ldap_bind() [function.ldap-bind]: Unable to bind to server: 
				//$ldapbind = ldap_bind($ldapconn, $user, $md5pass);
				//if ($ldapbind) {

				if (ldap_bind($ldapconn, $user, $md5pass)) {
					// Bind with this credentials went ok! Authentication ok!
					$pommo->_logger->addMsg("Query LDAP Authentication passed.");
					ldap_close($ldapconn);
					return TRUE;
				} else {
					//Invalid credentials, Authentication failed
					$pommo->_logger->addMsg("Query LDAP Authentication failed!");
					ldap_close($ldapconn);
					return FALSE;
				}
			}
		} else {
			//Connect not ok!
			$pommo->_logger->addMsg("Query LDAP Connect to LDAP/ADS DB failed.");
			ldap_close($ldapconn);
			return FALSE;
		}
		
		return FALSE; 
		
	} //verifyUser



	/**
	 * load configuration data stored in the database 
	 * specified in GENERAL PLUGIN SETUP
	 */ 
  	function getConfigFromDb() {

		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$data = array();
		
		$query = "SELECT d.data_name, d.data_value FROM " . $dbo->table['plugindata'] .
				 " AS d, " . $dbo->table['plugin'] . " AS p " .
				 "WHERE d.plugin_id=p.plugin_id AND p.plugin_uniquename='%s'";
		
		$query = $dbo->prepare($query, 
			array($this->name ) );

		while ($row = $dbo->getRows($query)) {
			$this->ldapconf["$row[data_name]"] = $row['data_value'];
		}

		return $data;
		
	} //getCOnfigFromDb



} //QueryLdapAuth

?>
