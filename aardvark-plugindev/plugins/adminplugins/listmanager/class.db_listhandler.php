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

class ListDBHandler { //implements iDbHandler {

	var $dbo;
	var $safesql;


	function ListDBHandler($dbo) {
		$this->dbo = $dbo;
		$this->safesql = $dbo->_safeSQL; //get rid
	}

	/** Returns if the Plugin itself is active */
	function & dbPluginIsActive($pluginame) {
		$sql = $this->safesql->query("SELECT plugin_active FROM %s WHERE plugin_uniquename='%s' ", 
			array(pommomod_plugin, $pluginame) );
		return $this->dbo->query($sql, 0);	//row 0
	}
	
	
	function dbFetchLists() {
		$sql = $this->safesql->query("SELECT rp.user_id, u.user_name, l.list_id, l.list_name, l.list_desc, l.list_created, l.list_sentmailings, l.list_active, l.list_senderinfo " .
				"FROM %s AS l LEFT JOIN %s AS lrp ON l.list_id = lrp.list_id " .
				"LEFT JOIN %s AS rp ON lrp.user_id = rp.user_id  " .
				"LEFT JOIN %s AS u ON rp.user_id = u.user_id " .
				"ORDER BY u.user_name ",
				array('pommomod_list', 'pommomod_list_rp', 'pommomod_responsibleperson', 'pommomod_user'));
			
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$list[$i] = array(
				'uid'		=> $row['user_id'],
				'uname'		=> $row['user_name'],
				'lid'		=> $row['list_id'],
				'name'		=> $row['list_name'],
				'desc'		=> $row['list_desc'],
				'created'		=> $row['list_created'],
				'sentmailings'	=> $row['list_sentmailings'],
				'active'		=> $row['list_active'],
				'senderinfo'	=> $row['list_senderinfo'],
			);
			$i++;
		}
		//print_r($list);
		return $list;
	}




	/*function dbFetchUserLists() {
		$sql = $this->safesql->query("SELECT u.user_name, p.perm_name, l.list_name " .	// count(l.list_id) AS numlist
				"FROM %s AS u LEFT JOIN %s AS lu ON u.user_id=lu.user_id " .
				"LEFT JOIN %s AS l ON lu.list_id=l.list_id " .
				"LEFT JOIN %s AS p ON u.user_perm=p.perm_id " .
				"ORDER BY u.user_id ",	//GROUP BY u.user_id 
			array( 'pommomod_user', 'pommomod_list_user', 'pommomod_list', 'pommomod_perm' ) );	
			
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				//'user_id' 		=> $row['user_id'],
				'user_name'		=> $row['user_name'],
				'user_group'	=> $row['group_name'],
				'numlist'		=> $row['numlist'],
				'list_name'		=> $row['list_name'],
				//'list_user_data'	=> $row['list_user_data'],
			);
			$i++;
		}
		
		echo "<div style='background-color:red;'>"; echo $this->dbo->affected(); echo "</div>";
		return $user;
	}*/
	function dbFetchUserLists() {
		$sql = $this->safesql->query("SELECT u.user_id, u.user_name, p.perm_name, count(lu.list_id) AS numlist " .	
				"FROM %s AS u LEFT JOIN %s AS lu ON u.user_id=lu.user_id " .
				//"LEFT JOIN %s AS l ON lu.list_id=l.list_id " .
				"LEFT JOIN %s AS p ON u.perm_id=p.perm_id " .
				"GROUP BY u.user_id ORDER BY u.user_id ",
			array( 'pommomod_user', 'pommomod_list_rp', 'pommomod_perm' ) );	// als 3. 'pommomod_list'
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				'uid'		=> $row['user_id'],
				'name'		=> $row['user_name'],
				'perm'		=> $row['perm_name'],
				'numlist'	=> $row['numlist'],
			);
			$i++;
		}
		
		for ($i=0; $i < count($user); $i++) {
			if ($user[$i]['numlist'] > 0 ) {
				$user[$i]['lists'] = $this->dbGetListsForUser($user[$i]['uid']); //$row['list_id']
			}
		}
		
		return $user;
	}
	
	function dbGetListsForUser($userid) {
		$sql1 = $this->safesql->query("SELECT lu.user_id, l.list_id, l.list_name, l.list_desc " .
				"FROM %s AS l, %s AS lu WHERE l.list_id=lu.list_id AND lu.user_id=%i",	//
			array('pommomod_list', 'pommomod_list_rp', $userid) );
		$i=0;
		while ($row1 = $this->dbo->getRows($sql1)) {
			$lists[$i] = array(
				'user_id'		=> $row1['user_id'],
				'list_id'		=> $row1['list_id'],
				'list_name'		=> $row1['list_name'],
				'list_desc'		=> $row1['list_desc'],
			);
			$i++;
		}
		return $lists;
	}
	

	function dbAddList($name, $desc, $email, $user, $group) {
			// SELECT USER ID
			$sql = $this->safesql->query("INSERT INTO %s (list_name, list_desc, list_created, list_sentmailings, list_active, list_senderinfo) " .
					"VALUES ('%s', '%s', NOW(), '0', TRUE, 'Ab. S. Ender'); ",
				array('pommomod_list', $name, $desc ) ); //$email
			$sql2 = $this->safesql->query("INSERT INTO %s (list_id, user_id) VALUES (LAST_INSERT_ID(), '%s')", 
				array('pommomod_list_rp', $user) );
				
			if (!$this->dbo->query($sql) OR !$this->dbo->query($sql2)) {
				return  $this->_dbo->getError();
			} else {
				return TRUE;
				/*$affected = $this->dbo->affected();	return ($affected == 2) ? 1 : FALSE;*/
			}
	}
	
	function dbDeleteList($listid, $userid) {
		$sql = $this->safesql->query("DELETE FROM %s WHERE list_id=%i",
			array('pommomod_list', $listid ) );
		$sql2 = $this->safesql->query("DELETE FROM %s WHERE list_id=%i AND user_id=%i",
			array('pommomod_list_rp', $listid, $userid) );
		if (!$this->dbo->query($sql) OR !$this->dbo->query($sql2)) {
			return  $this->_dbo->getError();
		} else {
			return TRUE;
			/*$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;*/
		}
	}
	function dbEditList($listid, $name, $desc) {
		$sql = $this->safesql->query("UPDATE %s SET list_name='%s', list_desc='%s'  
				WHERE list_id=%i",
			array('pommomod_list', $name, $desc, $listid ) );
		if (!$this->dbo->query($sql)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	
	function dbGetListInfo($listid, $userid) {
		/*$sql = $this->safesql->query("SELECT l.list_id, l.list_name, lu.user_id " .
				"FROM %s AS lu, %s AS l WHERE l.list_id=%i AND lu.user_id=%i", 
			array('pommomod_list_user', 'pommomod_list', $listid) );*/
		$sql = $this->safesql->query("SELECT list_id, list_name, list_desc, list_created, list_sentmailings, list_active, list_senderinfo " .	//user_name
				"FROM %s WHERE list_id=%i LIMIT 1", //USER
			array('pommomod_list', $listid) );
		//$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$listinfo = array(	//[$i]
				'lid'		=> $row['list_id'],
				'lname'		=> $row['list_name'],
				'ldesc'		=> $row['list_desc'],
				'lcreated'	=> $row['list_sentmailings'],
				'lactive'	=> $row['list_active'],
				'lsenderinfo' => $row['list_senderinfo'],
				'uname'		=> $userid,
				//'user_name'		=> $row['user_name']
			);
			//$i++;
		}
		return $listinfo;
	}
	
	function & dbGetMailGroups($where = NULL) {
	
		$whereStr = '';
		if (is_numeric($where))
			$whereStr = " WHERE group_id=".$where." ";
			//$whereStr = ' WHERE group_id=\''.$where.'\'';
	
		$groups = array ();
		$sql = $this->safesql->query("SELECT group_id, group_name FROM %s %s ORDER BY group_name",
			array($this->dbo->table['groups'], $whereStr) );
			
		while ($row = $this->dbo->getRows($sql, TRUE)) {
			$groups[$row[0]] = $row[1];
		}
		return $groups;
	}

} //ListDBHandler

?>

