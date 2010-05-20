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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$pommo->toggleEscaping(); // _T and logger responses will be wrapped in htmlspecialchars


function jsonKill($msg, $success = "false", $ids = array()) {
	$json = "{success: $success, msg: \"".$msg."\", ids: [".implode(',',$ids)."]}";
	die($json);
}


$emails = array();
if (isset($_POST['emails'])) {
	$in = array_unique(preg_split("/[\s,]+/", $_POST['emails']));
	foreach($in as $email) {
		if (PommoHelper::isEmail($email))
			array_push($emails,$email);
	}
}

$c = 0;
if (count($emails) > 0)  {
	$ids = PommoSubscriber::getIDByEmail($emails);
	$c = PommoSubscriber::delete($ids);
}

if ($c == 0)
	jsonKill(Pommo::_T('No subscribers were removed.'),"false");

jsonKill(sprintf(Pommo::_T('You have removed %s subscribers!'), $c),"true", $ids);
	 
?>