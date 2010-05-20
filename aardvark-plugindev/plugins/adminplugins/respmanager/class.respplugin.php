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
//require_once ($pommo->_baseDir.'plugins/adminplugins/useradmin/respmanager/class.db_resphandler.php'); 
//require_once ($pommo->_baseDir.'/inc/class.pager.php');



class RespPlugin {
	

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	var $pluginname = "respmanager";	
	
	var $dbo;
	var $logger;
	var $pommo;
	
	var $respdbhandler;
	

	function RespPlugin($pommo) {
		$this->dbo = $pommo->_dbo;
		$this->logger = $pommo->_logger;
		$this->pommo = $pommo;
		
		$this->respdbhandler = new RespDBHandler($this->dbo);
	}
	function __destruct() {
	}

	function isActive() {
		// Parameter 'PLUGINNAME' is the uniquename of the plugin
		return $this->userdbhandler->dbPluginIsActive($this->pluginname);
	}
	
	function getPermission($user) {
		//TODO select the permissions from DB 
		return TRUE;
	}
	
	
	function execute($data) {	

		Pommo::requireOnce($this->pommo->_baseDir.'inc/classes/template.php');
		$smarty = new PommoTemplate();
		
		
		echo "<div style='color:blue;'>"; print_r($data); echo "</div>";
		
		if ($data['showAdd']) {
			$smarty->assign('showAdd' , TRUE);
			$smarty->assign('user', $this->respdbhandler->dbFetchUser());
			$smarty->assign('groups', $this->respdbhandler->dbGetGroups());
			
		} elseif ($data['showEdit']) {
			$editdata = $this->respdbhandler->dbFetchUserData($data['editid']);
			$smarty->assign('groups', $this->respdbhandler->dbGetGroups());
			$smarty->assign('user', $this->respdbhandler->dbFetchUser());
			
			$smarty->assign('edit', $editdata);
			$smarty->assign('showEdit', 'TRUE');
		} elseif ($data['showDel']) {
			$deldata = $this->respdbhandler->dbFetchUserData($data['delid']);
			$smarty->assign('del', $deldata);
			$smarty->assign('showDel', 'TRUE');
		}
		
		/*
		if ($data['action']) {
			$smarty->assign('action', $data['action']);
			$smarty->assign('showformid', $data['userid']);	//needed for forms
		}*/
		
		$resp = $this->respdbhandler->dbFetchRespGroups();
		//$resp = $this->respdbhandler->dbFetchRespMatrix();
		$smarty->assign('resp', $resp);
		$smarty->assign('nrresp', count($resp));
		
		/*
		$list = $this->respdbhandler->dbFetchRespLists();
		$smarty->assign('list', $list);
		$smarty->assign('nrlists', count($list));
		*/
		/*echo "<div style='color: red;'>";
		print_r($list); echo "</div>";*/
		
		
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/respmanager/resp_main.tpl');
		Pommo::kill();
		
	}
	
	
	
	
	function addResponsiblePerson($uid, $realname, $surname, $bounce) {
		return $this->respdbhandler->dbAddResponsiblePerson($uid, $realname, $surname, $bounce);
		
	}
	function deleteResponsiblePerson($uid) {
		return $this->respdbhandler->dbDeleteResponsiblePerson($uid);
		
	}
	function editResponsiblePerson($uid, $realname, $surname, $bounce) {
		return $this->respdbhandler->dbAddResponsiblePerson($uid, $realname, $surname, $bounce);
		
	}
	/*
	 //TODO some checks
	function addList($name, $desc, $userid) {
		return $this->listdbhandler->dbAddList($name, $desc, $userid);
	}
	function editList($listid, $name, $desc) {
		return $this->listdbhandler->dbEditList($listid, $name, $desc);
	}
	function deleteList($id, $userid) {
		return $this->listdbhandler->dbDeleteList($id, $userid);
	}
	*/
	
} //RespPlugin



?>

