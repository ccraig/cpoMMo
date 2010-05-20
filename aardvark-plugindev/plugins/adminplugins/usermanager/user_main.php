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

Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/usermanager/class.db_userhandler.php');
Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/usermanager/class.userplugin.php');


// Generates template background and pagelist, logic to view, delete, send
$userplugin = new UserPlugin();


print_r($_REQUEST);


	/* USE CASES */
	if ($_REQUEST['AddUser']) {
		$ret = $userplugin->addUser($_REQUEST['username'], $_REQUEST['userpass'], $_REQUEST['userpasscheck'], $_REQUEST['usergroup']);
	}
	if ($_REQUEST['DeleteUser']) {
		$ret = $userplugin->deleteUser($_REQUEST['userid']);
	}
	if ($_REQUEST['EditUser']) {
		$ret = $userplugin->editUser($_REQUEST['userid'], $_REQUEST['username'], $_REQUEST['userpass'], $_REQUEST['usergroup']);
	}
	
	if ($_REQUEST['AddGroup']) {
		$ret = $userplugin->addPermGroup($_REQUEST['groupname'], $_REQUEST['groupperm'], $_REQUEST['groupdesc']);
	}
	if ($_REQUEST['DeleteGroup']) {
		$ret = $userplugin->deletePermGroup($_REQUEST['groupid']);
	}
	if ($_REQUEST['EditGroup']) {
		$ret = $userplugin->editPermGroup($_REQUEST['groupid'], $_REQUEST['groupname'], $_REQUEST['groupperm'], $_REQUEST['groupdesc']);
	}


$data = NULL;
$userplugin->execute($data);


?>

