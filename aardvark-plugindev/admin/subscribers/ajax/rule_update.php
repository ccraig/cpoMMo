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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/rules.php');

$pommo->init(array('noDebug' => TRUE));
$dbo = & $pommo->_dbo;

switch($_POST['logic']) {
	case 'is_in':
	case 'not_in':
		PommoRules::addGroupRule($_POST['group'], $_POST['match'], $_POST['logic']);
		break;
	case 'true':
	case 'false':
		PommoRules::addBoolRule($_POST['group'], $_POST['match'], $_POST['logic']);
		break;
	case 'is':
	case 'not':
	case 'less':
	case 'greater':
		// unserialize the values -- they are given as 'v=123&v=abdv=defsd'
		$values = preg_split('/&?v=/',substr($_POST['value'],2));
		
		// urldecode string, remove if empty
		foreach($values as $key => $val) {
			if (!empty($val))
				$values[$key] = urldecode($val);
			else
				unset($values[$key]);
		}
		$values = array_unique($values);
		
		$type = (isset($_POST['type'])) ?
			$_POST['type'] : 'add';
		
		PommoRules::addFieldRule($_POST['group'], $_POST['match'], $_POST['logic'], $values, $type);
		break;
}
?>