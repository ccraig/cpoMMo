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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$mailing = current(PommoMailing::get(array('id' => $_GET['mail_id'])));
if (empty($mailing))
	Pommo::kill('Unable to load mailing');
	
// change group name to ID
$groups = PommoGroup::getNames();
$gid = 'all';
foreach($groups as $group) 
	if ($group['name'] == $mailing['group'])
		$gid = $group['id'];

PommoAPI::stateReset(array('mailing'));

$dbvalues = PommoAPI::configGet(array(
	'list_wysiwyg'
));

// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('mailing',array(
	'fromname' => $mailing['fromname'],
	'fromemail' => $mailing['fromemail'],
	'frombounce' => $mailing['frombounce'],
	'list_charset' => $mailing['charset'],
	'wysiwyg' => $dbvalues['list_wysiwyg'],
	'mailgroup' => $gid,
	'subject' => $mailing['subject'],
	'body' => $mailing['body'],
	'altbody' => $mailing['altbody']
),
$_POST);

Pommo::redirect($pommo->_baseUrl.'admin/mailings/mailings_start.php');
?>