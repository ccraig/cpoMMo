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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/import.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$dupes = $tally = $flagged = 0;
$fp = fopen($pommo->_workDir.'/import.csv','r') 
	or die('Unable to open CSV file');
	
$includeUnsubscribed = isset($_REQUEST['excludeUnsubscribed']) ? false : true;

while (($row = fgetcsv($fp,2048,',','"')) !== FALSE) {
	$subscriber = array(
		'email' => false,
		'registered' => time(),
		'ip' => $_SERVER['REMOTE_ADDR'],
		'status' => 1,
		'data' => array());
	foreach ($row as $key => $col) {
		$fid =& $_POST['f'][$key];
		if (is_numeric($fid))
			$subscriber['data'][$fid] = $col;
		elseif ($fid == 'email' && PommoHelper::isEmail($col))
			$subscriber['email'] = $col;
		elseif ($fid == 'registered')
			$subscriber['registered'] = PommoHelper::timeFromStr($col);
		elseif ($fid == 'ip')
			$subscriber['ip'] = $col;
	}
	if ($subscriber['email']) {
		// check for dupe
		// TODO -- DO THIS IN BATCH ??
		if (PommoHelper::isDupe($subscriber['email'],$includeUnsubscribed)) {
			$dupes++;
			continue;
		}

		// validate/fix data
		if(!PommoValidate::subscriberData($subscriber['data'], array(
			'log' => false,
			'ignore' => true,
			'active' => false)))
			$subscriber['flag'] = 9;

		// add subscriber
		if (PommoSubscriber::add($subscriber)) {
			$tally++;
			if (isset($subscriber['flag']))
				$flagged++;
		}
	}

}
unlink($pommo->_workDir.'/import.csv');
echo ('<div class="warn"><p>'.sprintf(Pommo::_T('%s subscribers imported! Of these, %s were flagged to update their records.'),$tally, $flagged).'<p>'.sprintf(Pommo::_T('%s duplicates encountered.'),$dupes).'</p></div>');
die(Pommo::_T('Complete!').' <a href="subscribers_import.php">'.Pommo::_T('Return to').' '.Pommo::_T('Import').'</a>');
?>