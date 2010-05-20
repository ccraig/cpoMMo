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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

function jsonKill($msg, $success = "false") {
	$json = "{success: $success, msg: \"".$msg."\"}";
	die($json);
}


$when = '';
foreach($_POST['grid'] as $order => $id) { // syntax for multi-row updates in in 1 query.
	$id = substr($id,2);
	$when .= $dbo->prepare("WHEN '%s' THEN '%s'",array($id,$order)).' ';
}

$query = "
	UPDATE ".$dbo->table['fields']."
	SET field_ordering = 
		CASE field_id ".$when." ELSE field_ordering END";
if (!$dbo->query($dbo->prepare($query)))
	jsonKill('Error Updating Order');
	
jsonKill(Pommo::_T('Order Updated.'), "true");
			