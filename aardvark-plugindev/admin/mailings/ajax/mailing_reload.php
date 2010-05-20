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

$pommo->init(array('noDebug' => TRUE, 'keep' => TRUE));
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

PommoAPI::stateReset(array('mailings_send','mailings_send2'));

$state =& PommoAPI::stateInit('mailings_send',array(
	'fromname' => $mailing['fromname'],
	'fromemail' => $mailing['fromemail'],
	'frombounce' => $mailing['frombounce'],
	'list_charset' => $mailing['charset'],
	'subject' => $mailing['subject'],
	'ishtml' => $mailing['ishtml'],
	'mailgroup' => $gid
	));

$altInclude = (empty($mailing['altbody'])) ? 'no' : 'yes';

$state =& PommoAPI::stateInit('mailings_send2',array(
	'body' => $mailing['body'],
	'altbody' => $mailing['altbody'],
	'altInclude' => $altInclude,
	'editorType' => 'wysiwyg'
	));

Pommo::redirect($pommo->_baseUrl.'admin/mailings/mailings_send.php');
?>