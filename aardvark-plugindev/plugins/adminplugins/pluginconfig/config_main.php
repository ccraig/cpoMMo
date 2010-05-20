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
$logger = & $pommo->_logger;

$pommo->requireOnce($pommo->_baseDir.'inc/helpers/validate.php');
$pommo->requireOnce($pommo->_baseDir.'plugins/adminplugins/pluginconfig/class.db_confighandler.php');
$pommo->requireOnce($pommo->_baseDir.'plugins/adminplugins/pluginconfig/class.pluginconfig.php');

$data = NULL;

//print_r($pommo);

$test = $pommo->_auth->dbCheckPermission('PLUGINCONF');
//echo "<b><i>TEST: " . $test . "</i></b><br>";


/**
	//print_r($data['setupid']);
		//$blah = PommoValidate::subscriberData($data['setupid']);
		//if ($blah) echo "<br>ok<br>"; else echo "<br>ned ok!<br>";
		//print_r($data['setupid']);
 */

	/* collection of POST data*/

	if ($_REQUEST['viewsetup']) {
		$data['setupid'] = $_REQUEST['setupid'];
	} 	
	
	if ($_REQUEST['changesetup']) {
		//Data to be changed
		$data['changeid'] = $_REQUEST['changeid'];
		$data['active'] = $_REQUEST['active'];
		$data['old'] = $_REQUEST['old'];
		$data['new'] = $_REQUEST['plugindata'];
	}
	
	if ($_REQUEST['switchplugin'] AND $_REQUEST['switchid']) {
		$data['switchid'] = $_REQUEST['switchid'];
		$data['active'] = $_REQUEST['active'];
	}
	
	if ($_REQUEST['switchcid'] AND $_REQUEST['switchcid']) { 
		$data['switchcid'] = $_REQUEST['switchcid'];
		$data['active'] = $_REQUEST['active'];
	}
	
	if ($_REQUEST['setallpluginsoff']) {
		$data['allpluginsoff'] = TRUE;
	}



$pluginconfig = new PluginConfig($pommo);

// Some Validation with a inherited class
//$blah = PommoValidate::subscriberData($_REQUEST); //$blah = FALSE;

$blah = "TRUE";

if ($blah) {
	//$data = $pluginconfig->extractData($_REQUEST); //validated data
	// data validator in the extract Data function??
	$pluginconfig->execute($data); //$data
} else { 
	Pommo::kill("config_main: Validation Error.");
}
	
// Pommo::redirect($pommo->_baseUrl.'plugins/adminplugins/pluginconfig/config_main.php');

?>
