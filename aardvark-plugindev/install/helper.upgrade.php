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
 
// poMMo update routines

// upgrades poMMo
// returns bool (true if upgraded)
function PommoUpgrade() {
	global $pommo;
	
	// fetch the current/old revision
	$config = PommoAPI::configGet('revision');
	
	while($config['revision'] < $pommo->_revision) {
		if(!PommoRevUpgrade(intval($config['revision'])))
			return false;
		$config = PommoAPI::configGet('revision');
	}
	return true;
}

// upgrades to a revisions steps
function PommoRevUpgrade($rev) {
	global $pommo;
	$logger =& $pommo->_logger;
	$dbo =& $pommo->_dbo;
	
	switch ($rev) {
		case 26 : // Aardvark PR14

			// manually add the serial column
			$query = "ALTER TABLE ".$dbo->table['updates']." ADD `serial` INT UNSIGNED NOT NULL";
			if(!$dbo->query($query))
				Pommo::kill('Could not add serial column');
				
			if (!PommoInstall::incUpdate(1,
			"ALTER TABLE {$dbo->table['updates']} DROP `update_id` , DROP `update_serial`"
			,"Dropping old Update columns")) return false;
			
			if (!PommoInstall::incUpdate(2,
			"ALTER TABLE {$dbo->table['updates']} ADD PRIMARY KEY ( `serial` )"
			,"Adding Key to Updates Table")) return false;
			
			if (!PommoInstall::incUpdate(3,
			"CREATE TABLE {$dbo->table['mailing_notices']} (
				`mailing_id` int(10) unsigned NOT NULL,
				`notice` varchar(255) NOT NULL,
				`touched` timestamp NOT NULL,
				KEY `mailing_id` (`mailing_id`)
			)"
			,"Adding Mailing Notice Table")) return false;
			
			if (!PommoInstall::incUpdate(4,
			"ALTER TABLE {$dbo->table['mailing_current']} DROP `notices`"
			,"Dropping old Notice column")) return false;			
			
			// bump revision
			if (!PommoAPI::configUpdate(array('revision' => 27), true))
				return false;
		case 27 : // Aardvark PR14.1
			
			if (!PommoInstall::incUpdate(5,
			"CREATE TABLE {$dbo->table['subscriber_update']} (
				`email` varchar(60) NOT NULL,
  				`code` char(32) NOT NULL ,
  				`activated` datetime NULL default NULL ,
  				`touched` timestamp(14) NOT NULL,
				PRIMARY KEY ( `email` )
			)"
			,"Adding Update Activation Table")) return false;
			
			if (!PommoInstall::incUpdate(6,
			"INSERT INTO {$dbo->table['config']} VALUES ('public_history', 'off', 'Public Mailing History', 'off', 'on')"
			,"Adding configuration of Public Mailings")) return false;
			
			Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
			PommoHelperMessages::resetDefault();
			
			// bump revision
			if (!PommoAPI::configUpdate(array('revision' => 28,'version' => 'Aardvark PR14.2'), true))
				return false;
		
		case 28 : // Aardvark PR14.2
			// bump revision
			if (!PommoAPI::configUpdate(array('revision' => 29,'version' => 'Aardvark PR14.3'), true))
				return false;
		
		case 29: // Aardvark PR14.3
			// bump revision
			if (!PommoAPI::configUpdate(array('revision' => 30,'version' => 'Aardvark PR14.3.1'), true))
				return false;
		case 30: // Aardvark PR14.3.1
			// bump revision
			if (!PommoAPI::configUpdate(array('revision' => 31,'version' => 'Aardvark PR14.4'), true))
				return false;
			break;
		case 31: // Aardvark PR14.4
			// bump revision
			if (!PommoAPI::configUpdate(array('revision' => 32,'version' => 'Aardvark PR14.4.1'), true))
				return false;
		case 32: // Aardvark PR14.4.1
		
			if (!PommoInstall::incUpdate(7,
			"RENAME TABLE {$dbo->table['group_criteria']} TO {$dbo->table['group_rules']}"
			,"Renaming Group Rules Table")) return false;
			
			if (!PommoInstall::incUpdate(8,
			"ALTER TABLE {$dbo->table['group_rules']} CHANGE `criteria_id` `rule_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT"
			,"Renaming key column")) return false;
			
			if (!PommoAPI::configUpdate(array('revision' => 33,'version' => 'Aardvark SVN'), true))
				return false;
		case 33: // Aardvark PR14.4.1
			// gets executed by Upgrade from ^^
			
			if (!PommoInstall::incUpdate(9,
			"ALTER TABLE {$dbo->table['group_rules']} ADD `type` TINYINT( 1 ) NOT NULL DEFAULT '0'"
			,"Adding OR support to Group Rules")) return false;
			
			if (!PommoInstall::incUpdate(10,
			"INSERT INTO {$dbo->table['config']} (`config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES ('notices', '', '', 'off', 'off')"
			,"Enabling Notification of Subscriber List Changes")) return false;
			
		default: 
			return false;
	} 
	return true;
}

?>