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
$pommo->init();


$code = $_GET['code'];

echo 'Initial Run Time: '.ini_get('max_execution_time').' seconds <br>';
echo '<br/> This test takes at least 90 seconds. Upon completetion "SUCCESS" will be printed. If you do not see "SUCCESS", the max runtime should be set to the highest "reported working" value.';
echo '<hr>';
echo '<b>Reported working value(s)</b><br />';
ob_flush(); flush();

sleep(3);

if (!is_file($pommo->_workDir . '/mailing.test.php')) {
	// make sure we can write to the file
	if (!$handle = fopen($pommo->_workDir . '/mailing.test.php', 'w')) 
		die('Unable to write to test file!');
	fclose($handle);
	unlink($pommo->_workDir.'/mailing.test.php');
	
	die('Initial Spawn Failed (test file could not be written)! Did you try to "refresh" this test? close and try again.');
}

$die = false;
$time = 0;
while(!$die) {
	sleep(10);
	$o = PommoHelper::parseConfig($pommo->_workDir . '/mailing.test.php');
	if (!isset($o['code']) || $o['code'] != $code) {
		unlink($pommo->_workDir.'/mailing.test.php');
		die ('Spawning Failed. Codes did not match.');	
	}
	if(!isset($o['time']) || $time >= $o['time'] || $o['time'] == 90)
		$die = true;
	$time = $o['time'];
		
	echo "$time seconds <br />";
	ob_flush(); flush();
}
unlink($pommo->_workDir.'/mailing.test.php');


if($time == 90)
	die('SUCCESS');

die('FAILED -- A 3rd party tool or webserver "script timeout setting" is terminating PHP. You must adjust your max runtime value.');