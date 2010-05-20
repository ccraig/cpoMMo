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
$lang = false;
if (isset($_POST['lang'])) {
	define('_poMMo_lang', $_POST['lang']);
	$lang = true;
}
	
require('../bootstrap.php');
$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

$smarty->assign('header',array(
	'main' => 'poMMo '.$pommo->_config['version'],
	'sub' => sprintf(Pommo::_T('Powerful mailing list software for %s'),$pommo->_config['list_name']) 
	));

if($lang)
	$logger->addErr(Pommo::_T('You have changed the language for this session. To make this the default language, you must update your config.php file.'));

$smarty->assign('lang',($pommo->_slanguage) ? $pommo->_slanguage : $pommo->_language);	
$smarty->display('admin/admin.tpl');
Pommo::kill();
	