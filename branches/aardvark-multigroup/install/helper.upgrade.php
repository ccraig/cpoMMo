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
 
function PommoUpgrade() {
	global $pommo;
	$dbo =& $pommo->_dbo;
		
	// fetch the current/old revision
	$config = PommoAPI::configGet('revision');
	
	// halts upgrade on failed query
	$GLOBALS['pommoLooseUpgrade'] = FALSE;
	
	// if forced upgrade was requested, fake an earlier version,
	//	disable die on failed queries
	if($config['revision'] == $pommo->_revision) {
		$config['revision'] = $config['revision'] - 1;
		$GLOBALS['pommoLooseUpgrade'] = TRUE;
		$dbo->dieOnQuery(false);
	}

	while($config['revision'] < $pommo->_revision) { 
		if(!PommoRevUpgrade(intval($config['revision'])))
			return false;
		$config = PommoAPI::configGet('revision');
	}
	$dbo->dieOnQuery(true);
	return true;
}

// update routines

// upgrades to a revisions steps
//	if strict is true, return on query failure, if false, continue
function PommoRevUpgrade($rev, $strict = true) {
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
		case 33: // -> Aardvark PR15
			
			if (!PommoInstall::incUpdate(9,
			"ALTER TABLE {$dbo->table['group_rules']} ADD `type` TINYINT( 1 ) NOT NULL DEFAULT '0'"
			,"Adding OR support to Group Rules")) return false;
			
			if (!PommoInstall::incUpdate(10,
			"INSERT INTO {$dbo->table['config']} (`config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES ('notices', '', '', 'off', 'off')"
			,"Enabling Notification of Subscriber List Changes")) return false;
			
			if (!PommoInstall::incUpdate(11,
			"ALTER TABLE {$dbo->table['fields']} CHANGE `field_type` `field_type` ENUM( 'checkbox', 'multiple', 'text', 'date', 'number', 'comment' ) DEFAULT NULL"
			,"Adding 'comments' field type")) return false;
			
			if (!PommoInstall::incUpdate(12,
			"ALTER TABLE {$dbo->table['mailing_notices']} ADD `id` SMALLINT UNSIGNED NULL"
			,"Adding id to mailing notices")) return false;
			
			if (!PommoInstall::incUpdate(13,
			"ALTER TABLE {$dbo->table['mailing_current']} CHANGE `command` `command` ENUM( 'none', 'restart', 'stop', 'cancel' ) NOT NULL DEFAULT 'none'"
			,"Adding cancel type to mailing commands")) return false;
			
			if (!PommoInstall::incUpdate(14,
			"INSERT INTO {$dbo->table['config']} (`config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES ('maxRuntime', '80', '', 'off', 'on')"
			,"Enabling Mailing Runtime to be set in Config")) return false;
			
			if (!PommoInstall::incUpdate(15,
			"INSERT INTO {$dbo->table['config']} (`config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES ('list_wysiwyg', 'on', '', 'off', 'off')"
			,"Persisting State of WYSIWYG Editor Toggle")) return false;
			
			if (!PommoInstall::incUpdate(16,
			"ALTER TABLE {$dbo->table['subscriber_data']} CHANGE `value` `value` CHAR( 60 ) NOT NULL"
			,"Tuning Subscriber Data Table")) return false;
			
			if (!PommoInstall::incUpdate(17,
			"ALTER TABLE {$dbo->table['subscribers']} CHANGE `email` `email` CHAR( 60 ) NOT NULL"
			,"Tuning Subscribers Table")) return false;
			
			if (!PommoInstall::incUpdate(18,
			"DROP TABLE {$dbo->table['subscriber_update']}"
			,"Dropping previous activate routines")) return false;
			
			if (!PommoInstall::incUpdate(19,
			"CREATE TABLE {$dbo->table['templates']} (`template_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR( 60 ) NOT NULL DEFAULT 'name',`description` VARCHAR( 255 ) NULL ,`body` MEDIUMTEXT NULL ,`altbody` MEDIUMTEXT NULL, PRIMARY KEY(`template_id`))"
			,"Adding mailing template support")) return false;
			
			// custom update 20, install default template
			$query = "
			SELECT serial FROM ".$dbo->table['updates']." 
			WHERE serial=%i";
			$query = $dbo->prepare($query,array('20'));
			if (!$dbo->records($query)) {
				$file = $pommo->_baseDir."install/sql.templates.php";
				if(PommoInstall::parseSQL(false,$file)) {
					$query = "INSERT INTO ".$dbo->table['updates']."(serial) VALUES(%i)";
					$query = $dbo->prepare($query,array('20'));	
					$dbo->query($query);
				}
			}
			
			// end of upgrade (break), no revision bump.
			break;
		default: 
			return false;
	} 
	return true;
}

?>