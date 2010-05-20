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

// returns true if valid.. false if not. Adds errors/messages to logger.
function validateSubscribeForm($dupeCheck = TRUE) {
	global $logger;
	global $dbo;
	require_once (bm_baseDir . '/inc/lib.txt.php');

	// ** check for correct email syntax
	if (!isEmail($_POST['bm_email']))
		$logger->addErr(_T('Invalid Email Address'));
		
	// ** check if confirmation email matches. (if exists)
	if (isset($_POST['updateForm']) && $_POST['email2'] != $_POST['bm_email'])
		$logger->addErr(_T('Emails must match.'));

	// ** check if email already exists in DB ("duplicates are bad..")
	if ($dupeCheck) {
		if (isDupeEmail($dbo, $_POST['bm_email'])) {
			$logger->addErr('Email address already exists. Duplicates are not allowed');
			global $smarty;
			if (is_object($smarty))
				$smarty->assign('dupe', TRUE);
		}
	}

	// ** validate user submitted fields
	$fields = & dbGetFields($dbo, 'active');
	$subscriber_data = array ();
	
	if (!empty($fields)) {
	foreach (array_keys($fields) as $field_id) {
		$field = & $fields[$field_id];

		// check to make sure a required field is not empty
		if (empty ($_POST['d'][$field_id]) && $field['required'] == 'on') {
			$logger->addErr($field['prompt'] . ' ' . _T('was a required field.'));
			continue;
		}

		// create field array
		if (!empty ($_POST['d'][$field_id])) {
			// TODO : insert validation schemes here (ie. check options, #, date)
			switch ($field['type']) {
				case 'checkbox' :
					if ($_POST['d'][$field_id] == 'on') // don't add to subscriber_data if value is not checked..
						$subscriber_data[$field_id] = str2db($_POST['d'][$field_id]);
					break;
				default :
					$subscriber_data[$field_id] = str2db($_POST['d'][$field_id]);
					break;
			}

		}
	}
	}
	if ($logger->isErr())
		return false;
	return true;
}
?>