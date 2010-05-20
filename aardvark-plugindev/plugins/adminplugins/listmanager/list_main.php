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

$pommo->requireOnce($pommo->_baseDir.'plugins/adminplugins/listmanager/class.db_listhandler.php');
$pommo->requireOnce($pommo->_baseDir.'plugins/adminplugins/listmanager/class.listplugin.php');


$data = NULL;

$listplugin = new ListPlugin($pommo);


//GETPOST data
if ($_REQUEST['action']) {
	$data['action']	= $_REQUEST['action'];
	
	 if ($data['action'] == "delete") {
		$data['showDelete'] = TRUE;
		//$data['userid'] = $_REQUEST['userid'];
		$data['listid'] = $_REQUEST['listid'];
		if (!empty($_REQUEST['deleteList'])) {
			$ret = $listplugin->deleteList($data['listid'], $_REQUEST['userid']);
			if ($ret) $data['showDelete'] = FALSE;	
		}
	} elseif ($data['action'] == "edit") {
		$data['listid'] = $_REQUEST['listid'];
		$data['showEdit'] = TRUE;
		if (!empty($_REQUEST['editList'])) {
			$ret = $listplugin->editList($data['listid'], $_REQUEST['listname'], $_REQUEST['listdesc']);
			if ($ret) $data['showEdit'] = FALSE;	
		}
	} elseif ($data['action'] == "add") {
		$data['showAdd'] = TRUE;
		if (!empty($_REQUEST['addList'])) {
			$ret = $listplugin->addList($_REQUEST['listname'], $_REQUEST['listdesc'], 
				$_REQUEST['senderemail'], $_REQUEST['userarray'], $_REQUEST['grouparray']);
			if ($ret) $data['showAdd'] = FALSE;	
		}
	}
	
}


$listplugin->execute($data);


// Pommo::redirect($pommo->_baseUrl.'plugins/adminplugins/pluginconfig/config_main.php');

?>

