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

$maxRunTime = 80;
if (ini_get('safe_mode'))
	$maxRunTime = ini_get('max_execution_time') - 10;
else
	set_time_limit(0);
	
ignore_user_abort(true);

require ('../../bootstrap.php');
$pommo->init(array('noSession' => TRUE));

$code = (empty($_GET['code'])) ? null : $_GET['code'];

if (!$handle = fopen($pommo->_workDir . '/mailing.test.php', 'w')) 
	die('Unable to write to test file');

for($i=0; $i <= 90; $i+=5) {
	
	$fileContent = "<?php die(); ?>\n[code] = $code\n[time] = $i\n";
	
	rewind($handle);
	
	if (fwrite($handle, $fileContent) === FALSE) 
		die('Unable to write to test file');
	
	
	sleep(5);
}

fclose($handle);
	
die();