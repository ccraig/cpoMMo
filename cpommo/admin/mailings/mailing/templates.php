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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/templates.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/json.php');
$json = new PommoJSON();

$success = false;

if(isset($_POST['skip']) || (isset($_POST['template']) && !is_numeric($_POST['template'])))
	$success = true;
elseif(isset($_POST['load'])) {
	$template = current(PommoMailingTemplate::get(array('id' => $_POST['template'])));
	$pommo->_session['state']['mailing']['body'] = $template['body'];
	$pommo->_session['state']['mailing']['altbody'] = $template['altbody'];
	
	$success = true;
}
elseif(isset($_POST['delete'])) {
	$msg = (PommoMailingTemplate::delete($_POST['template'])) ?
		Pommo::_T('Template Deleted') :
		Pommo::_T('Error with deletion.');
	
	$json->add('callbackFunction','deleteTemplate');
	$json->add('callbackParams', array(
		'id' => $_POST['template'],
		'msg' => $msg)
	);
}
else {
	$smarty->assign('templates',PommoMailingTemplate::getNames());
	$smarty->display('admin/mailings/mailing/templates.tpl');
	Pommo::kill();
}

$json->serve($success);
?>