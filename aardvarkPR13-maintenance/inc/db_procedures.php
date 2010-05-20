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

/** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

/**
 NOTE TO SELF: make sure slashes are inserted when adding information to a database, etc. 
	CHECK FOR MAGIC QUOTES BEING ON BEFORE USING ADDSLASHES TO INPUT THAT'S DATABASE BOUND
	
	Magic Quotes is on by default...
	
	USE STRIPSLASHES +  HTMLSPECIALCHARS FOR DISPLAYING
*/

// dbUpdateConfig: Updates a setting in the config table. If $config can be an array, or specific config_name. 
//    array key must be config name
//    if $force is set to TRUE, all configuration settings will be eligible to change.
function dbUpdateConfig(& $dbo, & $input, $force = FALSE) {

	// TODO.. if force option given, lookup keys of input, and perform a query where config_name IS IN(key list...)

	// convert $input to an array if it is not one already
	if (!is_array($input))
		$input = array ($input);

	// Get list of user changable configuration options
	if ($force)
		$sql = "SELECT config_name FROM {$dbo->table['config']}";
	else
		$sql = "SELECT config_name FROM {$dbo->table['config']} WHERE user_change='on'";

	while ($row = $dbo->getRows($sql, TRUE)) {
		if (isset($input[$row[0]])) {
			if ($row[0] == 'admin_password' && trim($input[$row[0]]) == '') // don't allow blank password.
				continue;
			// Update option with user supplied values
			$sqlB = "UPDATE {$dbo->table['config']} SET config_value='".str2db($input[$row[0]])."' WHERE config_name='".$row[0]."'";
			if (!$dbo->query($sqlB))
				die('Error updating configuration option: '.$row[0]);
		}
	}
	return;
}

function dbResetMessageDefaults($section = 'all') {
		global $dbo;
		global $poMMo;

		if ($section != 'all') {
			$dbvalues = $poMMo->getConfig(array('messages'));
			$messages = unserialize($dbvalues['messages']);
		}
		else {
			$messages = array();
		}
		
		if ($section == 'all' || $section == 'subscribe') {
		$messages['subscribe'] = array();
		$messages['subscribe']['msg'] = sprintf(_T('You have requested to subscribe to %s. We would like to validate your email address before adding you as a subscriber. Please visit the link below to be added to  %s.'), $poMMo->_config['list_name'])."\n\n\t[[url]]\n\n"._T('If you have received this message in error, please ignore it.');
		$messages['subscribe']['sub'] = _T('Subscription request'); 
		$messages['subscribe']['suc'] = _T('Welcome to our mailing list. Enjoy your stay.');
		}

		if ($section == 'all' || $section == 'unsubscribe') {
		$messages['unsubscribe'] = array();
		$messages['unsubscribe']['msg'] = sprintf(_T('You have requested to unsubscribe from %s.'),$poMMo->_config['list_name'])._T('Before processing this request, you must validate your email address by visiting the link below.')."\n\n\t[[url]]\n\n"._T('If you have received this message in error, please ignore it.');
		$messages['unsubscribe']['sub'] = _T('Unsubscription request'); 
		$messages['unsubscribe']['suc'] = _T('You have successfully unsubscribed. We\'ll miss you.');
		}

		if ($section == 'all' || $section == 'password') {
		$messages['password'] = array();
		$messages['password']['msg'] =  sprintf(_T('You have requested to change your password for %s.'),$poMMo->_config['list_name'])._T('Before processing this request, you must validate your email address by visiting the link below ->')."\n\n\t[[url]]\n\n"._T('If you have received this message in error, please ignore it.');
		$messages['password']['sub'] = _T('Change Password request'); 
		$messages['password']['suc'] = _T('Your password has been reset. Enjoy!');
		}

		if ($section == 'all' || $section == 'update') {
		$messages['update'] = array();
		$messages['update']['msg'] =  sprintf(_T('You have requested to change your password for %s.'),$poMMo->_config['list_name'])._T('Before processing this request, you must validate your email address by visiting the link below ->')."\n\n\t[[url]]\n\n"._T('If you have received this message in error, please ignore it.');
		$messages['update']['sub'] = _T('Update Records request'); 
		$messages['update']['suc'] = _T('Your records have been updated. Enjoy!');
		}

		$input = array('messages' => serialize($messages));
		dbUpdateConfig($dbo, $input, TRUE);

		return $messages;
}
?>