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

// TODO : Make array of queries.. send array to dbo->query(); update query function to allow arrays...
// TODO : Play with output buffering...
// TODO : load array of done serials @ beginning of loop.. not per each update!!!
// TODO : delete from data tables where demographic type is checkbox & value is off
//      * ensure program behaves similarly (ignoring 'off') -- ie. user_update2.php removes 'off's

// NOTE TO SELF -- all updates in a upgrade must be serialized, and their serial incremented!
defined('_IS_VALID') or die('Move along...');

function parse_mysql_dump($ignoreerrors = false) {
	
	global $dbo;
	global $logger;
	
			$file_content = file(bm_baseDir."/install/sql.schema.php");
			if (empty ($file_content))
				bmKill(_T('Error installing. Could not read sql.schema.php'));
			$query = '';
			foreach ($file_content as $sql_line) {
				$tsl = trim($sql_line);
				if (($sql_line != "") && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != "#")) {
					$query .= $sql_line;
					if (preg_match("/;\s*$/", $sql_line)) {
						$matches = array();
						preg_match('/:::(.+):::/',$query,$matches);
						if ($matches[1])
							$query = preg_replace('/:::(.+):::/',$dbo->table[$matches[1]], $query);
							$query = trim($query);
						if (!$dbo->query($query) && !$ignoreerrors) {
							$logger->addErr(_T('Database Error: ').$dbo->getError());
							return false;
						}
						$query = '';
					}
				}
			}
			return true;
		}
		

// <bool> Returns true if the program is installed, false if not. TODO: eventaully allow for table prefixing..
function bmIsInstalled() {
	global $dbo;
	
	if (is_object($dbo)) {
	$sql = 'SHOW TABLES LIKE \'' . $dbo->table['groups'] . '\'';
	if ($dbo->records($sql))
		return true;
	}
	
	return false;
}

// Returns the poMMo revision the user is upgrading from
function getOldVersion(& $dbo) {
	$oldRevision = NULL;

	$sql = "SELECT config_value FROM {$dbo->table['config']} WHERE config_name='revision'";
	$oldRevision = $dbo->query($sql, 0);
	if (is_numeric($oldRevision))
		return $oldRevision;

	// Revision was not found in database... check to see if we're dealing w/ an OLD version of poMMo
	$sql = "SELECT * FROM {$dbo->table['subscriber_data']} LIMIT 1";
	if ($dbo->records($sql)) {
		$sql = "SELECT * FROM {$dbo->table['config']} LIMIT 1";
		if (!$dbo->query($sql))
			$oldRevision = 5; // if there are demographics in subscriber_data, but the config table does not exist, we're using Aardvark PR6 or before.'
	}
	return $oldRevision;
}

// updates the version + revision in the DB
function bmBumpVersion(& $dbo, $revision, $versionStr) {
	global $logger;

	$logger->addMsg(_T('Bumping poMMo version to: ') . $versionStr);
	// TODO : Make array of queries.. send array to dbo->query(); update query function to allow arrays...
	$sql = 'UPDATE `' . $dbo->table['config'] . '` SET config_value=\'' . $revision . '\' WHERE config_name=\'revision\'';
	$dbo->query($sql);
	$sql = 'UPDATE `' . $dbo->table['config'] . '` SET config_value=\'' . $versionStr . '\' WHERE config_name=\'version\'';
	$dbo->query($sql);
}

// returns true if a update has already been performed before [protects against user refreshing upgrade page/allows incremental upgrades]
function checkUpdate($serial, & $dbo) {
	$sql = "SELECT update_serial FROM {$dbo->table['updates']} WHERE update_serial='" . $serial . "'";
	if ($dbo->records($sql))
		return true;
	return false;
}

// returns true if part of a upgrade was sucessfully performed
function performUpdate(& $sql, & $dbo, $serial, $message = NULL, $check = TRUE, $sqlBool = FALSE) {

	global $logger;

	// check to see if this was already performed. Bypassed if check is false
	if ($check)
		if (checkUpdate($serial, $dbo))
			return true;

	// if sqlBool is true (passed as argument), update has been done elsewhere. 
	// evaluate against $sql [should be passed as <bool> if the update was performed elsewhere]
	if ($sqlBool)
		$sqlBool = $sql;
	else { // perform the query

		// convert sql querty to an array if it isn't already
		if (!is_array($sql))
			$sql = array (
				$sql
			);

		$sqlBool = TRUE;
		foreach ($sql as $query)
			if (!$dbo->query($query))
				$sqlBool = FALSE;
	}

	// If an update has been performed, serialize it. Return status of update.
	if ($sqlBool) {
		$sql = "INSERT INTO {$dbo->table['updates']} (update_serial) VALUES('" . $serial . "')";
		if ($dbo->affected($sql) != 1) {
			$logger->addMsg(sprintf(_T('Failed to properly serialize update %s : %s'), $serial, $message));
			return false;
		}
		$logger->addMsg($serial . '. ' . $message . '... ' . _T('success!'));
		return TRUE;
	} else {
		$logger->addMsg($serial . '. ' . $message . '... <span style="font-weight: bold; background-color: red; color: white;">' . _T('FAILED!') . '</span>');
		return FALSE;
	}
}

// inserts the default configuration into the DB
function bmInstallConfig(& $dbo) {
	$x = TRUE;
	$queries = array ();
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (1, 'admin_username', 'admin', 'Username', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (2, 'admin_password', 'c40d70861d2b0e48a8ff2daa7ca39727', 'Password', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (3, 'site_name', 'My Website', 'Website Name', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (4, 'site_url', 'http://localhost/', 'Website URL', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (5, 'site_success', 'http://localhost/thanks.html', 'Signup Success URL', 'off', 'off')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (6, 'list_name', 'The poMMo Fanclub Mailing List', 'List Name', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (7, 'admin_email', 'admin@pommo.com', 'Administrator Email', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (8, 'list_fromname', 'poMMo Administrative Team', 'From Name', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (9, 'list_fromemail', 'pommo@yourdomain.com', 'From Email', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (10, 'list_frombounce', 'bounces@yourdomain.com', 'Bounces', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (11, 'list_exchanger', 'sendmail', 'List Exchanger', 'on', 'off')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (12, 'list_confirm', 'on', 'Confirmation Messages', 'on', 'off')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (13, 'demo_mode', 'on', 'Demonstration Mode', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (14, 'mailMax', '300', 'Mails per refresh', 'on', 'off')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (15, 'mailNum', '30', 'Mails per error dump', 'on', 'off')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (16, 'mailSize', '10', 'Mails per bMailer BATCH array', 'on', 'off')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (17, 'mailDelay', '1000', 'Microsends to delay between batches. A value of 2000000 would be 2 seconds.', 'on', 'on')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (18, 'version', 'Aardvark PR1', 'poMMo Version', 'on', 'off')";
	$queries[] = "INSERT INTO `{$dbo->table['config']}` VALUES (19, 'revision', '1', 'Internal Revision', 'on', 'off')";
	foreach ($queries as $sql) {
		if (!$dbo->query($sql))
			$x = FALSE;
	}
	return $x;
}

?>