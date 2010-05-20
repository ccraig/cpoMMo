<?php
/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
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
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/json.php');
$json = new PommoJSON();

$mailing = current(PommoMailing::get(array('active' => TRUE)));

switch ($_GET['cmd']) {
	case 'cancel' : // cancel a mailing
	case 'restart' : // restart mailing
	case 'stop' :  // pause mailing
		$query = "
			UPDATE ".$dbo->table['mailing_current']."
			SET command='".$_GET['cmd']."'
			WHERE current_id=%i";
		$query = $dbo->prepare($query,array($mailing['id']));
		if (!$dbo->query($query))
			$json->fail();
		
		if($_GET['cmd'] == 'restart' || $_GET['cmd'] == 'cancel') 
			PommoMailCtl::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?id='.$mailing['id'].'&code='.$mailing['code']);
		break;
}
$json->success();
?>