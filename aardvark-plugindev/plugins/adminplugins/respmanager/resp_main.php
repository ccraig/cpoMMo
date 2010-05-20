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

require ('../../../bootstrap.php');

$pommo->init();	

require_once ($pommo->_baseDir.'plugins/adminplugins/respmanager/class.respplugin.php');
require_once ($pommo->_baseDir.'plugins/adminplugins/respmanager/class.db_resphandler.php'); 



$data = NULL;
$respplugin = new RespPlugin($pommo);



//GETPOST data
if ($_REQUEST['action']) {
	$data['action']	= $_REQUEST['action'];
	
	
	 if ($data['action'] == "showAdd") {
		$data['showAdd'] = TRUE;
		//$data['userid'] = $_REQUEST['userid'];		//$data['listid'] = $_REQUEST['listid'];
		if (!empty($_REQUEST['addResp'])) {
			$ret = $respplugin->addResponsiblePerson($_REQUEST['userid'], $_REQUEST['realname'], $_REQUEST['surname'], $_REQUEST['bounceemail']);
			if ($ret) { 
				$data['showAdd'] = FALSE;	
				Pommo::redirect($pommo->_baseUrl.'/plugins/adminplugins/useradmin/respmanager/resp_main.php');
			}
		}
	} elseif ($data['action'] == "showEdit") {
		$data['editid'] = $_REQUEST['editid'];
		$data['showEdit'] = TRUE;
		if (!empty($_REQUEST['editResp'])) {
			$ret = $respplugin->editResponsiblePerson($data['editid'], $_REQUEST['realname'], $_REQUEST['surname'], $_REQUEST['bounceemail']);
			if ($ret) $data['showEdit'] = FALSE;	
		}
	} elseif ($data['action'] == "showDel") {
		$data['delid'] = $_REQUEST['delid'];
		$data['showDel'] = TRUE;
		if (!empty($_REQUEST['delResp'])) {
			$ret = $respplugin->deleteResponsiblePerson($data['delid']);
			if ($ret) $data['showDel'] = FALSE;	
		}
	}

}



$respplugin->execute($data);


?>

