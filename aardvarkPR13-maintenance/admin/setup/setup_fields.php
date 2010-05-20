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

/**********************************
	INITIALIZATION METHODS
*********************************/
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/db_fields.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();

$smarty->assign('intro', _T('Subscriber fields are used to gather and sort information on your list members. Any number of fields can be assigned to the subscription form.  Each field is categorized as either <em>TEXT</em>, <em>NUMBER</em>, <em>MULTIPLE CHOICE</em>, <em>CHECK BOX</em>, or <em>DATE</em> depending on kind of information it collects.'));

// add field if requested, redirect to its edit page on success
if (!empty ($_POST['field_name'])) {
	if (dbFieldAdd($dbo, str2db($_POST['field_name']), str2db($_POST['field_type'])))
		bmRedirect('fields_edit.php?field_id=' .
		$dbo->lastId());
	else
		$logger->addMsg(_T('Unable to add field'));
}

// check for a deletion request
if (!empty ($_GET['delete'])) {

	// make sure it is a valid field
	if (!dbFieldCheck($dbo, $_GET['field_id'])) {
		$logger->addMsg(_T('Field cannot be deleted.'));
	} else {
		// See if this change will affect any subscribers, if so, confirm the change.
		$sql = 'SELECT COUNT(data_id) FROM ' . $dbo->table['subscribers_data'] . ' WHERE field_id=\'' . $_GET['field_id'] . '\'';
		$affected = $dbo->query($sql, 0);

		if ($affected && empty ($_GET['dVal-force'])) {
			$smarty->assign('confirm', array (
				'title' => _T('Delete Field'
			), 'nourl' => $_SERVER['PHP_SELF'] . '?field_id=' . $_GET['field_id'],
			 'yesurl' => $_SERVER['PHP_SELF'] . '?field_id=' . $_GET['field_id'] . '&delete=TRUE&dVal-force=TRUE',
			  'msg' => sprintf(_T('Currently, %1$s subscribers have a non empty value for this field. All Subscriber data relating to this field will be lost. Are you sure you want to remove field %2$s?'), '<b>' . $affected . '</b>','<b>' . $_GET['field_name'] . '</b>')));
			$smarty->display('admin/confirm.tpl');
			bmKill();
		} else {
			// delete field
			if (dbFieldDelete($dbo, $_REQUEST['field_id']))
				bmRedirect($_SERVER['PHP_SELF']);
			$logger->addMsg(_T('Field cannot be deleted.'));
		}
	}
}

// Get array of fields. Key is ID, value is an array of the demo's info
$fields = dbGetFields($dbo);
if (!empty($fields))
	$smarty->assign('fields', $fields);
	
$smarty->display('admin/setup/setup_fields.tpl');
bmKill();
?>