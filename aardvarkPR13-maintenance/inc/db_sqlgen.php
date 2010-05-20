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
 * dbGetGroupSubscribers -> 
 * 	Reads in a group_id, dependant on $return type, returns :
 *   (list) an array of subscriber_id's in group_id
 *   (count) the # of subscibedrs in a group_id 
 *   (email) an array of emails in a group (for fast mail queue population)
 *
 *  if order_id is given, results will be ordered by the that particular field_id.	
 *    ordering can be changed from ASC to DESC if $order_type is provided (as 'DESC')
 *
 *  if limit is given, the array returned array will have a max # of entries. If
 *   start is given, the resultset will begin at the start entry
 */
 
 // TODO -> known bug: if you include/exclude a group w/ no criteria.. warnings are thrown.
 
function & dbGetGroupSubscribers(& $dbo, $table, $group_id, $returnType = 'list', $order_by = NULL, $order_type = 'ASC', $limit = NULL, $start = NULL) {
	if ($table != 'subscribers' && $table != 'pending')
		die('<img src="' .
		bm_baseUrl . 'themes/shared/images/icons/alert.png" align="middle">Unknown table passed to dbGetGroupSubscribers().');

	// set variables to be appended onto SQL statements
	$sortTbl = '';
	$orderSQL = '';
	$limitStr = '';
	$whereSQL = ' WHERE 1';

	if ($order_by) { // returned subscribers should be ordered 
		if ($group_id == 'all') {
			if ($order_by == 'email') {
				$sortTbl = ' INNER JOIN ' . $dbo->table[$table] . ' sort ON (s.' . $table . '_id=sort.' . $table . '_id)';
				$orderSQL = ' ORDER BY sort.email,s.' . $table . '_id ' . $order_type;
			} else {
				$sortTbl = ' LEFT JOIN ' . $dbo->table[$table] . '_data sort ON (s.' . $table . '_id=sort.' . $table . '_id AND sort.field_id=' . $order_by . ')';
				$orderSQL = ' ORDER BY sort.value,s.' . $table . '_id ' . $order_type;
			}
		} else {
			if ($order_by == 'email') {
				$sortTbl = ' INNER JOIN ' . $dbo->table[$table] . ' sort ON (t1.' . $table . '_id=sort.' . $table . '_id)';
				$orderSQL = ' ORDER BY sort.email,t1.' . $table . '_id ' . $order_type;
			} else {
				$sortTbl = ' LEFT JOIN ' . $dbo->table[$table] . '_data sort ON (t1.' . $table . '_id=sort.' . $table . '_id AND sort.field_id=' . $order_by . ')';
				$orderSQL = ' ORDER BY sort.value,t1.' . $table . '_id ' . $order_type;
			}
		}
	}

	if (is_numeric($group_id)) {
		// generate include/exclude arrays for the group
		$criteriaTbl = '';
		$subtractTbl = '';
		$sqlArray = genSql($dbo, $group_id);
		
		if (!$sqlArray) {
			// no group criteria exist?
			$group_id = 'all';
			$sortTbl = ' INNER JOIN ' . $dbo->table[$table] . ' sort ON (s.' . $table . '_id=sort.' . $table . '_id)';
			$orderSQL = ' ORDER BY sort.email,s.' . $table . '_id ' . $order_type;
		}
		elseif ($sqlArray['include'][1]) {
			for ($i = 2; $i <= $sqlArray['include'][0]; $i++)
				$criteriaTbl .= ' inner join ' . $dbo->table[$table . '_data'] . ' t' . $i . ' using (' . $table . '_id)';
			$whereSQL = ' WHERE ' . $sqlArray['include'][1];
		}

		// WHY NO MINUS/SUBTRACT/SUBQUERY?? B/C MySQL FTL!! ESPECIALLY WHEN KEEPING 3.23 COMPLIANCE :(
		//   Future improvements -> Seperate Criteria into groups_addCritera, groups_subCriteria. Cache their queries in DB 
		//   so SQL generator is not required if criteria haven't changed... look into cacheing of actual IDs, etc.
		//   TODO : with this cache, use serialize to store the include/exclude array.

		// If the group calls for anything to be excluded, insert IDs into subtraction table.
		if (!empty ($sqlArray['exclude'][0]) && $sqlArray['exclude'][0] > 0) {
			$dbo->dieOnQuery(FALSE);
			$sql = 'DROP TABLE IF EXISTS subtract';
			$dbo->query($sql) or die('<img src="' . bm_baseUrl . 'themes/shared/images/icons/alert.png" align="middle">Please ensure your MySQL user has privileges to DROP TEMPORARY TABLE.');
			$sql = 'CREATE TEMPORARY TABLE subtract (' . $table . '_id INT UNSIGNED NOT NULL, INDEX (' . $table . '_id))';
			$dbo->query($sql) or die('<img src="' . bm_baseUrl . 'themes/shared/images/icons/alert.png" align="middle">Please ensure your MySQL user has privileges to CREATE TEMPORARY TABLE.');
			$sql = 'INSERT INTO subtract (' . $table . '_id) SELECT DISTINCT t1.' . $table . '_id FROM ' . $dbo->table[$table . '_data'] . ' t1';
			for ($i = 2; $i <= $sqlArray['exclude'][0]; $i++)
				$sql .= ' inner join ' . $dbo->table[$table . '_data'] . ' t' . $i . ' using (' . $table . '_id)';
			$sql .= ' WHERE ' . $sqlArray['exclude'][1];
			$dbo->query($sql) or die('<img src="' . bm_baseUrl . 'themes/shared/images/icons/alert.png" align="middle">Error inserting records into temp table. <br><br>Query was: ' . $sql);

			$subtractTbl = ' LEFT JOIN subtract ON t1.' . $table . '_id=subtract.' . $table . '_id';
			$whereSQL .= ' AND subtract.' . $table . '_id IS NULL';
			$dbo->dieOnQuery(TRUE);
		}
	}

	if ($table == 'pending') // if viewing the pending table, only show subscribers to be added. Not those requesting changes...
		$whereSQL .= ' AND s.type=\'add\'';

	switch ($returnType) {
		case 'count' :
			if ($group_id == 'all')
				$sql = 'SELECT COUNT(s.' . $table . '_id) FROM ' . $dbo->table[$table] . ' s' . $whereSQL;
			else
				$sql = 'SELECT COUNT(DISTINCT t1.' . $table . '_id) FROM ' . $dbo->table[$table . '_data'] . ' t1' . $criteriaTbl . $subtractTbl . $whereSQL;
			$result = & $dbo->query($sql, 0);
			break;
		case 'list' : // only type which will apply a limit
			if (is_numeric($limit))
				if ($start)
					$limitStr = ' LIMIT ' . $start . ',' . $limit;
				else
					$limitStr = ' LIMIT ' . $limit;
			if ($group_id == 'all')
				$sql = 'SELECT DISTINCT s.' . $table . '_id FROM ' . $dbo->table[$table] . ' s' . $sortTbl . $whereSQL . $orderSQL . $limitStr;
			else
				$sql = 'SELECT DISTINCT t1.' . $table . '_id FROM ' . $dbo->table[$table . '_data'] . ' t1' . $criteriaTbl . $subtractTbl . $sortTbl . $whereSQL . $orderSQL . $limitStr;
			$result = & $dbo->getAll($sql, 'row', '0');
			break;
		case 'email' : // grabs all emails
			if ($group_id == 'all')
				$sql = 'SELECT s.email FROM ' . $dbo->table[$table] . ' s' . $sortTbl . $whereSQL . $orderSQL;
			else
				$sql = 'SELECT DISTINCT s.email FROM ' . $dbo->table[$table . '_data'] . ' t1' . $criteriaTbl . $subtractTbl . $sortTbl . ' INNER JOIN ' . $dbo->table[$table] . ' s ON (s.' . $table . '_id=t1.' . $table . '_id)' . $whereSQL . $orderSQL;
			$result = & $dbo->getAll($sql, 'row', '0');
			break;
		default :
			die('<img src="' . bm_baseUrl . 'themes/shared/images/icons/alert.png" align="middle">Unknown type sent to dbGetGroupSubscribers()');
	}
	return $result;
}

// Todo --> integrate this w/ dbCrawl ??	
// opposite logic must be applied. Rules:
//   1) Subscribers matching anything in the 'exclude' array will be SUBTRACTED from subscribers matched in the INCLUDE array
//   2) not_equal + not_true logic will be converted to opposite logic and placed in the opposite array.
//      ie. if a field has 'not_equal' logic & is in the exclude array, it will be transformed to 'is_in' and moved to the INCLUDE array
function makedemo(& $tree, & $criteriaArray, & $fields, $include = 'include') {
	foreach (array_keys($tree) as $key) {

		$criteria = & $criteriaArray[$key];

		if (is_array($tree[$key])) {
			if ($criteria['logic'] == 'not_in')
				makedemo($tree[$key], $criteriaArray, $fields, 'exclude');
			else
				makedemo($tree[$key], $criteriaArray, $fields);
		} else {
			if (!isset ($fields[$criteria['field_id']]))
				$fields[$criteria['field_id']] = array (
					'include' => array (),
					'exclude' => array ()
				);

			$field = & $fields[$criteria['field_id']];

			// convert not_equal and not_true to opposite logic
			if ($criteria['logic'] == 'not_equal')
				$oppLogic = 'is_equal';
			elseif ($criteria['logic'] == 'not_true') $oppLogic = 'is_true';

			if (isset ($oppLogic)) {
				if ($include == 'include')
					if (!isset ($field['exclude']['is_equal']))
						$field['exclude'][$oppLogic] = quotesplit($criteria['value']);
					else
						$field['exclude'][$oppLogic] = array_unique(array_merge($field['exclude'][$oppLogic], quotesplit($criteria['value'])));
				else
					if (!isset ($field['include'][$oppLogic]))
						$field['include'][$oppLogic] = quotesplit($criteria['value']);
					else
						$field['include'][$oppLogic] = array_unique(array_merge($field['include'][$oppLogic], quotesplit($criteria['value'])));
				unset ($oppLogic);
			}
			elseif (!isset ($field[$include][$criteria['logic']])) $field[$include][$criteria['logic']] = quotesplit($criteria['value']);
			else
				$field[$include][$criteria['logic']] = array_unique(array_merge($field[$include][$criteria['logic']], quotesplit($criteria['value'])));

		}
	}
	return $fields;
}

function whereGen(& $field, & $logicTbl, & $i) {
	if (empty ($field))
		return;

	$whereSQL = ' AND (';
	foreach (array_keys($field) as $logic) {
		$values = & $field[$logic];
		switch ($logic) {
			case 'is_equal' :
				$count = count($values);
				if ($count > 1) {
					$c = 0;
					$whereSQL .= ' t' . $i . '.value IN (';
					foreach ($values as $value) {
						$c++;
						$whereSQL .= '\'' . db2db($value) . '\'';
						if ($c < $count)
							$whereSQL .= ',';
					}
					$whereSQL .= ' )';
				} else
					$whereSQL .= ' t' . $i . '.value' . $logicTbl[$logic] . '\'' . current($values) . '\'';
				$whereSQL .= ' )';
				break;
			case 'is_less' :
			case 'is_more' : // cannot have multiple is more / is less.. 1st value is used if multiple..
				$whereSQL .= ' t' . $i . '.value' . $logicTbl[$logic] . '\'' . current($values) . '\')';
				break;
		}
	}
	return $whereSQL;
}

// crawls through a group's filtering criteria, returning an array tree of criteria_id's
//  the tree is a tree of criteria_id. 
function & dbCrawl(& $dbo, $group_id, & $criteriaArray, & $groupArray, & $groupsVisited) {

	// leave a breadcrumb...
	$groupsVisited[$group_id] = TRUE;
	$tree = array ();

	// Examine each criteria belonging to this group
	$group = & $groupArray[$group_id];
	foreach (array_keys($group) as $key) {
		$criteria_id = & $group[$key];
		$criteria = & $criteriaArray[$criteria_id];

		// If criteria references another group..
		if (($criteria['logic'] == 'is_in') || ($criteria['logic'] == 'not_in')) {
			// check to make sure we haven't already been there [loop prevention!] 
			if (!isset ($groupsVisited[$criteria['value']]))
				$tree[$criteria_id] = dbCrawl($dbo, $criteria['value'], $criteriaArray, $groupArray, $groupsVisited);
		} else // if not add the criteria_id to the tree and continue.
			$tree[$criteria_id] = $criteria_id;
	}
	return $tree;
}

// returns an array of include & exclude SQL pertaining to a group.
function & genSql(& $dbo, & $group_id) {

	require_once (bm_baseDir . '/inc/lib.txt.php'); // used to convert value line (csv format)
	require_once (bm_baseDir . '/inc/db_groups.php');

	// TODO, remove this dependance on dbGroups....
	// get array of all criteria (saves from many MySQL queries). criteria_id is array key.
	//require_once (bm_baseDir.'/inc/db_groups.php');
	$criteriaArray = dbGetGroupFilter($dbo);

	// make $groupArray where group_id is array key, and element is an array of that group's criteria_ids
	// ie. $groupArray[5 => array (6,12,15)]  means group 5  has filtering criteria w/ id 6,12, and 15 assosiated w/ it.
	$groupArray = array ();
	foreach (array_keys($criteriaArray) as $key) {
		$criteria = & $criteriaArray[$key];
		if (empty ($groupArray[$criteria['group_id']]))
			$groupArray[$criteria['group_id']] = array ();
		array_push($groupArray[$criteria['group_id']], $key);
	}

	// determine if any criteria is assosiated with this group.. if not, return
	if (empty ($groupArray[$group_id]))
		return 0;

	// Recursively generate WHERE logic, returns SQL to match subscribers to be included and excluded
	$groupsVisited = array ();
	$tree = & dbCrawl($dbo, $group_id, $criteriaArray, $groupArray, $groupsVisited);

	// create array containing every field touched by this group's filtering process.
	// FORMAT: 
	// [3]=> array { (3 is field_id)
	//    ["include"]=> array { ["is_equal"/logic] => array { "Milwaukee"/values } }
	//    ["exclude"]=> (same) as above
	//  Include should be parsed with 'AND', exlude with 'AND NOT'
	//  excludes are derived from criteria where a group 'IS NOT BELONG TO'.

	$a = array ();
	$fields = makedemo($tree, $criteriaArray, $a);

	// create array to translate poMMo logic to valid mySql syntax
	$logicTbl = array ();
	$logicTbl['is_equal'] = '=';
	$logicTbl['is_more'] = '>';
	$logicTbl['is_less'] = '<';
	// not_equal, not_true, is_true have been removed b/c they'll never be looked up
	//   see makedemo comments

	// how many fields we're filtering from
	$fieldCount = count($fields);

	$includeCount = 0;
	$excludeCount = 0;
	$includeSQL = '';
	$excludeSQL = '';
	foreach (array_keys($fields) as $field_id) {
		$field = & $fields[$field_id];

		if (!empty ($field['include'])) {
			$includeCount++;
			if ($includeCount > 1)
				$includeSQL .= ' AND ';
			if (isset ($field['include']['is_true']))
				$includeSQL .= '(t' . $includeCount . '.field_id = ' . $field_id . ' AND t' . $includeCount . '.value = \'on\')';
			else
				$includeSQL .= '(t' . $includeCount . '.field_id = \'' . $field_id . '\'' . whereGen($field['include'], $logicTbl, $includeCount) . ')';
		}
		if (!empty ($field['exclude'])) {
			$excludeCount++;
			if ($excludeCount > 1)
				$excludeSQL .= ' AND ';
			if (isset ($field['exclude']['is_true']))
				$excludeSQL .= '(t' . $excludeCount . '.field_id = ' . $field_id . ' AND t' . $excludeCount . '.value = \'on\')';
			else
				$excludeSQL .= '(t' . $excludeCount . '.field_id = \'' . $field_id . '\'' . whereGen($field['exclude'], $logicTbl, $excludeCount) . ')';
		}
	}

	// return arrays and a count the count of them
	$sql = array ();
	$sql['include'] = array (
		& $includeCount,
		& $includeSQL
	);
	$sql['exclude'] = array (
		& $excludeCount,
		& $excludeSQL
	);

	return $sql;
}
?>