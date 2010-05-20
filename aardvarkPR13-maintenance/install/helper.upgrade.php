<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

// pass database object as argument
function bmUpgrade(& $dbo) {
	if (!is_numeric(pommo_revision))
		die('Can not read current revision');
	$oldRevision = getOldVersion($dbo);
	if ($oldRevision >= pommo_revision)
		return true; // eventually analyze $oldRevision & call an appropriate function name (ie. during a branch)

	return bmUpgradeAardvark($oldRevision, $dbo);

}

function bmUpgradeAardvark(& $revision, & $dbo, $failed = FALSE) {

	// get database value
	$dbRev = getOldVersion($dbo);

	switch ($revision) {
		case 6 : // ( <= Aardvark PR6 )

			if ($dbRev < $revision) {

				// Schema Changes
				$sql = 'CREATE TABLE `' . $dbo->table['updates'] . '` (' . ' `update_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, ' . ' `update_serial` INT UNSIGNED NOT NULL,' . ' PRIMARY KEY (`update_id`),' . ' INDEX (`update_serial`)' . ' )';
				if (!performUpdate($sql, $dbo, 1, 'Creating updates table', FALSE))
					$failed = TRUE;

				$sql = 'CREATE TABLE `' . $dbo->table['config'] . '` (' . ' `config_id` int(10) unsigned NOT NULL auto_increment,' . ' `config_name` varchar(64)  NOT NULL,' . ' `config_value` text  NOT NULL,' . ' `config_description` tinytext  NOT NULL,' . ' `autoload` enum(\'on\',\'off\')  NOT NULL default \'on\',' . ' `change` enum(\'on\',\'off\')  NOT NULL default \'on\',' . ' PRIMARY KEY (`config_id`,`config_name`),' . ' KEY `name` (`config_name`)' . ' )';
				if (!performUpdate($sql, $dbo, 2, 'Creating config table'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['subscriber_data'] . '` ADD `visible` ENUM(\'on\',\'off\') DEFAULT \'off\' NOT NULL AFTER `active`, ADD `ordering` TINYINT UNSIGNED NOT NULL AFTER `visible`';
				if (!performUpdate($sql, $dbo, 3, 'Adding demographics to subscriber_data'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['subscribers_data'] . '` DROP INDEX `active`, ADD INDEX `active` (`active`,`visible`,`ordering`)'; // Data Changes
				if (!performUpdate($sql, $dbo, 4, 'Changing indexes on subscriber_data'))
					$failed = TRUE;

				$sql = 'UPDATE `' . $dbo->table['subscribers_data'] . '` SET visible=\'on\', ordering=id';
				if (!performUpdate($sql, $dbo, 5, 'Bringing subscriber_data up to date'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['config'] . '` CHANGE `change` `user_change` ENUM(\'on\',\'off\') NOT NULL DEFAULT \'on\'';
				if (!performUpdate($sql, $dbo, 6, 'Renaming alter demographic in config table'))
					$failed = TRUE;

				if (!checkUpdate(7, $dbo)) { // LOAD DEFAULT CONFIG (if config hasn't already been loaded')
					$x = bmInstallConfig($dbo);
					if (!performUpdate($x, $dbo, 7, 'Installing Default Configuration Values', FALSE, TRUE))
						$failed = TRUE;
				}

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR6");
			}

			// bump revision for recursive call
			$revision = 7;
			break;

		case 7 : // (<= Aardvark PR7)

			if ($dbRev < $revision) {

				$sql = 'ALTER TABLE `' . $dbo->table['pending'] . '` CHANGE `type` `type` ENUM(\'add\',\'del\',\'change\',\'password\') NOT NULL DEFAULT \'add\'';
				if (!performUpdate($sql, $dbo, 12, 'Allowing DB to track password changes'))
					$failed = TRUE;

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR7");
			}

			// bump revision for recursive call
			$revision = 8;
			break;

		case 8 : // (<= Aardvark PR7.1)

			if ($dbRev < $revision) {

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR7.1");
			}

			// bump revision for recursive call
			$revision = 9;
			break;

		case 9 : // (<= Aardvark PR8)

			if ($dbRev < $revision) {
				$sql = 'ALTER TABLE `' . $dbo->table['groups_criteria'] . '` CHANGE `gid` `group_id` INT UNSIGNED NOT NULL DEFAULT \'0\'';
				if (!performUpdate($sql, $dbo, 14, 'Fixing group_id in groups_criteria'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['groups_criteria'] . '` CHANGE `target` `demographic_id` TINYINT(3) UNSIGNED NOT NULL DEFAULT \'0\'';
				if (!performUpdate($sql, $dbo, 15, 'Changing target to demographic_id in groups_criteria'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['groups_criteria'] . '` CHANGE `id` `criteria_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT';
				if (!performUpdate($sql, $dbo, 16, 'Renaming id to criteria_id in groups_criteria'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['groups'] . '` CHANGE `id` `group_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT';
				if (!performUpdate($sql, $dbo, 17, 'Fixing group_id in groups'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['groups'] . '` DROP INDEX `name`';
				if (!performUpdate($sql, $dbo, 18, 'Droping name from index in groups'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['groups'] . '` CHANGE `name` `group_name` TINYTEXT NOT NULL';
				if (!performUpdate($sql, $dbo, 19, 'Renaming name to group_name in groups'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['groups_criteria'] . '` DROP INDEX `gid`';
				if (!performUpdate($sql, $dbo, 20, 'Dropping gid from index in groups_criteria'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['groups_criteria'] . '` ADD INDEX(`group_id`)';
				if (!performUpdate($sql, $dbo, 21, 'Adding index group_id to groups_criteria'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['groups'] . '` ADD `group_cacheTally` INT UNSIGNED NOT NULL, ADD `group_cacheTime` TIMESTAMP';
				if (!performUpdate($sql, $dbo, 22, 'Adding cache demographics to groups'))
					$failed = TRUE;

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR8");
			}

			$revision = 10;
			break;

		case 10 : // (<= Aardvark PR8.1)

			if ($dbRev < $revision) {
				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR8.1");
			}

			$revision = 11;
			break;

		case 11 : // (<= Aardvark PR8.2)

			if ($dbRev < $revision) {
				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR8.2");
			}

			$revision = 12;

			break;

		case 12 : // (<= Aardvark PR 8.3)

			if ($dbRev < $revision) {
				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR8.3");
			}

			$revision = 13;
			break;

		case 13 : // AARDVARK PR9

			if ($dbRev < $revision) {

				$sql = 'ALTER TABLE `' . $dbo->table['pending'] . '` CHANGE `type` `type` ENUM(\'add\',\'del\',\'change\',\'password\') NULL DEFAULT NULL';
				if (!performUpdate($sql, $dbo, 23, 'Fixing pending type to allow passwords'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['old_subscribers_data'] . '` RENAME `demographics`;';
				if (!performUpdate($sql, $dbo, 24, 'Renaming subscriber_data table to demographics'))
					$failed = TRUE;

				// bump demographic IDs to get rid of the zero.. note, this hurts group filters, they'll have to be deducted!
				$sqlA = array ();
				$sqlA[] = 'UPDATE `' . $dbo->table['demographics'] . '` SET id=id+101';
				$sqlA[] = 'UPDATE `' . $dbo->table['demographics'] . '` SET id=id-100';
				if (!performUpdate($sqlA, $dbo, 40, 'Bumping id value of every demographic'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['demographics'] . '` CHANGE `id` `demographic_id` TINYINT(4) NOT NULL AUTO_INCREMENT';
				if (!performUpdate($sql, $dbo, 25, 'Changing demographics key to demographic_id'))
					$failed = TRUE;

				$sqlA = array ();
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['groups'] . '` CHANGE `group_cacheTime` `group_cacheTime` timestamp(12) NULL';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['mailing_current'] . '` CHANGE `started` `started` timestamp(12) NULL default NULL';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['mailing_current'] . '` CHANGE `finished` `finished` timestamp(12) NULL default NULL';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['mailing_history'] . '` CHANGE `started` `started` timestamp(12) NULL default NULL';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['mailing_history'] . '` CHANGE `finished` `finished` timestamp(12) NULL default NULL';
				if (!performUpdate($sqlA, $dbo, 26, 'Changing timestamp types'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['subscribers'] . '` CHANGE `id` `subscriber_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT';
				if (!performUpdate($sql, $dbo, 27, 'Changing subscribers key to subscriber_id'))
					$failed = TRUE;

				$sql = 'CREATE TABLE `' . $dbo->table['subscribers_data'] . '` ( `data_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `demographic_id` INT UNSIGNED NOT NULL, ' . ' `subscriber_id` INT UNSIGNED NOT NULL, ' . ' `value` TINYTEXT NOT NULL,' . ' INDEX (`demographic_id`, `subscriber_id`))';
				if (!performUpdate($sql, $dbo, 28, 'Create new subscriber data table'))
					$failed = TRUE;

				if (!checkUpdate(29, $dbo)) { // copy subscriber info to new table
					$x = TRUE;
					$sql = 'SELECT * FROM ' . $dbo->table['subscribers'];
					while ($row = $dbo->getRows($sql)) {
						for ($i = 0; $i < 10; $i++) {
							if (!empty ($row['d' . $i])) {
								$sql = 'INSERT INTO ' . $dbo->table['subscribers_data'] . ' SET demographic_id=\'' . $i . '\', subscriber_id=\'' . $row['subscriber_id'] . '\', value=\'' . db2db($row['d' . $i]) . '\'';
								if (!$dbo->query($sql))
									$x = FALSE;
							}
						}
					}
					if (!performUpdate($x, $dbo, 29, 'Copying subscriber data to demographics table', FALSE, TRUE)) {
						$failed = TRUE;
						$sql = 'DELETE FROM ' . $dbo->table['subscribers_data']; // cleanup any partial data so it's not repeated..'
						$dbo->query($sql);
					}
				}

				// ** FAIL POINT -- DO NOT PROCEED BEYOND IF FAIL.
				if ($failed)
					return FALSE;

				$sql = 'ALTER TABLE `' . $dbo->table['subscribers'] . '` DROP `d0`, DROP `d1`, DROP `d2`, DROP `d3`, DROP `d4`, DROP `d5`, DROP `d6`, DROP `d7`, DROP `d8`, DROP `d9`';
				if (!performUpdate($sql, $dbo, 30, 'Cleaning up subscribers table...'))
					$failed = TRUE;

				$sql = ' CREATE TABLE `' . $dbo->table['pending_data'] . '` ( `data_id` bigint( 20 ) unsigned NOT NULL auto_increment ,' . ' `demographic_id` int( 10 ) unsigned NOT NULL default \'0\',' . ' `pending_id` int( 10 ) unsigned NOT NULL default \'0\',' . ' `value` tinytext,' . ' PRIMARY KEY ( `data_id` ) ,' . ' KEY `demographic_id` ( `demographic_id` , `pending_id` ) )';
				if (!performUpdate($sql, $dbo, 31, 'Making new pending_data table...'))
					$failed = TRUE;

				if (!checkUpdate(31, $dbo)) { // copy subscriber info to new table
					$x = TRUE;
					$sql = 'SELECT * FROM ' . $dbo->table['pending'];
					while ($row = $dbo->getRows($sql)) {
						for ($i = 0; $i < 10; $i++) {
							if (!empty ($row['d' . $i])) {
								$sql = 'INSERT INTO ' . $dbo->table['pending_data'] . ' SET demographic_id=\'' . $i . '\', subscriber_id=\'' . $row['subscriber_id'] . '\', value=\'' . db2db($row['d' . $i]) . '\'';
								if (!$dbo->query($sql))
									$x = FALSE;
							}
						}
					}
					if (!performUpdate($x, $dbo, 31, 'Copying pending data', FALSE, TRUE)) {
						$failed = TRUE;
						$sql = 'DELETE FROM ' . $dbo->table['pending_data']; // cleanup any partial data so it's not repeated..'
						$dbo->query($sql);
					}
				}

				$sqlA = array ();
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['demographics'] . '` CHANGE `type` `type` ENUM(\'checkbox\',\'multiple\',\'text\',\'date\',\'year\',\'check\',\'select\') NULL DEFAULT NULL';
				$sqlA[] = 'UPDATE `' . $dbo->table['demographics'] . '` SET type=\'checkbox\' WHERE type=\'check\'';
				$sqlA[] = 'UPDATE `' . $dbo->table['demographics'] . '` SET type=\'multiple\' WHERE type=\'select\'';
				$sqlA[] = 'UPDATE `' . $dbo->table['demographics'] . '` SET type=\'date\' WHERE type=\'year\'';
				if (!performUpdate($sqlA, $dbo, 32, 'Fixing demographic types'))
					$failed = TRUE;

				if (!$failed)
					$sql = 'ALTER TABLE `' . $dbo->table['demographics'] . '` CHANGE `type` `type` ENUM(\'checkbox\',\'multiple\',\'text\',\'date\',\'number\') NULL DEFAULT NULL';
				if (!performUpdate($sql, $dbo, 33, 'Removing old demographic types'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['demographics'] . '` CHANGE `demographic_id` `demographic_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT';
				if (!performUpdate($sql, $dbo, 34, 'Changing demographic key'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['demographics'] . '` CHANGE `ordering` `ordering` SMALLINT UNSIGNED NOT NULL DEFAULT \'0\'';
				if (!performUpdate($sql, $dbo, 35, 'Updating demographic ordering'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['demographics'] . '` CHANGE `active` `demographic_active` ENUM(\'on\',\'off\') NOT NULL DEFAULT \'off\', CHANGE `visible` `demographic_visible` ENUM(\'on\',\'off\') NOT NULL DEFAULT \'off\', CHANGE `ordering` `demographic_ordering` SMALLINT(5) UNSIGNED NOT NULL DEFAULT \'0\', CHANGE `nickname` `demographic_name` VARCHAR(60) NULL DEFAULT NULL, CHANGE `prompt` `demographic_prompt` VARCHAR(60) NULL DEFAULT NULL, CHANGE `type` `demographic_type` ENUM(\'checkbox\',\'multiple\',\'text\',\'date\',\'number\') NULL DEFAULT NULL, CHANGE `normally` `demographic_normally` VARCHAR(60) NULL DEFAULT NULL, CHANGE `options` `demographic_options` TEXT NULL DEFAULT NULL, CHANGE `required` `demographic_required` ENUM(\'on\',\'off\') NOT NULL DEFAULT \'off\'';
				if (!performUpdate($sql, $dbo, 36, 'Renaming demographic columns'))
					$failed = TRUE;

				$sql = 'UPDATE `' . $dbo->table['config'] . '` SET `user_change` = \'on\' WHERE `config_name` = \'list_exchanger\' LIMIT 1';
				if (!performUpdate($sql, $dbo, 37, 'Allowing mail exchanger to be changed'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['demographics'] . '` DROP `demographic_visible`';
				if (!performUpdate($sql, $dbo, 38, 'Dropping "visible" column from demographics table'))
					$failed = TRUE;

				$sql = 'CREATE TABLE `' . $dbo->table['subscribers_flagged'] . '` (`flagged_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `subscribers_id` INT UNSIGNED NOT NULL, `flagged_type` ENUM(\'update\') NULL, INDEX (`subscribers_id`, `flagged_type`))';
				if (!performUpdate($sql, $dbo, 39, 'Creating flagged subscribers table'))
					$failed = TRUE;

				if (!checkUpdate(40, $dbo)) { // copy subscriber info to new table
					$x = TRUE;
					$sql = 'SELECT * FROM ' . $dbo->table['pending'];
					while ($row = $dbo->getRows($sql)) {
						for ($i = 0; $i < 10; $i++) {
							if (!empty ($row['d' . $i])) {
								$sql = 'INSERT INTO ' . $dbo->table['pending_data'] . ' SET demographic_id=\'' . $i . '\', pending_id=\'' . $row['id'] . '\', value=\'' . db2db($row['d' . $i]) . '\'';
								if (!$dbo->query($sql))
									$x = FALSE;
							}
						}
					}
					if (!performUpdate($x, $dbo, 40, 'Copying pending data to pending_data table', FALSE, TRUE)) {
						$failed = TRUE;
						$sql = 'DELETE FROM ' . $dbo->table['pending_data']; // cleanup any partial data so it's not repeated..'
						$dbo->query($sql);
					}
				}

				// ** FAIL POINT -- DO NOT PROCEED BEYOND IF FAIL.
				if ($failed)
					return FALSE;

				$sqlA = array ();
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['pending'] . '` CHANGE `id` `pending_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['pending'] . '` DROP `d0`, DROP `d1`, DROP `d2`, DROP `d3`, DROP `d4`, DROP `d5`, DROP `d6`, DROP `d7`, DROP `d8`, DROP `d9`';
				if (!performUpdate($sqlA, $dbo, 41, 'Cleaning up pending table...'))
					$failed = TRUE;

				// deduce group value to compensate
				$sqlA = array ();
				$sqlA[] = 'UPDATE `' . $dbo->table['groups_criteria'] . '` SET demographic_id=demographic_id+1';
				$sqlA[] = 'UPDATE `' . $dbo->table['subscribers_data'] . '` SET demographic_id=demographic_id+1';
				$sqlA[] = 'UPDATE `' . $dbo->table['pending_data'] . '` SET demographic_id=demographic_id+1';
				if (!performUpdate($sqlA, $dbo, 42, 'Compensating for the demographic id bump across tables'))
					$failed = TRUE;

				$sqlA = array ();
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['subscribers'] . '` CHANGE `subscriber_id` `subscribers_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['subscribers_data'] . '` CHANGE `subscriber_id` `subscribers_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT \'0\'';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['subscribers_data'] . '` CHANGE `value` `value` VARCHAR(60) NOT NULL';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['subscribers_data'] . '` DROP INDEX `demographic_id`';
				if (!performUpdate($sqlA, $dbo, 43, 'Making demographic name and index adjustments.'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['subscribers_data'] . '` ADD INDEX `s_plus_demo_id` (`demographic_id`,`subscribers_id`)';
				if (!performUpdate($sql, $dbo, 44, 'Adding demographic+subscriber_id Index to data table.'))
					$failed = TRUE;
				$sql = 'ALTER TABLE `' . $dbo->table['subscribers_data'] . '` ADD INDEX `val_plus_demo` (`value`,`demographic_id`)';
				if (!performUpdate($sql, $dbo, 45, 'Adding demographic+value Index to data table.'))
					$failed = TRUE;
				$sql = 'ALTER TABLE `' . $dbo->table['subscribers_data'] . '` ADD INDEX `subscribers_id` (`subscribers_id`)';
				if (!performUpdate($sql, $dbo, 46, 'Adding subscriber_id Index to data table.'))
					$failed = TRUE;
				$sql = 'ALTER TABLE `' . $dbo->table['subscribers_data'] . '` ADD INDEX `subscribers_id_2` (`subscribers_id`,`value`)';
				if (!performUpdate($sql, $dbo, 47, 'Adding subscriber_id+value Index to data table.'))
					$failed = TRUE;
				$sql = 'ALTER TABLE `' . $dbo->table['pending'] . '` ADD INDEX ( `email` )';
				if (!performUpdate($sql, $dbo, 48, 'Indexing Pending Table.'))
					$failed = TRUE;

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR9");
			}

			$revision = 14;
			break;

		case 14 : // AARDVARK PR9.1

			if ($dbRev < $revision) {

				$sql = 'ALTER TABLE `' . $dbo->table['pending'] . '` ADD INDEX(`type`)';
				if (!performUpdate($sql, $dbo, 49, 'Adding type index to pending table.'))
					$failed = TRUE;

				$sql = 'INSERT INTO `' . $dbo->table['config'] . '` (`config_id`, `config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES (NULL, \'smtp_host\', \'\', \'\', \'off\', \'on\'), (NULL, \'smtp_port\', \'\', \'\', \'off\', \'on\'), (NULL, \'smtp_auth\', \'\', \'\', \'off\', \'on\'), (NULL, \'smtp_user\', \'\', \'\', \'off\', \'on\'), (NULL, \'smtp_pass\', \'\', \'\', \'off\', \'on\');';
				if (!performUpdate($sql, $dbo, 50, 'Adding SMTP Support.'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['pending'] . '` ADD `newEmail` VARCHAR(60) NULL AFTER `email`;';
				if (!performUpdate($sql, $dbo, 51, 'Adding ability for subscriber to update their email.'))
					$failed = TRUE;

				$sql = 'UPDATE `' . $dbo->table['config'] . '` SET `autoload` = \'off\' WHERE `config_id` IN(1,2,8,9,10,11,12,14,15,16,17)';
				if (!performUpdate($sql, $dbo, 52, 'Protecting config variables by disabling autoload.'))
					$failed = TRUE;

				$sqlA = array ();
				$sqlA[] = 'UPDATE `' . $dbo->table['config'] . '` SET `config_value` = \'\' WHERE `config_id` =5 AND `config_name` = \'site_success\' LIMIT 1';
				$sqlA[] = 'UPDATE `' . $dbo->table['config'] . '` SET `autoload` = \'off\', `user_change` = \'on\' WHERE `config_id` = 5 AND `config_name` = \'site_success\' LIMIT 1;';
				if (!performUpdate($sqlA, $dbo, 53, 'Enabling Success URL redirection.'))
					$failed = TRUE;

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR9.1");
			}

			$revision = 15;

			break;

		case 15 : // AARDVARK PR9.2

			if ($dbRev < $revision) {

				$sql = 'ALTER TABLE `' . $dbo->table['mailing_current'] . '` ADD `altbody` MEDIUMTEXT NULL AFTER `body`;';
				if (!performUpdate($sql, $dbo, 54, 'Adding support for optional altbody.'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['mailing_history'] . '` ADD `altbody` MEDIUMTEXT NULL AFTER `body`;';
				if (!performUpdate($sql, $dbo, 55, 'Adding altbody to mailing_history.'))
					$failed = TRUE;

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR9.2");
			}

			$revision = 16;
			break;

		case '16' : // Aardvark CVS

			$sqlA = array ();
			if ($dbRev < $revision) {
				$sqlA[] = 'CREATE TABLE `' . $dbo->table['queue'] . '` (' . ' `queue_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, ' . ' `email` VARCHAR(60) NOT NULL, ' . ' UNIQUE (`email`)' . ' )' . ' TYPE = myisam;';
				$sqlA[] = 'CREATE TABLE `' . $dbo->table['queue_working'] . '` (' . ' `queue_id` INT UNSIGNED NOT NULL, ' . ' `smtp_id` ENUM(\'1\',\'2\',\'3\',\'4\') NOT NULL,' . ' PRIMARY KEY (`queue_id`),' . ' INDEX (`smtp_id`)' . ' )' . ' TYPE = myisam;';
				$sqlA[] = 'DROP TABLE `mailing_queue`';
				if (!performUpdate($sqlA, $dbo, 56, 'Creating new mailing queue schema.'))
					$failed = TRUE;

				$sql = 'INSERT INTO `' . $dbo->table['config'] . '` (`config_id`, `config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES (NULL, \'site_confirm\', \'\', \'\', \'off\', \'on\');';
				if (!performUpdate($sql, $dbo, 57, 'Adding Confirm URL.'))
					$failed = TRUE;

				$sql = 'UPDATE `' . $dbo->table['config'] . '` SET `user_change` = \'on\' WHERE `config_id` = 12 AND `config_name` = \'list_confirm\' LIMIT 1;';
				if (!performUpdate($sql, $dbo, 58, 'Enable toggling of email confirmation.'))
					$failed = TRUE;

				$sqlA = array ();
				$sqlA[] = 'INSERT INTO `' . $dbo->table['config'] . '` (`config_id`, `config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES (NULL, \'smtp_1\', \'\', \'\', \'off\', \'off\'), (NULL, \'smtp_2\', \'\', \'\', \'off\', \'off\'), (NULL, \'smtp_3\', \'\', \'\', \'off\', \'off\'), (NULL, \'smtp_4\', \'\', \'\', \'off\', \'off\');';
				$sqlA[] = 'DELETE FROM ' . $dbo->table['config'] . ' WHERE config_name IN (\'smtp_host\', \'smtp_port\', \'smtp_pass\', \'smtp_user\', \'smtp_auth\')';
				if (!performUpdate($sqlA, $dbo, 59, 'Adding support for multiple SMTP servers.'))
					$failed = TRUE;

				$sqlA = array ();
				$sqlA[] = 'INSERT INTO `' . $dbo->table['config'] . '` (`config_id`, `config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES (NULL, \'throttle_MPS\', \'3\', \'\', \'off\', \'on\'), (NULL, \'throttle_BPS\', \'0\', \'\', \'off\', \'on\'), (NULL, \'throttle_DMPP\', \'0\', \'\', \'off\', \'on\'), (NULL, \'throttle_DBPP\', \'0\', \'\', \'off\', \'on\'), (NULL, \'throttle_DP\', \'10\', \'\', \'off\', \'on\'), (NULL, \'throttle_SMTP\', \'individual\', \'\', \'off\', \'on\');';
				$sqlA[] = 'DELETE FROM ' . $dbo->table['config'] . ' WHERE config_name IN (\'mailMax\', \'mailSize\', \'mailDelay\', \'mailNum\')';
				if (!performUpdate($sqlA, $dbo, 60, 'Adding Throttler Engine.'))
					$failed = TRUE;

				$sqlA = array ();
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['mailing_current'] . '` CHANGE `errors` `notices` LONGTEXT NULL DEFAULT NULL';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['mailing_history'] . '` DROP `errors`';
				if (!performUpdate($sqlA, $dbo, 61, 'Updating mailing table to handle notices.'))
					$failed = TRUE;

				$sql = 'INSERT INTO `' . $dbo->table['config'] . '` (`config_id`, `config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES (NULL, \'dos_processors\', \'0\', \'\', \'on\', \'off\');';
				if (!performUpdate($sql, $dbo, 62, 'Adding DOS (denial of service) protection to mail processor.'))
					$failed = TRUE;

				// remove dupes first...	

				$dupes = array (); // holds an array of IDs to delete
				$bemails = array ();
				$sql = 'select s1.subscribers_id,s1.email from ' . $dbo->table['subscribers'] . ' s1 LEFT JOIN ' . $dbo->table['subscribers'] . ' s2 ON s1.email = s2.email WHERE s1.subscribers_id <> s2.subscribers_id';
				while ($row = $dbo->getRows($sql)) {
					if (!isset ($bemails[$row['email']]))
						$bemails[$row['email']] = $row['subscribers_id'];
					else {
						$dupes[] = $bemails[$row['email']];
						$bemails[$row['email']] = $row['subscribers_id'];
					}
				}
				if (!empty ($dupes)) {
					foreach ($dupes as $dupeid) {
						$sql = 'DELETE from ' . $dbo->table['subscribers'] . ' WHERE subscribers_id=\'' . $dupeid . '\' LIMIT 1';
						$dbo->query($sql);
						$sql = 'DELETE from ' . $dbo->table['subscribers_data'] . ' WHERE subscribers_id=\'' . $dupeid . '\'';
						$dbo->query($sql);
					}
				}

				$dupes = array (); // holds an array of IDs to delete
				$bemails = array ();
				$sql = 'select s1.pending_id,s1.email from ' . $dbo->table['pending'] . ' s1 LEFT JOIN ' . $dbo->table['pending'] . ' s2 ON s1.email = s2.email WHERE s1.pending_id <> s2.pending_id';
				$dbo->query($sql);
				while ($dbo->getRows($sql)) {
					if (!isset ($bemails[$row['email']]))
						$bemails[$row['email']] = $row['pending_id'];
					else {
						$dupes[] = $bemails[$row['email']];
						$bemails[$row['email']] = $row['pending_id'];
					}
				}
				if (!empty ($dupes)) {
					foreach ($dupes as $dupeid) {
						$sql = 'DELETE from ' . $dbo->table['pending'] . ' WHERE pending_id=\'' . $dupeid . '\' LIMIT 1';
						$dbo->query($sql);
						$sql = 'DELETE from ' . $dbo->table['pending_data'] . ' WHERE pending_id=\'' . $dupeid . '\'';
						$dbo->query($sql);
					}
				}

				$sqlA = array ();
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['subscribers'] . '` DROP INDEX `email`';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['pending'] . '` DROP INDEX `email`';
				if (!performUpdate($sqlA, $dbo, 63, 'Dropping old email indexes on subscribers and pending tables.'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['subscribers'] . '` ADD UNIQUE(`email`)';
				if (!performUpdate($sql, $dbo, 64, 'Enforcing Unique email on subscribers table.'))
					$failed = TRUE;

				$sql = 'ALTER TABLE `' . $dbo->table['pending'] . '` ADD UNIQUE(`email`)';
				if (!performUpdate($sql, $dbo, 65, 'Enforcing Unique email on pending table.'))
					$failed = TRUE;

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR10");
			}

			$revision = 17;

			break;

		case 17 : // AARDVARK PR10.1

			if ($dbRev < $revision) {
				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR10.1");
			}

			$revision = 18;
			break;

		case 18 : // AARDVARK PR 10.2

			if ($dbRev < $revision) {

				$sql = 'DROP TABLE ' . $dbo->table['queue_working'];
				if (!performUpdate($sql, $dbo, 66, 'Dropping the queue_working table.'))
					$failed = TRUE;

				$sqlA = array ();
				$sqlA[] = 'ALTER TABLE ' . $dbo->table['queue'] . ' DROP `queue_id`';
				$sqlA[] = 'ALTER TABLE ' . $dbo->table['queue'] . ' ADD `smtp_id` ENUM(\'0\',\'1\',\'2\',\'3\',\'4\') NOT NULL DEFAULT \'0\'';
				$sqlA[] = 'ALTER TABLE ' . $dbo->table['queue'] . ' ADD INDEX(`smtp_id`)';
				if (!performUpdate($sqlA, $dbo, 67, 'Modifying the queue table for efficiency.'))
					$failed = TRUE;

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark 10.2");
			}

			$revision = 19;
			break;

		case 19 : // AARDVARK PR11

			if ($dbRev < $revision) {

				$sqlA = array ();
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['config'] . '` DROP INDEX `name`';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['config'] . '` DROP `config_id`';
				if (!performUpdate($sqlA, $dbo, 68, 'Enforcing unique name on config table.'))
					$failed = TRUE;

				$sql = 'INSERT INTO `' . $dbo->table['config'] . '` (`config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES (\'messages\', \'\', \'\', \'off\', \'off\');';
				if (!performUpdate($sql, $dbo, 69, 'Adding customizable messages to configuration.'))
					$failed = TRUE;

				require_once (bm_baseDir . '/inc/db_procedures.php');
				dbResetMessageDefaults('all');

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR11");
			}

			$revision = 20;
			break;

		case 20 : // Aardvark PR11.1
			
			if ($dbRev < $revision) {

				$sql = 'ALTER TABLE `' . $dbo->table['demographics'] . '` RENAME `' . $dbo->table['subscriber_fields'] . '`';
				if (!performUpdate($sql, $dbo, 70, 'Renaming demographic table to fields.'))
					$failed = TRUE;

				$sqlA = array ();
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['subscriber_fields'] . '` CHANGE `demographic_id` `field_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `demographic_active` `field_active` ENUM(\'on\',\'off\') NOT NULL DEFAULT \'off\', CHANGE `demographic_ordering` `field_ordering` SMALLINT(5) UNSIGNED NOT NULL DEFAULT \'0\', CHANGE `demographic_name` `field_name` VARCHAR(60) NULL DEFAULT NULL, CHANGE `demographic_prompt` `field_prompt` VARCHAR(60) NULL DEFAULT NULL, CHANGE `demographic_type` `field_type` ENUM(\'checkbox\',\'multiple\',\'text\',\'date\',\'number\') NULL DEFAULT NULL, CHANGE `demographic_normally` `field_normally` VARCHAR(60) NULL DEFAULT NULL, CHANGE `demographic_options` `field_options` TEXT NULL DEFAULT NULL, CHANGE `demographic_required` `field_required` ENUM(\'on\',\'off\') NOT NULL DEFAULT \'off\'';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['groups_criteria'] . '` CHANGE `demographic_id` `field_id` TINYINT(3) UNSIGNED NOT NULL DEFAULT \'0\'';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['pending_data'] . '` CHANGE `demographic_id` `field_id` INT(10) UNSIGNED NOT NULL DEFAULT \'0\'';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['subscribers_data'] . '` CHANGE `demographic_id` `field_id` INT(10) UNSIGNED NOT NULL DEFAULT \'0\'';
				if (!performUpdate($sqlA, $dbo, 71, 'Migrating references from demographics to fields'))
					$failed = TRUE;

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR11.1");
			}

			$revision = 21;

			break;
		case 21:
		
			if ($dbRev < $revision) {
				
				$sqlA = array();
				$sqlA[] = 'INSERT INTO `' . $dbo->table['config'] . '` (`config_name`, `config_value`, `config_description`, `autoload`, `user_change`) VALUES (\'list_charset\', \'UTF-8\', \'\', \'off\', \'on\');';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['mailing_current'] . '` ADD `charset` VARCHAR(10) NOT NULL DEFAULT \'UTF-8\';';
				if (!performUpdate($sqlA, $dbo, 72, 'Enabling mailing character set selection'))
					$failed = TRUE;

				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR11.2");
			}
			
			$revision = 22;
				
			break;
			
		case 22:
		
			if ($dbRev < $revision) {
			
				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR11.2c");
			}
			
			$revision = 23;
				
			break;
			
		case 23: // AARDVARK PR12
		
			if ($dbRev < $revision) {
				
				$sqlA = array();
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['mailing_history'] . '` CHANGE `started` `started` DATETIME NOT NULL , CHANGE `finished` `finished` DATETIME NOT NULL ';
				$sqlA[] = 'ALTER TABLE `' . $dbo->table['mailing_current'] . '` CHANGE `started` `started` DATETIME NOT NULL , CHANGE `finished` `finished` DATETIME NOT NULL ';
				if (!performUpdate($sqlA, $dbo, 73, 'Updating mailing timestamps'))
					$failed = TRUE;
					
				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR12");
			}
			
			$revision = 24;
			
			break;
		
		case 24: // AARDVARK PR13
		
			if ($dbRev < $revision) {
				
				$sql = 'ALTER TABLE `' . $dbo->table['mailing_current'] . '` CHANGE `started` `started` DATETIME NULL, CHANGE `finished` `finished` DATETIME NULL';
				if (!performUpdate($sql, $dbo, 74, 'Allowing NULL times for mailings'))
					$failed = TRUE;
					
				$sql = 'ALTER TABLE `' . $dbo->table['mailing_current'] . '` CHANGE `charset` `charset` VARCHAR(15) NOT NULL DEFAULT \'UTF-8\'';
				if (!performUpdate($sql, $dbo, 75, 'Allowing longer encoding names'))
					$failed = TRUE;
					
				$sql = 'ALTER TABLE `' . $dbo->table['mailing_history'] . '` ADD `charset` VARCHAR(15) NOT NULL DEFAULT \'UTF-8\';';
				if (!performUpdate($sql, $dbo, 76, 'Saving encoding to mailing history'))
					$failed = TRUE;
					
				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR13");
			}
			
			
			$revision = 25;
			break;
			
		case 25: // AARDVARK PR13.1
		
			if ($dbRev < $revision) {
				
				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark PR13.1");
			}
			
			
			$revision = 26;
			break;
			
		case 26: // AARDVARK SVN
		
			if ($dbRev < $revision) {
				
				$sql = 'ALTER TABLE `' . $dbo->table['groups'] . '` DROP `group_cacheTally` , DROP `group_cacheTime`';
				if (!performUpdate($sql, $dbo, 77, 'Removing Group Tally Cache'))
					$failed = TRUE;
					
				// bump version
				if (!$failed)
					bmBumpVersion($dbo, $revision, "Aardvark SVN");
			}
			
			// follows last case
			if ($failed)
				return FALSE;
			return TRUE;
			
			$revision = 27;
			break;
		
		default :
			die('Unknown Revision passed to upgrade function - ' . $revision);
			break;
	}
	

	if ($failed)
		return FALSE;

	return bmUpgradeAardvark($revision, $dbo, $failed);
}
?>