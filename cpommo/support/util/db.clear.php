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
 
// Clears the entire database, resets auto increment values
 
/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_poMMo_support', TRUE);
require ('../../bootstrap.php');
$pommo->init();
	
$dbo =& $pommo->_dbo;


foreach($dbo->table as $id => $table) {
	if($id == 'config' || $id == 'updates' || $id == 'group_criteria' || $id == 'templates')
		continue;
		
	$query = "DELETE FROM ".$table;
	if(!$dbo->query($query))
		die('ERROR deleting '.$id); 
		
	$query = "ALTER TABLE ".$table." AUTO_INCREMENT = 1";
	if(!$dbo->query($query))
		die('ERROR setting AUTO_INCREMENT on '.$id); 
}
	
die('Database Reset.');