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
define('_poMMo_support', TRUE);
require ('../../bootstrap.php');
$pommo->init();

function delDir($dirName) {
	global $pommo;
	
	if (empty ($dirName)) 
		return true;
		
	if (file_exists($dirName)) {
		$dir = dir($dirName);
		while ($file = $dir->read()) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirName . '/' . $file)) {
					delDir($dirName . '/' . $file);
				} else {
					unlink($dirName . '/' . $file) or die('File ' . $dirName . '/' . $file . ' couldn\'t be deleted!');
				}
			}
		}
		$dir->close();
		if ($dirName != $pommo->_workDir)
			@ rmdir($dirName) or die('Folder ' . $dirName . ' couldn\'t be deleted!');
	} else {
		return false;
	}
	return true;
}

echo (delDir($pommo->_workDir)) ? 'Work Directory Cleared' : 'Unable to Clear Work Directory -- Does it exist?';