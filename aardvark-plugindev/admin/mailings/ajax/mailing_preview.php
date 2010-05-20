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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$config = PommoAPI::configGet('public_history');
if($config['public_history'] == 'on') {
	$pommo->init(array('noDebug' => TRUE, 'keep' => TRUE, 'authLevel' => 0));
} else {
	$pommo->init(array('noDebug' => TRUE, 'keep' => TRUE));	
}
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$input = (isset($_GET['mail_id'])) ? 
	current(PommoMailing::get(array('id' => $_GET['mail_id']))) :
	$input = $pommo->_session['state']['mailings_send2'];

die($input['body']);

?>