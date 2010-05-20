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
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init();
$logger = & $pommo->_logger;


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// fetch the mailing IDs
$mailingIDS = $_REQUEST['mailings'];
if(!is_array($mailingIDS))
	$mailingIDS = array($mailingIDS);
	

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/json.php');
$json = new PommoJSON(false); // do not toggle escaping
	
// EXAMINE CALL
switch ($_REQUEST['call']) {
	case 'notice':
		foreach($mailingIDS as $id) {
			$logger->AddMsg('<br /><br />###'. sprintf(Pommo::_T('Displaying notices for mailing %s'),PommoMailing::getSubject($id)).' ###<br /><br />');
			$notices = PommoMailing::getNotices($id);	
			$logger->AddMsg($notices);
		}
	break;
	
	case 'reload' :
	
		Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');

		$mailing = current(PommoMailing::get(array('id' => $_REQUEST['mailings'])));
		
		// change group name to ID
		$groups = PommoGroup::getNames();
		$gid = 'all';
		foreach($groups as $group) 
			if ($group['name'] == $mailing['group'])
				$gid = $group['id'];
		
		PommoAPI::stateReset(array('mailing'));
		
		// if this is a plain text mailing, switch body + altbody.
		if($mailing['ishtml'] == 'off') {
			$mailing['altbody'] = $mailing['body'];
			$mailing['body'] = null;
		}
		
		// Initialize page state with default values overriden by those held in $_REQUEST
		$state =& PommoAPI::stateInit('mailing',array(
			'fromname' => $mailing['fromname'],
			'fromemail' => $mailing['fromemail'],
			'frombounce' => $mailing['frombounce'],
			'list_charset' => $mailing['charset'],
			'mailgroup' => $gid,
			'subject' => $mailing['subject'],
			'body' => $mailing['body'],
			'altbody' => $mailing['altbody']
		));

		Pommo::redirect($pommo->_baseUrl.'admin/mailings/mailings_start.php');
	break;
	
	case 'delete' :
		$deleted = PommoMailing::delete($mailingIDS);
		$logger->addMsg(Pommo::_T('Please Wait').'...');
		
		$params = $json->encode(array('ids' => $mailingIDS));
		$smarty->assign('callbackFunction','deleteMailing');
		$smarty->assign('callbackParams',$params);
	break;
	
	default:
		$logger->AddErr('invalid call');
	break;
}

$smarty->display('admin/rpc.tpl');
?>