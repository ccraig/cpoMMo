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
 
 class PommoHelperMaintenance {
 	
 	function perform() {
 		global $pommo;
 		PommoHelperMaintenance::memorizeBaseURL();
 		if(is_file($pommo->_workDir.'/import.csv'))
 			if (!unlink($pommo->_workDir.'/import.csv'))
 				Pommo::kill('Unable to remove import.csv');
 		return true;
 		
 	}
 	// write baseURL to maintenance.php in config file syntax (to be read back by embedded apps)
 	function memorizeBaseURL() {
 		global $pommo;
 		
 		if (!$handle = fopen($pommo->_workDir . '/maintenance.php', 'w'))
			Pommo::kill('Unable to prepare maintenance.php for writing');
			
		$fileContent = "<?php die(); ?>\n[baseURL] = \"$pommo->_baseUrl\"\n";
		
		if (!fwrite($handle, $fileContent)) 
			Pommo::kill('Unable to perform maintenance');
		
		fclose($handle);
 	}
 	
 	function rememberBaseURL() {
 		global $pommo;
 		$config = PommoHelper::parseConfig($pommo->_workDir . '/maintenance.php');
 		return $config['baseURL'];
 	}
 }
?>
