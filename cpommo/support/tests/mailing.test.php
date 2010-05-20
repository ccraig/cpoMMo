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
define('_poMMo_support', TRUE);
require ('../../bootstrap.php');
$pommo->init();

Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');

echo 'Please Wait...';
ob_flush();
flush();

$code = PommoHelper::makeCode();

if(!PommoMailCtl::spawn($pommo->_baseUrl.'support/tests/mailing.test2.php?code='.$code,true)) 
	Pommo::kill('Initial Spawn Failed! You must correct this before poMMo can send mailings.');

sleep(6);

if (!is_file($pommo->_workDir . '/mailing.test.php')) {
	// make sure we can write to the file
	if (!$handle = fopen($pommo->_workDir . '/mailing.test.php', 'w')) 
		die('Unable to write to test file!');
	fclose($handle);
	unlink($pommo->_workDir.'/mailing.test.php');
	
	Pommo::kill('Initial Spawn Failed (test file not written)! You must correct this before poMMo can send mailings.');
}
	
$o = PommoHelper::parseConfig($pommo->_workDir . '/mailing.test.php');
unlink($pommo->_workDir.'/mailing.test.php') or die('could not remove mailing.test.php');

if(isset($o['error']))
	Pommo::kill('ERROR WITH RESAWN. SEE THE OUTPUT OF \'MAILING_TEST\' IN THE WORK DIRECTORY');

if (!isset($o['code']) || $o['code'] != $code)
	Pommo::kill('Spawning Failed. Codes did not match.');
	
if (!isset($o['spawn']) || $o['spawn'] == 0)
	Pommo::kill('Inital spawn success. Respawn failed!');

Pommo::kill('Initial spawn success. Respawn success. Spawning Works!');