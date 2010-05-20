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

class UserPlugin { //implements plugin

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	var $pluginname = "useradmin";	
	var $userdbhandler;
	

	function UserPlugin() {
		$this->userdbhandler = new UserDBHandler();
	}
	
	function __destruct() {
		//UNSET
	}

	function isActive() {
		// Parameter 'PLUGINNAME' is the uniquename of the plugin
		return $this->userdbhandler->dbPluginIsActive($this->pluginname);
	}
	
	function getPermission($user) {
		//TODO select the permissions from DB 
		return TRUE;
	}
	
	
	// This should be named showUserMatrix()
	// But i think execute as main function for this plugin to show all the users is ok.
	function execute($data) {	
		
		global $pommo;
		
		// TODO test this
		if (!$this->isActive()) {
			//print_r("<b style='color:red;'>NOT ACTIVE!!! Try to enable useradmin plugin in ´the General Plugin setup</b>");
			Pommo::kill("PLUGIN NOT ACTIVE. Try to enable the 'useradmin' plugin in the General Plugin Setup." .
					" <a href='../../pluginconfig/config_main.php'>&raquo; go there</a> &nbsp;&nbsp;");
			//zurückleiten zu seite vorher??? permissions einfügen und $logger
			return;
		}
		
		
		
		// Smarty Init
		Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
		$smarty = new PommoTemplate();
		$smarty->assign('returnStr', Pommo::_T('poMMo User Manager'));
		

	/*	if ($data['showAddForm']) { // == 'addForm') {
			$smarty->assign('showAddForm', TRUE);
			$smarty->assign('usergroups', $this->userdbhandler->dbFetchPermNames());
			$smarty->assign('actionStr', 'Add new User');
			
		} elseif ($data['showDelForm']) { // == 'delForm') {
			$smarty->assign('showDeleteForm', TRUE);
			$smarty->assign('actionStr', 'Delete User');
			
			//Show deletion info
			$smarty->assign('userinfo', $this->userdbhandler->dbFetchUserInfo($data['userid']));
			
		} elseif ($data['showEditForm']) { // == 'editForm') {
			$smarty->assign('showEditForm', TRUE);
			$smarty->assign('actionStr', 'Edit User');
			
			echo "<div style='color: red'>";
			print_r($this->userdbhandler->dbFetchUserInfo($data['userid']));
			echo "</div>";
			
			//Show data to edit
			$smarty->assign('userinfo', $this->userdbhandler->dbFetchUserInfo($data['userid']));
			$smarty->assign('permgroups',  $this->userdbhandler->dbFetchPermNames());

		} elseif ($data['showGroupAddForm']) {
			$smarty->assign('showGroupAddForm', TRUE);
		} elseif ($data['showGroupDelForm']) {
			$smarty->assign('showGroupDelForm', TRUE);
			$smarty->assign('groupinfo', $this->userdbhandler->dbFetchPermInfo($data['groupid']));
		} elseif ($data['showGroupEditForm']) {
			$smarty->assign('showGroupEditForm', TRUE);
			$smarty->assign('groupinfo', $this->userdbhandler->dbFetchPermInfo($data['groupid']));
		}
		*/

		/* We need a sorting mechanism here too
		//if (empty($this->poMMo->_state)) {
			// State initialization for sorting options
			$pmState = array(
				'limit' => '10',
				'sortOrder' => 'DESC',
				'sortBy' => 'date'
			);
			$this->poMMo->stateInit('mailings_queue',$pmState);
		//}
		$limit = $this->poMMo->stateVar('limit',$data['mailings_queue']['limit']);
		$sortOrder = $this->poMMo->stateVar('sortOrder',$data['mailings_queue']['sortOrder']);
		$sortBy = $this->poMMo->stateVar('sortBy',$data['mailings_queue']['sortBy']);

		$smarty->assign('state',$this->poMMo->_state);
		*/
		/*	$action = $this->poMMo->stateVar('action',$data['action']);
		$userid = $this->poMMo->stateVar('userid',$data['userid']);
		$smarty->assign('action',$action);
		$smarty->assign('userid',$userid);
		*/
		/* Pager for later
		// Pager part $_GET['page']
		$p = new Pager();
		if ($p->findStart($limit) > $mailcount) $data['page'] = '1';
		$pages = $p->findPages($mailcount, $limit);
		$start = $p->findStart($limit); 
		$pagelist = $p->pageList($data['page'], $pages);
		 */
		
		
		// Permission Groups Matrix
		$perm =  $this->userdbhandler->dbFetchPermissionGroupMatrix();
		$smarty->assign('permgroups', $perm);
		$smarty->assign('nrperm' , count($perm)); 

		// User Matrix
		$user = $this->userdbhandler->dbFetchUserMatrix();
		$smarty->assign('user' , $user); 
		$smarty->assign('nrusers' , count($user)); 
		
									
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/usermanager/user_main.tpl');
		Pommo::kill();
		
	} //execute
	


	/* USE CASES user */
	
	function addUser($user, $pass, $passcheck, $group) {

		global $pommo;
		
		//TODO mache string aus permission -> soll array sein / SMARTY VALIDATOR
		if (empty($user) OR empty($pass) OR empty($passcheck)) {//OR empty($group)) {
			// No parameter should be empty
			$str = "({$user}, {$group})";
			$pommo->_logger->addMsg('Add User: Parameter is empty. ' . $str);	

		} else {
			
			//write to the database after password check (if its the same)
			if (md5($pass) == md5($passcheck)) { //was &&
				$ret = $this->userdbhandler->dbAddUser($user, $pass, $group);
				if (!is_numeric($ret)) {
					$pommo->_logger->addMsg("Add User: User could not be added: ".$ret);
					return FALSE;
				} else {
					if ($ret == 1) {
						$pommo->_logger->addMsg('Add User: User added.');
						return TRUE;
					} else {
						$pommo->_logger->addMsg(_T('Add User: Problem during adding user.'));	
						return FALSE;
					}
				}
				
			} else {
				$pommo->_logger->addMsg(_T('Add User: Password check failed.'));
				return FALSE;
			}
		}
	} //AddUser
	
	
	function deleteUser($userid) {
		global $pommo;
		if (!empty($userid)) {
			return $this->userdbhandler->dbDeleteUser($userid);
		} else {
			$pommo->_logger->addMsg('Could not delete: No user id given.'); // why _T not permitted
			return FALSE;
		}

	}
	
	function editUser($id, $user, $pass, $group) {
		//if eines leer -> fehler
		global $pommo;
		// Es darf nicht nogroup ausgewählt sein
		if ($group=='nogroup') {
			$pommo->_logger->addMsg("No Permissiongroup selected.");
			return FALSE;
		}
		
		// Nur das ändern das sich geändert hat? oder alle auf einmal
		$ret = $this->userdbhandler->dbEditUser($id, $user, $pass, $group);
		if ($ret == 1) {
			//Transaktion ok, 1 data altered
			return TRUE;
		} else {
			//Fehlermeldung über logger
			return FALSE;
		}
		
		
	}
	

	/* USE CASES permission group */
	//TODO Fehlerbehandlung
	
	function addPermGroup($name, $perm, $desc) {
		
		//Checks
		$ret = $this->userdbhandler->dbAddPermGroup($name, $perm, $desc);
		if ($ret == 1) {
			//Transaktion ok, 1 data altered
			return TRUE;
		} else {
			//Fehlermeldung über logger
			global $pommo;
			//$pommo->_logger->addMsg(printf(_T('Permission Group could not be added.')));
			return FALSE;
		}
	}
	
	function deletePermGroup($groupid) {
		return $this->userdbhandler->dbDeletePermGroup($groupid);
	}
	
	function editPermGroup($groupid, $name, $perm, $desc) {
		return $this->userdbhandler->dbEditPermGroup($groupid, $name, $perm, $desc);
	}
	
	

} //UserPlugin

?>
