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
define('_poMMo_support', TRUE);
require ('../../bootstrap.php');
$pommo->init(array('install' => TRUE));

Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');

$code = (empty($_GET['securityCode'])) ? null : $_GET['securityCode'];
$spawn = (!isset($_GET['spawn'])) ? 0 : ($_GET['spawn'] + 1);

$fileContent = "<?php die(); ?>\n[code] = $code\n[spawn] = $spawn\n";

if (!$handle = fopen($pommo->_workDir . '/mailing.test.php', 'w')) 
	die('Unable to write to test file');

if (fwrite($handle, $fileContent) === FALSE) 
	die('Unable to write to test file');
	
fclose($handle);

if($spawn > 0)
	die();

sleep(1);

$page = $pommo->_baseUrl.'support/tests/mailing.test2.php';
PommoMailCtl::respawn(array('code' => $code, 'spawn' => $spawn), $page);
	
die();