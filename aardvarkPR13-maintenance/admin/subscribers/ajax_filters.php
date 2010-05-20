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

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

$output = FALSE;
$defaultLogic = null;
$defaultValue = null;

// check if this is an update -- assign appropriate defaults
// NOTE -- the filters values could be passed from parent script also... cleanup! (1 less DB query..)
if (isset($_POST['update'])) {
	require_once (bm_baseDir . '/inc/db_groups.php');
	require_once (bm_baseDir . '/inc/lib.txt.php');
	$filters = dbGetGroupFilter($dbo, $group_id = $_POST['group_id'], $_POST['filter_id']);
	$filter =& $filters[$_POST['filter_id']];
	if (is_array($filter)) {
		switch ($filter['logic']) {
			case 'is_in' :
			case 'not_in' :
				$_POST['group_logic'] = $filter['logic'];
				$defaultValue = quotesplit($filter['field_id']);
				
				// a kludge to trick the add(update) routine in groups_edit.php
				echo '<input type="hidden" name="group_logic" value="'.$filter['logic'].'">';
				break;
			case 'is_equal' :
			case 'not_equal' :
			case 'is_more' :
			case 'is_less' :
				$_POST['field_id'] = $filter['field_id'];
				$defaultValue = quotesplit($filter['value']);
				$defaultLogic = $filter['logic'];
				
				// a kludge to trick the add(update) routine in groups_edit.php
				echo '<input type="hidden" name="field_id" value="'.$filter['field_id'].'">';
				break;
			case 'is_true' :
			case 'not_true' :
				$_POST['field_id'] = $filter['field_id'];
				$defaultLogic = $filter['logic'];
				$defaultValue = TRUE;
				
				// a kludge to trick the add(update) routine in groups_edit.php
				echo '<input type="hidden" name="field_id" value="'.$filter['field_id'].'">';
				break;
				
		}
	}
}

function checkLogic($check) {
	global $defaultLogic;
	return ($defaultLogic == $check) ? ' CHECKED' : null;
}

function checkValue($check) {
	global $defaultValue;
	if ($defaultValue)
		return (in_array($check, $defaultValue)) ? ' SELECTED' : null;
}


if (!empty ($_POST['field_id'])) {
	// determine the type of the field
	require_once (bm_baseDir . '/inc/db_fields.php');

	$demos = dbGetFields($dbo, str2db($_POST['field_id']));
	$demo = & $demos[$_POST['field_id']];

	switch ($demo['type']) {
		case 'checkbox' :

			$output = '
									' . sprintf(_T('Match subscribers where %s'), '<strong>' . $demo['name'] . '</strong>') . '
									<br>
									<input type="radio" name="logic" value="is_true"' . checkLogic('is_true') . '>' . _T('Is Checked') . '
									<br>
									<input type="radio" name="logic" value="not_true"' . checkLogic('not_true') . '>' . _T('Is Not Checked') . '
									';
			break;
		case 'multiple' :

			$options = '';
			foreach ($demo['options'] as $val) {
				$options .= '<option' . checkValue($val) . '>' . $val . '</option>';
			}

			$output = '
									' . sprintf(_T('Match subscribers where %s'), '<strong>' . $demo['name'] . '</strong>') . '
									<br>
									<input type="radio" name="logic" value="is_equal"' . checkLogic('is_equal') . '>' . _T('Is') . '
									<br>
									<input type="radio" name="logic" value="not_equal"' . checkLogic('not_equal') . '>' . _T('Is Not') . '
									<div style="float: right">'._T('NOTE: You can select multiple values by holding the SHIFT or CONTROL keys').'</div>
									<br><br>
										
									<select multiple name="logic-val[]" size="10">
									' . $options . '
									</select>
									
									';
			break;

		case 'text' :

			$output = '
									' . sprintf(_T('Match subscribers where %s'), '<strong>' . $demo['name'] . '</strong>') . '
									<br>
									<input type="radio" name="logic" value="is_equal"' . checkLogic('is_equal') . '>' . _T('Is') . '
									<br>
									<input type="radio" name="logic" value="not_equal"' . checkLogic('not_equal') . '>' . _T('Is Not') . '
									<br><br>
									<input type="text" class="text" name="logic-val" size="30" maxlength="65" value="' . $defaultValue[0] . '">
									';

			break;

		case 'date' :

			$output = '
									' . sprintf(_T('Match subscribers where %s'), '<strong>' . $demo['name'] . '</strong>') . '
									<br>
									<table cellspacing="2" border="0">
										<tr>
											<td><input type="radio" name="logic" value="is_equal"' . checkLogic('is_equal') . '>' . _T('Is') . '</td>
											<td width="35"></td>
											<td><input type="radio" name="logic" value="is_less"' . checkLogic('is_less') . '>' . _T('Is Before') . '</td>
										</tr>
										<tr>
											<td><input type="radio" name="logic" value="not_equal"' . checkLogic('not_equal') . '>' . _T('Is Not') . '</td>
											<td width="35"></td>
											<td><input type="radio" name="logic" value="is_more"' . checkLogic('is_more') . '>' . _T('Is After') . '</td>
										</tr>
									</table>
						
									<input type="text" class="text" name="logic-val" size="30" maxlength="65" value="' . $defaultValue[0] . '">
									';

			break;

		case 'number' :

			$output = '
									' . sprintf(_T('Match subscribers where %s'), '<strong>' . $demo['name'] . '</strong>') . '
									<br>
									<table cellspacing="2" border="0">
										<tr>
											<td><input type="radio" name="logic" value="is_equal"' . checkLogic('is_equal') . '>' . _T('Is') . '</td>
											<td width="35"></td>
											<td><input type="radio" name="logic" value="is_less"' . checkLogic('is_less') . '>' . _T('Is Less Than') . '</td>
										</tr>
										<tr>
											<td><input type="radio" name="logic" value="not_equal"' . checkLogic('not_equal') . '>' . _T('Is Not') . '</td>
											<td width="35"></td>
											<td><input type="radio" name="logic" value="is_more"' . checkLogic('is_more') . '>' . _T('Is Greater Than') . '</td>
										</tr>
									</table>
						
									<input type="text" class="text" name="logic-val" size="30" maxlength="65" value="' . $defaultValue[0] . '">
									';
			break;
	}

}
elseif (!empty ($_POST['group_logic'])) {
	require_once (bm_baseDir . '/inc/db_groups.php');
	$groups = & dbGetGroups($dbo);

	if (isset ($_POST['group_id']) && is_numeric($_POST['group_id'])) {
		$options = '';
		foreach ($groups as $group_id => $group_name) {
			if ($group_id != $_POST['group_id'])
				$options .= '<option value="' . $group_id . '"' . checkValue($group_id) . '>' . $group_name . '</option>';
		}

		switch ($_POST['group_logic']) {
			case 'is_in' :
				$output = '
										' . _T('Include subscribers belonging to') . '
										<br><br>
										<select name="logic-val">
										' . $options . '
										</select>
										';

				break;

			case 'not_in' :
				$output = '
										' . _T('Exclude subscribers belonging to') . '
										<br><br>
										<select name="logic-val">
										' . $options . '
										</select>
										';
				break;
		}
	}

}

if ($output) {

	$buttonStr = (empty ($defaultValue)) ? 
	'<input type="submit" name="add" value="' . _T('Add Filter') . '">' : 
	'<input type="submit" name="update" value="' . _T('Update Filter') . '">
	<input type="hidden" name="filter_id" value="'.$_POST['filter_id'].'">';

	$output .= '
				<br><br>' . $buttonStr . '
				<span style="margin-left: 50px;">
					<div class="goback">
						<a href="javascript:reset();">
							<img src="' . bm_baseUrl . 'themes/shared/images/icons/left.png" align="absmiddle" border="0">' . _T('Go Back') . '
						</a>
					</div>
				</span>';
} else {
	$output = '
				<br>
				<div  class="goback">
					<a href="javascript:reset();">
						<img src="' . bm_baseUrl . 'themes/shared/images/icons/left.png" align="absmiddle" border="0">' . _T('Go Back') . '
					</a>
				</div>';
}

echo $output;
?>