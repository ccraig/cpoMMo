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

class ListPlugin {
	

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	var $pluginname = "listmanager";	
	
	var $logger;
	var $pommo;
	
	var $listdbhandler;
	

	function ListPlugin($pommo) {
		$this->pommo = $pommo;
		$this->logger = $pommo->_logger;
		
		$this->listdbhandler = new ListDBHandler($pommo->_dbo);
	}
	function __destruct() {
	}

	function isActive() {
		// Parameter 'PLUGINNAME' is the uniquename of the plugin
		return $this->listdbhandler->dbPluginIsActive($this->pluginname);
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
			$smarty->assign('showAdd' , 'TRUE');
			$mailgroups = $this->listdbhandler->dbGetMailGroups();
			$smarty->assign('mailgroups', $mailgroups);
		} elseif ($data['showEdit']) {
			$listdata = $this->listdbhandler->dbGetListInfo($data['listid'], $data['userid']);
			$mailgroups = $this->listdbhandler->dbGetMailGroups();
			$smarty->assign('listdata', $listdata);
			$smarty->assign('mailgroups', $mailgroups);
			$smarty->assign('showEdit', 'TRUE');
		} elseif ($data['showDelete']) {
			$listdata = $this->listdbhandler->dbGetListInfo($data['listid'], $data['userid']);
			echo "<br><br>LISTDATA:<br>im"; print_r($listdata);
			$smarty->assign('listdata', $listdata);
			$smarty->assign('showDelete', 'TRUE');
		}
		
		/*
		if ($data['action']) {
			$smarty->assign('action', $data['action']);
			$smarty->assign('showformid', $data['userid']);	//needed for forms
		}*/
		
		
		$list = $this->listdbhandler->dbFetchLists();
		$smarty->assign('list' , $list);
		$smarty->assign('nrlists', count($list) );
		
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/listmanager/list_main.tpl');
		Pommo::kill();
		
	}
	
	
	//TODO some checks
	function addList($name, $desc, $email, $user, $group) {
		return $this->listdbhandler->dbAddList($name, $desc, $email, $user, $group);
	}
	function editList($listid, $name, $desc) {
		return $this->listdbhandler->dbEditList($listid, $name, $desc);
	}
	function deleteList($id, $userid) {
		return $this->listdbhandler->dbDeleteList($id, $userid);
	}
	
	
}



?>


