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
require('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$map = array(
	'sent' => 1,
	'unsent' => 0,
	'error' => 2);
	
$nameMap = array (
	0 => 'Unsent_Subscribers',
	1 => 'Sent_Subscribers',
	2 => 'Failed_Subscribers'
);

$i = (isset($map[$_GET['type']])) ? $map[$_GET['type']] : false;
if ($i === false)
	die();
	
$query = "
	SELECT s.email 
	FROM ".$dbo->table['subscribers']." s
	JOIN ".$dbo->table['queue']." q ON (s.subscriber_id = q.subscriber_id)
	WHERE q.status = %i";
$query = $dbo->prepare($query,array($i));
$emails = $dbo->getAll($query,'assoc','email');

$o = '';
foreach($emails as $e)
	$o .= "$e\r\n";
	
$size_in_bytes = strlen($o);
header("Content-disposition:  attachment; filename=".$nameMap[$i].".txt; size=$size_in_bytes");
print $o;
exit;  