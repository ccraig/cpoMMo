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

/**********************************
	INITIALIZATION METHODS
 *********************************/
require ('../../../bootstrap.php');
$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// Read user requested changes	
if (!empty($_POST['throttle-restore'])) {
	$input = array ('throttle_MPS' => 3, 'throttle_BPS' => 0, 'throttle_DP' => 10, 'throttle_DBPP' => 0,'throttle_DMPP' => 0);
	PommoAPI::configUpdate($input,TRUE);
}
elseif(!empty($_POST['throttle-submit'])) {
	
	$input = array();
	
	if(is_numeric($_POST['mps']) && $_POST['mps'] >= 0 && $_POST['mps'] <= 5)
		$input['throttle_MPS'] = $_POST['mps'];
		
	if(is_numeric($_POST['bps']) && $_POST['bps'] >= 0 && $_POST['bps'] <= 400)
		$input['throttle_BPS'] = $_POST['bps'];
		
	if(is_numeric($_POST['dp']) && $_POST['dp'] >= 5 && $_POST['dp'] <= 20)
		$input['throttle_DP'] = $_POST['dp'];
		
	if(is_numeric($_POST['dmpp']) && $_POST['dmpp'] >= 0 && $_POST['dmpp'] <= 5)
		$input['throttle_DMPP'] = $_POST['dmpp'];
		
	if(is_numeric($_POST['dbpp']) && $_POST['dbpp'] >= 0 && $_POST['dbpp'] <= 200)
		$input['throttle_DBPP'] = $_POST['dbpp'];
		
	
	if(!empty($input)) {
		PommoAPI::configUpdate($input,TRUE);
		$smarty->assign('output',Pommo::_T('Configuration Updated.'));
	}
	else 
		$smarty->assign('output',Pommo::_T('Please review and correct errors with your submission.'));	
}

$config= PommoAPI::configGet(array('throttle_MPS', 'throttle_BPS', 'throttle_DP', 'throttle_DBPP','throttle_DMPP'));


/*  slider conversions.. (in template)
 * 	var mps=p.x/40 || maxStr;
 *  var kbps=p.x*2 || maxStr;
 *  var dp=Math.round(p.x/13+5) || maxStr;
 *  var dmpp=Math.round(p.x/40) || maxStr;
 *  var dbpp=p.x;
 */

$smarty->assign('mps',$config['throttle_MPS']*40);
$smarty->assign('bps',$config['throttle_BPS']/2);
$smarty->assign('dp',($config['throttle_DP']-5)*13);
$smarty->assign('dmpp',$config['throttle_DMPP']*40);
$smarty->assign('dbpp',$config['throttle_DBPP']);

$smarty->display('admin/setup/ajax/config_throttle.tpl');
Pommo::kill();
?>