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

set_time_limit(0);

$code = PommoHelper::makeCode();

if(!PommoMailCtl::spawn($pommo->_baseUrl.'support/tests/mailing.runtime2.php?code='.$code)) 
	Pommo::kill('Initial Spawn Failed! You must correct this before poMMo can send mailings.');

echo 'Initial Run Time: '.ini_get('max_execution_time').' seconds <br>';
echo '<br/> This test takes at least 90 seconds. Upon completetion "SUCCESS" will be printed. If you do not see "SUCCESS", the max runtime should be set near the second highest "reported working" value.';
echo '<hr>';
echo '<b>Reported working value(s)</b><br />';
ob_flush(); flush();

sleep(5);

if (!is_file($pommo->_workDir . '/mailing.test.php')) {
	// make sure we can write to the file
	if (!$handle = fopen($pommo->_workDir . '/mailing.test.php', 'w')) 
		Pommo::kill('Unable to write to test file!');
	fclose($handle);
	unlink($pommo->_workDir.'/mailing.test.php');
	
	Pommo::kill('Initial Spawn Failed (test file not written to)! Test the mail processor.');
}

$die = false;
$time = 0;
while(!$die) {
	sleep(10);
	$o = PommoHelper::parseConfig($pommo->_workDir . '/mailing.test.php');
	if (!isset($o['code']) || $o['code'] != $code) {
		unlink($pommo->_workDir.'/mailing.test.php');
		Pommo::kill('Spawning Failed. Codes did not match.');	
	}
	if(!isset($o['time']) || $time >= $o['time'] || $o['time'] == 90)
		$die = true;
	$time = $o['time'];
		
	echo "$time seconds <br />";
	ob_flush(); flush();
}
unlink($pommo->_workDir.'/mailing.test.php');


if($time == 90)
	Pommo::kill('SUCCESS');

Pommo::kill('FAILED -- Your webserver or a 3rd party tool is force terminating PHP. Mailings may freeze. If you are having problems with frozen mailings, try setting the Mailing Runtime Value to '.($time-10).' or below');