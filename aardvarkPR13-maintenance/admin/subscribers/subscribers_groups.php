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
require_once (bm_baseDir.'/inc/db_groups.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();


// add group if requested
if (!empty ($_POST['group_name'])) {
	if (dbGroupAdd($dbo, str2db($_POST['group_name'])))
		$logger->addMsg(sprintf(_T('Group %s Added'),$_POST['group_name']));
}

if (!empty ($_GET['delete'])) {
	// make sure it is a valid field
	if (!dbGroupCheck($dbo, $_GET['group_id'])) {
		$logger->addMsg(_T('Group cannot be deleted.'));
	} else {
		// See if this change will affect any subscribers, if so, confirm the change.
		$sql = 'SELECT COUNT(criteria_id) FROM ' . $dbo->table['groups_criteria'] . ' WHERE group_id=\'' . $_GET['group_id'] . '\'';
		$affected = $dbo->query($sql, 0);

		if ($affected > 1 && empty ($_GET['dVal-force'])) {
			$smarty->assign('confirm', array (
				'title' => _T('Delete Group'
			), 'nourl' => $_SERVER['PHP_SELF'] . '?group_id=' . $_GET['group_id'],
			 'yesurl' => $_SERVER['PHP_SELF'] . '?group_id=' . $_GET['group_id'] . '&delete=TRUE&dVal-force=TRUE&group_name='.$_GET['group_name'],
			  'msg' => sprintf(_T('%1$s filters belong this group . Are you sure you want to remove %2$s?'), '<b>' . $affected . '</b>','<b>' . $_GET['group_name'] . '</b>')));
			$smarty->display('admin/confirm.tpl');
			bmKill();
		} else {
			// delete field
			if (dbGroupDelete($dbo, $_GET['group_id'])) {
				$logger->addMsg(sprintf(_T('%s deleted.'),$_GET['group_name']));
				bmRedirect($_SERVER['PHP_SELF']);
			}
			$logger->addMsg(_T('Group cannot be deleted.'));
		}
	}
}

// Get array of mailing groups. Key is ID, value is name
$groups = dbGetGroups($dbo);

$smarty->assign('groups',$groups);
$smarty->display('admin/subscribers/subscribers_groups.tpl');
bmKill();
?>