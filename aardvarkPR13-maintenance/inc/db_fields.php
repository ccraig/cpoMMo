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

// NOTE - type checkbox should be NULL in SQL if not checked!

// dbDemoArray: <array> - Returns an array of subscriber fields. The array key
//  is the field_key, and it points to an array holding name, type, prompt, etc.
//  check if where is provided to limit this array to ACTIVE or a specific field
function & dbGetFields(& $dbo, $where = NULL) {
	
	require_once (bm_baseDir.'/inc/lib.txt.php');
	
	$demos = array ();
	$whereStr = '';

	if ($where == 'active')
		$whereStr = ' WHERE field_active=\'on\'';
	elseif (is_numeric($where)) $whereStr = ' WHERE field_id=\''.$where.'\'';

	$sql = 'SELECT * FROM '.$dbo->table['subscriber_fields'].$whereStr.' ORDER BY field_ordering';
	while ($row = $dbo->getRows($sql)) {
		$a = array ();
		$a['active'] = $row['field_active'];
		$a['ordering'] = $row['field_ordering'];
		$a['name'] = $row['field_name'];
		$a['prompt'] = $row['field_prompt'];
		$a['type'] = $row['field_type'];
		$a['normally'] = $row['field_normally'];
		$a['options'] = quotesplit($row['field_options']);
		$a['required'] = $row['field_required'];

		$demos[$row['field_id']] = $a;
	}
	return (!empty($demos)) ? $demos : array();
}

// returns a field ID based off name
function dbGetFieldId($name) {
	global $dbo;
	$sql = 'SELECT field_id FROM '.$dbo->table['subscriber_fields'].' WHERE field_name=\''.$name.'\'';
	return ($dbo->query($sql, 0));
}

// dbfieldCheck: <bool> - Returns true if a name/id field exists
function dbFieldCheck(& $dbo, $fieldId) {

	// determine if we're to check for name or id -- note field names CANNOT be numeric
	if (is_numeric($fieldId))
		$sql = 'SELECT count(field_id) FROM '.$dbo->table['subscriber_fields'].' WHERE field_id=\''.$fieldId.'\'';
	else
		$sql = 'SELECT count(field_id) FROM '.$dbo->table['subscriber_fields'].' WHERE field_name=\''.$fieldId.'\'';

	return ($dbo->query($sql, 0)) ? true : false;
}

// dbfieldAdd: <bool> - Returns true if a field of passed 'fieldname' was added
function dbFieldAdd(& $dbo, $fieldName, $fieldType) {

	// field NAMES CANNOT BE NUMERIC, or duplicate
	if (is_numeric($fieldName) || dbfieldCheck($dbo, $fieldName))
		return false;

	// get the last ordering
	$sql = 'SELECT field_ordering FROM '.$dbo->table['subscriber_fields'].' ORDER BY field_ordering DESC';
	$order = $dbo->query($sql, 0) + 1;

	$sql = 'INSERT INTO '.$dbo->table['subscriber_fields'].' SET field_name=\''.$fieldName.'\', field_type=\''.$fieldType.'\', field_ordering=\''.$order.'\',field_active=\'off\',field_required=\'off\'';
	return $dbo->affected($sql);
}

// dbFieldDelete: <bool> - Returns true if the passed fieldId was deleted, false if nothing was.
function dbFieldDelete(& $dbo, $fieldId) {

	// return false if a bad field was passed.
	if (!dbFieldCheck($dbo, $fieldId))
		return false;

	// delete entries in subscriber/pending_data, and groups_criteria referencing this field
	$sql = 'DELETE FROM '.$dbo->table['pending_data'].' WHERE field_id=\''.$fieldId.'\'';
	$dbo->query($sql);
	$sql = 'DELETE FROM '.$dbo->table['subscribers_data'].' WHERE field_id=\''.$fieldId.'\'';
	$dbo->query($sql);
	$sql = 'DELETE FROM '.$dbo->table['groups_criteria'].' WHERE field_id=\''.$fieldId.'\'';
	$dbo->query($sql);

	// delete field
	$sql = 'DELETE FROM '.$dbo->table['subscriber_fields'].' WHERE field_id=\''.$fieldId.'\'';
	return $dbo->affected($sql);
}

// dbFieldUpdate: <bool> - Returns true if a field's paramenters get updated
function dbFieldUpdate(& $dbo, & $input) {

	if (!dbFieldCheck($dbo, str2db($input['field_id'])))
		return false;

	$sql = 'UPDATE '.$dbo->table['subscriber_fields'].' SET field_name=\''.str2db($input['field_name']).'\', field_prompt=\''.str2db($input['field_prompt']).'\', field_required=\''.str2db($input['field_required']).'\', field_active=\''.str2db($input['field_active']).'\', field_normally=\''.str2db($input['field_normally']).'\' WHERE field_id=\''.str2db($input['field_id']).'\' LIMIT 1';
	return $dbo->affected($sql);
}

// dbFieldOptionAdd: <bool> - Returns true if option(s) are added to a multiple choice field
// automatically rids any duplicate choices
function dbFieldOptionAdd(& $dbo, & $field_id, & $option) {

	// ensure field_id exists
	if (!dbFieldCheck($dbo, $field_id) || empty ($option))
		return false;

	// set option(s) into array
	require_once (bm_baseDir.'/inc/lib.txt.php');
	$options = quotesplit($option);

	// get existing option(s)	
	$sql = 'SELECT field_options FROM '.$dbo->table['subscriber_fields'].' WHERE field_id=\''.$field_id.'\'';
	$oldoptions = quotesplit($dbo->query($sql, 0));

	// merge old options with new ones, getting rid of any duplicates
	if (!empty ($oldoptions))
		$options = array_unique(array_merge($oldoptions, $options));

	// add new options to field in DB, exploding array to csv with array2csv function.
	$sql = 'UPDATE '.$dbo->table['subscriber_fields'].' SET field_options=\''.addslashes(array2csv($options)).'\' WHERE field_id=\''.$field_id.'\' LIMIT 1';
	return $dbo->affected($sql);
}

// dbFieldOptionDelete: <int/false> - removes an option from a multiple choice field and
//  returns the # of subscribers affected (ones who chose this option). The option is removed from
//  subscriber_data, group_criteria, and pending_data. If a subscriber is affected, they get flagged to 'update their records'
function dbFieldOptionDelete(& $dbo, & $field_id, & $option) {

	// get existing option(s)
	require_once (bm_baseDir.'/inc/lib.txt.php');
	$sql = 'SELECT field_options FROM '.$dbo->table['subscriber_fields'].' WHERE field_id=\''.$field_id.'\'';
	$oldoptions = quotesplit($dbo->query($sql, 0));

	if (empty($oldoptions) || !is_array($oldoptions))
		bmKill('Bad oldoptions in dbFieldOptionDelete');
		
	// remove option from options array (if exists)
	$key = array_search(str2str($option), $oldoptions);
	if (is_numeric($key))
		unset ($oldoptions[$key]);

	// Remove option from field [ using adjusted oldoptions array ] 
	$sql = 'UPDATE '.$dbo->table['subscriber_fields'].' SET field_options=\''.addslashes(array2csv($oldoptions)).'\' WHERE field_id=\''.$field_id.'\' LIMIT 1';
	$dbo->query($sql);
	

	// search for subscribers who had this option selected. Delete it from their data + flag subscriber
	$subscribers = array('subscribers_id' => array(), 'data_id' => array());
	$sql = 'SELECT data_id, subscribers_id FROM '.$dbo->table['subscribers_data'].' WHERE field_id=\''.$field_id.'\' AND value=\''.str2db($option).'\'';
	while ($row = $dbo->getRows($sql,TRUE)) {
		$subscribers['subscribers_id'][] = $row[1];
		$subscribers['data_id'][] = $row[0];
	}
	$affected = @count($subscribers['data_id']);
	
	if ($affected) {
	$sql = 'DELETE FROM '.$dbo->table['subscribers_data'].' WHERE data_id IN(\''.implode('\',\'', $subscribers['data_id']).'\')';
	$dbo->query($sql);
	
	require_once (bm_baseDir.'/inc/db_subscribers.php');
	dbFlagSubscribers($subscribers['subscribers_id']);
	}
	
	// remove from groups_criteria...
	// get existing criteria matches
	$sql = 'SELECT criteria_id, value FROM '.$dbo->table['groups_criteria'].' WHERE field_id=\''.$field_id.'\'';
	while ($row = $dbo->getRows($sql, TRUE)) {
		$oldoptions = quotesplit($row[1]);
		// see if option matches one of this group's filtering criteria
		$key = array_search(str2str($option), $oldoptions);
		if ($key) { // MATCHES 
			// remove option from matches array
			unset ($oldoptions[$key]);
			// if there are still matches left, update criteria. If not, delete the row.
			if (empty ($oldoptions))
				$sql = 'DELETE FROM '.$dbo->table['groups_criteria'].' WHERE criteria_id=\''.$row[0].'\' LIMIT 1';
			else
				$sql = 'UPDATE '.$dbo->table['groups_criteria'].' SET value=\''.addslashes(array2csv($oldoptions)).'\' WHERE criteria_id=\''.$row[0].'\' LIMIT 1';
			$dbo->query($sql);
		}
	}
	// remove from pending_data
	$sql = 'DELETE FROM '.$dbo->table['pending_data'].' WHERE field_id=\''.$field_id.'\' AND value=\''.str2db($option).'\'';
	$dbo->query($sql);

	return $affected;
}
?>