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
 * Don't allow direct access to this file. Must be called from elsewhere
 */


class UserDBHandler { //implements iDbHandler {

	var $dbo;

	function UserDBHandler() {
	
		global $pommo;
		$this->dbo = $pommo->_dbo;
		
	}


	/** Returns if the Plugin itself is active */
	function & dbPluginIsActive($pluginname) {
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = "SELECT plugin_active FROM ". $dbo->table['plugin'] .
				 " WHERE plugin_uniquename='".$pluginname."' ";
		$query = $dbo->prepare($query);

		return $dbo->query($query, 0);
	
	} //dbPluginIsActive
	
	
	
	/* ---------- Custom DB fetch functions ---------- */
	
	/**
	 *  Get User Matrix with Permission Group Information
	 */
	function dbFetchUserMatrix() {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = "SELECT u.user_id, u.user_name, u.user_pass, pg.permgroup_name, u.user_created, " .
				 "u.user_lastlogin, u.user_logintries, u.user_lastedit, u.user_active " .
				 "FROM ".$dbo->table['user']." AS u LEFT JOIN ".$dbo->table['permgroup']." AS pg ON u.permgroup_id=pg.permgroup_id " .
				 "ORDER BY u.user_id";
		$query = $dbo->prepare($query);

		$i=0;
		while ($row = $dbo->getRows($query)) {
			
			//TODO if lastlogin in a time before created -> ERROR?????
			// installation time -> if login before install time??
			// or maybe if date > NOW()
			if ($row['user_lastlogin'] == "0000-00-00 00:00:00") {
			//TODO make some time checks	
			//if (checkdate($row['user_lastlogin'])) {
				$login = "never";
			} else {
				$login = $row['user_lastlogin'];
			}
			$user[$i] = array(
				'id' 	=> $row['user_id'],
				'name'	=> $row['user_name'],
				'pass'	=> $row['user_pass'],
				'perm'	=> $row['permgroup_name'],
				'created'	=> $row['user_created'],
				'lastlogin'=> $login,
				'logintries' => $row['user_logintries'],
				'lastedit' => $row['user_lastedit'],
				'active' => $row['user_active'],
				);
			$i++;
		}
		
		return $user;
		
	} //dbFetchUserMatrix
	
	
	/**
	 * Get Permissions Matrix
	 * Every Permission Set is stored in a GROUP, you can add a User to a Group, depending on his/her permission
	 */
	function dbFetchPermissionGroupMatrix() {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = "SELECT permgroup_id, permgroup_name, permgroup_desc FROM ". $dbo->table['permgroup'];
		$query = $dbo->prepare($query);
		
		$i=0;
		while ($row = $dbo->getRows($query)) {
			$group[$i] = array(
				'id' 	=> $row['permgroup_id'],
				'name'	=> $row['permgroup_name'],
				'perm'	=> $this->dbFetchPermForGroup($row['permgroup_id']),
				'desc'	=> $row['permgroup_desc'],
				);
			$i++;
		}
		return $group;
	}	

	/** 
	 * Fetch a Array that contains only ID / Permission Group Info
	 */
	function dbFetchPermNames() {
		
		$dbo = $this->dbo;
		
		$query = "SELECT permgroup_id, permgroup_name FROM ".$dbo->table['permgroup'];
		$query = $dbo->prepare($query);
		
		$i=0;
		while ($row = $dbo->getRows($query)) {
			$group[$i] = array(
				'id'	=> $row['permgroup_id'],
				'name'	=> $row['permgroup_name'],
				);
			$i++;
		}
		return $group;
	}
	
	
	
	
	// Problem: when user has no group GRP it is NULL and cannot be selected here
	/**
	 * Fetch User info for a single user ID
	 */
	function dbFetchUserInfo($userid) {
		
		$dbo = $this->dbo;
		
		$query = "SELECT u.user_id, u.user_name, u.user_pass, pg.permgroup_name, u.user_created, u.user_lastlogin, " .
				 "u.user_logintries, u.user_lastedit, u.user_active " .
				 "FROM ".$dbo->table['user']." AS u LEFT JOIN ".$dbo->table['permgroup']." AS pg ON u.permgroup_id=pg.permgroup_id " .
				 "WHERE u.user_id=".$userid; 
		$query = $dbo->prepare($query);

		while ($row = $dbo->getRows($query)) {
			
			//TODO if lastlogin VOR created -> 
			// some date checks?
			if ($row['user_lastlogin'] == "0000-00-00 00:00:00") {	//TIMESTAMP, not DATETIME
				$login = "-";
			} else {
				$login = $row['user_lastlogin'];
			}
			$user = array(
				'id' 		=> $row['user_id'],
				'name'		=> $row['user_name'],
				'pass'		=> $row['user_pass'],
				'perm'		=> $row['permgroup_name'],
				'created'	=> $row['user_created'],
				'lastlogin' => $login,
				'logintries' => $row['user_login_tries'],
			);
		}
		return $user;
	}
	
	
	/* TODO Permission_name unique??? */
	function dbFetchPermInfo($groupid) {
		
		$dbo = $this->dbo;
		
		$query = "SELECT permgroup_id, permgroup_name, permgroup_desc " .
				"FROM ".$dbo->table['permgroup']." WHERE permgroup_id=".$groupid;
		$query = $dbo->prepare($query);

		while ($row = $dbo->getRows($query)) {
			
			//$tmp = $this->dbFetchPermForGroup($row['permgroup_id']);
			//print_r($tmp);
			
			$group = array(
				'id' 	=> $row['permgroup_id'],
				'name'	=> $row['permgroup_name'],
				'perm'	=> 'blah', //Todo
				'desc'	=> $row['permgroup_desc'],
			);
		}
		return $group;
	}
	
	function dbFetchPermForGroup($groupid) {
		
		$dbo = $this->dbo;
		
		$query = "SELECT p.perm_id, p.perm_name, pgp.pgp_grant " .
				"FROM ".$dbo->table['permission']." AS p RIGHT JOIN ". $dbo->table['pg_perm'] ." AS pgp " .
				"ON p.perm_id=pgp.perm_id WHERE permgroup_id=".$groupid ." AND pgp.pgp_grant=TRUE ";
		$query = $dbo->prepare($query);

		$p = array();
		$i = 0;
		while ($row = $dbo->getRows($query)) {
			$p[$i] = array(
				'id'	=> $row['perm_id'],
				'name'	=> $row['perm_name'],
				'grant'	=> $row['pgp_grant'],
			);
			$i++;
		}

		//$p['groupid'] = $groupid;
		//print_r($p); echo "<br>";
		return $p;
	}


	/* -------------------- [user] USE CASES -------------------- */

	/**
	 * Add a new User to Database.
	 * No username can be double TODO: make some duplicate check for "User already exists."
	 * Table row is unique, check not needed? only logger binding!
	 */
	function dbAddUser($user, $pass, $perm) {

		//TODO some check
		global $pommo;
		$dbo = $this->dbo;

		if ($this->dbCheckUserName($user) == 0) {
			
			// We insert in DB only when the username does not exist
			$query = "INSERT INTO ".$dbo->table['user']." (user_name, user_pass, permgroup_id, user_created, " .
					 "user_lastlogin, user_logintries, user_lastedit, user_active ) " .
					 "VALUES ('".$user."', '".md5($pass)."', '".$perm."',  NOW(), '0000-00-00', 0, NOW(), TRUE)";
			$query = $dbo->prepare($query);		 
			
			// If query fails return the Error
			if (!$dbo->query($query)) {
				return  $dbo->getError();
			} else {
				$affected = $dbo->affected();
				return ($affected == 1) ? 1 : FALSE;
			}
		} else {
			$pommo->_logger->addMsg(_T('User already in DB.'));
		}
	}
	
	function dbDeleteUser($userid) {
		$dbo = $this->dbo;
		$query = "DELETE FROM ".$dbo->table['user']." WHERE user_id=".$userid." LIMIT 1";
		$query = $dbo->prepare($query);
	
		// If query fails return the error.
		if (!$dbo->query($query)) {
			return  $dbo->getError();
		} else {
			$affected = $dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	
	function dbEditUser($id, $user, $pass, $perm) {
		
		$dbo = $this->dbo;
		
		$active = TRUE; // in titel
		// We could change only one column but i prefer the atomic transaction idea of this
		// If we change 4 dates in a loop and the loop is somehow aborted/fails, then there is data 
		// changed and some unchanged.
		$query = "UPDATE ".$dbo->table['user']." SET user_name='".$user."', ".// user_pass='".md5($pass)."', " .
				 "permgroup_id='".$perm."', user_lastedit=NOW(), user_active=".$active." WHERE user_id=".$id;
		$query = $dbo->prepare($query);

		//TODO	//return "User {$id}->{$column}:{$newval} changed.<br>";
		if (!$dbo->query($query)) {
			return  $dbo->getError();
		} else {
			$affected = $dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	/*	OLD function 
	function dbUpdateUserData($id, $column, $newval) {
		$sql = $this->safesql->query("UPDATE %s SET %s='%s' WHERE user_id=%i",
			array('pommomod_user', $column, $newval, $id ) );
		$count = $this->dbo->query($sql);
		//TODO
		echo "<h1>User {$id}->{$column}:{$newval} changed.<br></h1>";
		return "User {$id}->{$column}:{$newval} changed.<br>";
	}*/	
	
	function dbCheckUserName($user) {
		
		$dbo = $this->dbo;
		
		$query = "SELECT user_name FROM ".$dbo->table['user']." WHERE user_name='".$user."'";
		$query = $dbo->prepare($query);
		
		$dbo->query($query);
		$count = $dbo->affected();
		return $count;
	
	}
	
	

	/* -------------------- [permission groups] USE CASES -------------------- */

	function dbAddPermGroup($name, $perm, $desc) {
			
		$dbo = $this->dbo;



		$query = "INSERT INTO ".$dbo->table['permgroup']." (permgroup_name, permgroup_desc) VALUES ('".$name."', '".$desc."')";
		$query = $dbo->prepare($query);

		// If query fails return the Error
		if ($dbo->query($query)) {

			//$affected = $dbo->affected();
			
			// if insert ok get last inserted id and insert the permissions
			$permgroupid = mysql_insert_id();
			$this->dbSetPermissions($permgroupid, $perm);
			
			
			//return ($affected == 1) ? 1 : FALSE;
			
			
		} else {
			return  $dbo->getError();
		}
		
	} //dbAddPermGroup
	

	
	
	
	function dbEditPermGroup($permid, $name, $perm, $permlvl, $desc) {
		$dbo = $this->dbo;
		$query = "UPDATE ".$dbo->table['permgroup']." SET permgroup_name='".$name."', permgroup_desc='".$desc."'  
				WHERE permgroup_id=".$permid;
		$query = $dbo->prepare($query);
		
		//TODO addMsg
		//return "User {$id}->{$column}:{$newval} changed.<br>";
		if (!$dbo->query($query)) {
			return  $dbo->getError();
		} else {
			$affected = $dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	
	function dbDeletePermGroup($permid) {
		$dbo = $this->dbo;
		$query = "DELETE FROM ".$dbo->table['permgroup']." WHERE permgroup_id=".$permid;
		$query = $dbo->prepare($query);
		
		$query3 = "DELETE FROM ".$dbo->table['pg_perm']." WHERE permgroup_id=".$permid;
		$query3 = $dbo->prepare($query3);
		
		$query2 = "UPDATE ".$dbo->table['user']." SET permgroup_id=NULL WHERE permgroup_id=".$permid;
		$query2 = $dbo->prepare($query2);
		
		// If query fails return the error.
		$dbo->query($query);
		$dbo->query($query3);
		$dbo->query($query2);
		/*if (!$dbo->query($query) OR !$dbo->query($query3)  OR !$dbo->query($query2)) {
			return  $dbo->getError();
		} else {
			$affected = $dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}*/
	}
	
	
	
	/*** PERMISSION HANDLING *****/
	
	function dbSetPermissions($permgroupid, $perm) {

		$dbo = $this->dbo;		
		
		$allperm = $this->dbFetchPermissions();
		echo "ALLPERM:";  print_r($allperm); echo "<br>";
		echo "PERM: "; print_r($perm); echo "<br>";
		
		
		// copy permission id to pgp table and set all permissions to FALSE
		$query = "INSERT INTO ".$dbo->table['pg_perm']." (permgroup_id, perm_id, pgp_grant) VALUES ";
				
			for ($i = 0; $i < count($allperm); $i++) {
				$query .= " (" . $permgroupid . ", " . $allperm[$i]['id'] . ", FALSE )";
				
				if ($i == count($allperm)-1) {
					$query .= "; ";
				} else {
					$query .= ", ";
				}
			}	
		$query = $dbo->prepare($query);
		$dbo->query($query);
		
		
		$query2 = "UPDATE ". $dbo->table['pg_perm'] ." SET pgp_grant=TRUE " .
				"WHERE permgroup_id=" . $permgroupid . " AND perm_id IN (". implode(', ', $perm) .")";
		$query2 = $dbo->prepare($query2);
		$dbo->query($query2);	
				
		print_r($query2);
				

		
		/*
$query = "UPDATE INTO ".$dbo->table['pg_perm']."  ";
		$query = $dbo->prepare($query);*/
	}


	function dbFetchPermissionGroups($groupid) {
		$dbo = $this->dbo;
		
		$a = array();
		
		/*$query = "SELECT p.perm_id, p.perm_name, pg.permgroup_id FROM " . $dbo->table['permission'] . " AS p " .
				"RIGHT JOIN " . $dbo->table['pg_perm'] . " AS pg ON p.perm_id=pg.perm_id WHERE pg.permgroup_id=".$groupid;*/
		$query = "SELECT p.perm_name FROM " . $dbo->table['permission'] . " AS p ";
		$query = $dbo->prepare($query);
		
		$i=0;
		while ($row = $dbo->getRows($query)) {
			$a[$i]['id'] = $row['perm_id'];
			$a[$i]['name'] = $row['perm_name'];
			$i++;
		}
		return $a;
	}


	
	function dbFetchPermissions() {
		$dbo = $this->dbo;
		
		$a = array();
		
		$query = "SELECT p.perm_id, p.perm_name, p.perm_cat FROM " . $dbo->table['permission'] . " AS p " .
				"ORDER BY p.perm_cat ";
		$query = $dbo->prepare($query);
		
		$i=0;
		while ($row = $dbo->getRows($query)) {
			$a[$i]['id'] = $row['perm_id'];
			$a[$i]['name'] = $row['perm_name'];
			$a[$i]['cat'] = $row['perm_cat'];
			$i++;
		}
		
		return $a;
	}

} //UserDBHandler


?>
