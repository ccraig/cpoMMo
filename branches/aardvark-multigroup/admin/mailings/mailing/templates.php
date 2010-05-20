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

if(!empty($_POST['template']) && is_numeric($_POST['template'])) {
	
	// check if we are to load a template
	if(isset($_POST['load'])) {
		$template = current(PommoMailingTemplate::get(array('id' => $_POST['template'])));
		$pommo->_session['state']['mailing']['body'] = $template['body'];
		$pommo->_session['state']['mailing']['altbody'] = $template['altbody'];
		
		$smarty->assign('success',3);
				
	}
	// check if we are to delete a template
	elseif(isset($_POST['delete'])) {
		if(PommoMailingTemplate::delete($_POST['template']))
			$logger->addMsg(Pommo::_T('Template Deleted'));
		else
			$logger->addMsg(Pommo::_T('Error with deletion.'));
	}
}

// check if we should skip
if(isset($_POST['template']) && !isset($_POST['delete']))
	$smarty->assign('success',3);
else
	$smarty->assign('templates',PommoMailingTemplate::getNames());
	
$smarty->display('admin/mailings/mailing/templates.tpl');
Pommo::kill();
?>