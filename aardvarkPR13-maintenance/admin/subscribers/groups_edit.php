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
require_once (bm_baseDir . '/inc/db_groups.php');
require_once (bm_baseDir . '/inc/db_fields.php');
require_once (bm_baseDir . '/inc/lib.txt.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();
$smarty->assign('returnStr', _T('Groups Page'));

// validate group_id before setting it as var
if (isset ($_REQUEST['group_id']) && dbGroupCheck($dbo, $_REQUEST['group_id']))
	$group_id = str2db($_REQUEST['group_id']);
else {
	bmRedirect('subscribers_groups.php');
}

// delete criteria if requested
if (!empty ($_GET['delete'])) {
	if (is_numeric($_GET['filter_id']))
		if (dbGroupFilterDel($dbo, str2db($_GET['filter_id'])))
			$logger->addMsg(_T('Filter Removed'));
}

// change group name  if requested
if (isset ($_POST['rename']) && !empty ($_POST['group_name']))
	dbGroupUpdateName($dbo, $group_id, str2db($_POST['group_name']));

// get groups, fields
$groups = & dbGetGroups($dbo);
$demos = & dbGetFields($dbo);

// check if a filter is requested to be added
if (isset ($_POST['add']) || isset ($_POST['update'])) {

	function validateFilter() {
		global $demos;
		global $groups;

		if (isset ($_POST['logic'])) {

			// logic-val: what a field should be compared to
			// field_id: which field_id (field_id) should be compared
			// logic: the logic of the comparisson

			// make sure field_id is valid
			$demo = & $demos[$_POST['field_id']];
			if (!is_array($demo))
				return false;

			switch ($_POST['logic']) {
				case 'is_in' :
				case 'not_in' :
					return false; // group inclusion/exclusion should be hanled by section below...

				case 'is_equal' :
				case 'not_equal' :
					if (empty($_POST['logic-val']))
						return false;
			
					if ($demo['type'] == 'checkbox')
						return false;
					break;

				case 'is_more' :
				case 'is_less' :
					if (empty($_POST['logic-val']))
						return false;
						
					if ($demo['type'] == 'checkbox' || $demo['type'] == 'multiple')
						return false;
					break;
				case 'is_true' :
				case 'not_true' :
					if ($demo['type'] != 'checkbox')
						return false;
					break;
				default :
					return false;
					break;
			}

		}
		elseif (!empty($_POST['group_logic'])) {
			switch ($_POST['group_logic']) {
				case 'is_in' :
				case 'not_in' :
					// make sure logic-val is a valid group

					if (!isset ($groups[$_POST['logic-val']]))
						return false;
					break;
			}
		} else {
			return false;
		}
		// addition passed sanity checks
		return true;
	}

	// validate addition
	if (validateFilter()) {
		@$logic = (!empty ($_POST['group_logic'])) ? $_POST['group_logic'] : $_POST['logic'];
		@$value = (!empty ($_POST['group_logic'])) ? $_POST['logic-val'] : $_POST['logic-val'];
		@$demo_id = (!empty ($_POST['group_logic'])) ? $_POST['logic-val'] : $_POST['field_id'];

		// check if we should update filter
		if (isset ($_POST['update']) && isset ($_POST['filter_id']) && is_numeric($_POST['filter_id'])) {
			if (dbGroupFilterDel($dbo, $_POST['filter_id']))
				if (dbGroupFilterAdd($dbo, $group_id, $demo_id, $logic, $value))
					$logger->addMsg(_T('Filter Updated'));
				else
					$logger->addMsg(_T('Update failed'));
		} else {
			if (dbGroupFilterAdd($dbo, $group_id, $demo_id, $logic, $value))
				$logger->addMsg(_T('Filter Added'));
			else
				$logger->addMsg(_T('Could not add filter. Perhaps it negates the effect of an existing one?'));
		}
	} else {
		$logger->addMsg(_T('Filter failed validation'));
	}
}

$tally = dbGroupTally($dbo, $group_id);
$filters = dbGetGroupFilter($dbo, $group_id);
$filterCount = count($filters);
$group_name = db2str($groups[$group_id]);

$smarty->assign('group_name', $group_name);
$smarty->assign('demos', $demos);
$smarty->assign('groups', $groups);
$smarty->assign('group_id', $group_id);
$smarty->assign('filters', $filters);
$smarty->assign('filterCount', $filterCount);
$smarty->assign('tally', $tally);

$smarty->display('admin/subscribers/groups_edit.tpl');
bmKill();
?>