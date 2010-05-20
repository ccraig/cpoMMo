<?php
/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 * 
 * This file is part of poMMo (http://www.pommo.org)
 * 
 * poMMo is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published 
 * by the Free Software Foundation; either version 2, or any later version.
 * 
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// common SQL clauses
// REWRITE ALL...

class PommoSQL {
	
	
	
	// returns where clauses as array
	// accepts a attribute filtering array.
	//   array_key == column, value is filter table filter table (subscriber_pending, subscriber_data, subscribers)
	//   e.g. 
	//   array('pending_code' => array("not: 'abc1234'", "is: 'def123'", "is: '2234'")); 
	//   array(12 => array("not: 'Milwaukee'")); (12 -- numeric -- is alias for field_id=12)
	//   array('status' => array('equal: active'))
	// accepts a table prefix (e.g. WHERE prefix.column = 'value')
	// returns SQL WHERE + JOIN clauses (array)
	
	// DEPRECIATED....
	function & fromFilter(&$in, $p = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$where = $joins = array();
			
		// parse column => logic => value from array
		$filters = array();
		foreach ($in as $col => $val) 
			PommoSQL::getLogic($col,$val,$filters);
		

		// get where &/or joins
		foreach($filters as $col => $l) { 
			if (is_numeric($col)) { // "likely" encountered a field_id in subscriber_data... 
				foreach($l as $logic => $vals) {
					$i = count($joins);
					$join = "LEFT JOIN {$dbo->table['subscriber_data']} $p$i ON (s.subscriber_id = $p$i.subscriber_id AND $p$i.field_id=$col AND ";
					switch ($logic) {
						case "is" :
							$joins[] = $dbo->prepare("[".$join."$p$i.value IN (%Q))]",array($vals)); break;
						case "not":
							$joins[] = $dbo->prepare("[".$join."$p$i.value NOT IN (%Q))]",array($vals)); break;
						case "less":
							$joins[] = $dbo->prepare("[".$join."$p$i.value < %I)]",array($vals[0])); break;
						case "greater":
							$joins[] = $dbo->prepare("[".$join."$p$i.value > %I)]",array($vals[0])); break;
						case "true":
							$joins[] = $join."$p$i.value = 'on')"; break;
						case "false":
							$joins[] = $join."$p$i.value != 'on')"; break;
						case "like" :
							$joins[] = $dbo->prepare("[".$join."$p$i.value LIKE '%%S%']",array($vals[0])); break;		
					}
				}
			}
			else {
				foreach($l as $logic => $vals) {
					switch ($logic) {
						case "is" :
							$where[] = $dbo->prepare("[AND $p.$col IN (%Q)]",array($vals)); break;
						case "not":
							$where[] = $dbo->prepare("[AND $p.$col NOT IN (%Q)]",array($vals)); break;
						case "less":
							$where[] = $dbo->prepare("[AND $p.$col < %I]",array($vals)); break;
						case "greater":
							$where[] = $dbo->prepare("[AND $p.$col > %I]",array($vals)); break;
						case "true":
							$where[] = "AND $p.$col = 'on'"; break;
						case "false":
							$where[] = "AND $p.$col != 'on'"; break;
						case "equal":
							$where[] = $dbo->prepare("[AND $p.$col = '%S']", array($vals[0])); break;
						case "like" :
							$where[] = $dbo->prepare("[AND $p.$col LIKE '%%S%']",array($vals[0])); break;	
					
					}
				}
			}
		}
		// add joins to where clause -- TODO: this is where OR filtering can be looked up!
		$c = count($joins);
		for ($i=0; $i < $c; $i++)
			$where[] = "AND $p$i.subscriber_id IS NOT NULL"; // for an "or", this could be left out!
		
		return array('where' => $where, 'join' => $joins);
	}
	
	// get the column(s) logic + value(s)
	// DEPRECIATED....
	function getLogic(&$col, &$val, &$filters) {
		if (is_array($val)) {
			foreach($val as $v)
				PommoSQL::getLogic($col,$v,$filters);
		}
		else {
			// extract logic ($matches[1]) + value ($matches[2]) 
			preg_match('/^(?:(not|is|less|greater|true|false|equal|like):)?(.*)$/i',$val,$matches);
			if (!empty($matches[1])) { 
				if (empty($filters[$col]))
					$filters[$col] = array();
				if (empty($filters[$col][$matches[1]]))
					$filters[$col][$matches[1]] = array();
				array_push($filters[$col][$matches[1]],trim($matches[2]));
			}
		}
	}
	
	
				
	// A group "rules" array consists of the filtering rules which make up a group
	//  it resembles:
	//	$rules[rule_id] = array (
	//		'field_id' => $row['field_id'],
  	//		'logic' => $row['logic'],
	//		'value' => $row['value'],
	//	);
	
	
	// seperates and, or, and group inclusion/exclusion rules
	// accepts a group rules array
	// returns a seperated rules array
	function & sortRules(&$rules) {
		$o = array(
			'and' => array(),
			'or' => array(),
			'include' => array(),
			'exclude' => array()
		);
		
		foreach($rules as $id => $r) {
			
			if($r['or'])
				$o['or'][$id] = $r;
			else 
			switch ($r['logic']) {
				case 'is_in':
					$o['include'][$id] = $r['value'];
					break;
				case 'not_in':
					$o['exclude'][$id] = $r['value'];
					break;
				default:
					$o['and'][$id] = $r;
					break;
			}		
		}
		return $o;
	}
	
	
	// LOGIC is either; "is, is not, less, greater, true, false, NOT IN, IN"
	
	// A "logic array" resembles:
	//  $logic[field_id] = array(
	//		[logic] => array(values)
	//		is_in => array(1,2)
	//	);
	
	// accepts a group rules array
	// returns a logic array
	function & sortLogic(&$rules) {
		$o = array();
		
		foreach($rules as $r) {
			if(!isset($o[$r['field_id']]))
				$o[$r['field_id']] = array();
			if(!isset($o[$r['field_id']][$r['logic']]))
				$o[$r['field_id']][$r['logic']] = array();
			array_push($o[$r['field_id']][$r['logic']], $r['value']);
		}
		
		return $o;
	}
	
	// accepts a logic array
	// returns an array or SQL sub queries
	function & getSubQueries(&$in) { 
		global $pommo;
		$dbo =& $pommo->_dbo;

		$o = array();
		foreach($in as $fid => $a) {
		
			$sql = "subscriber_id IN
				(select subscriber_id from {$dbo->table['subscriber_data']} WHERE field_id=$fid ";
			
			foreach($a as $logic => $v) {
				switch ($logic) {
						case "is" :
							$sql.= $dbo->prepare("[ AND value IN (%Q)]",array($v)); break;
						case "not":
							$sql.= $dbo->prepare("[ AND value NOT IN (%Q)]",array($v)); break;
						case "less":
							$sql.= $dbo->prepare("[ AND value < %I ]",array($v[0])); break;
						case "greater":
							$sql.= $dbo->prepare("[ AND value > %I ]",array($v[0])); break;
						case "true":
							// WHERE field_id=$fid is already sufficient
							break; 
						case "false":
							$sql = "subscriber_id NOT IN (select subscriber_ID from {$dbo->table['subscriber_data']} WHERE field_id=$fid";
							break;
					}
			}
			$sql .= ")";
			array_push($o,$sql);
		}

		return $o;
	}
	
	// generate the group SQL subselects
	// accepts a group object
	function groupSQL(&$group, $tally = false, $status = 1, $filter = false) {
		// used to prevent against group include/exclude recursion
		static $groups;
		if (!isset ($groups[$group['id']])) 
			$groups[$group['id']] = TRUE;
		
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		/*
		SELECT count(subscriber_id)
			from subscribers 
			where 
			status ='1' 
			AND ( // base group
			subscriber_id in 
				(select subscriber_id from subscriber_data  where  field_id =3 and value IN ('on'))
			AND subscriber_id in 
				(select subscriber_id from subscriber_data  where  field_id =4 and value NOT IN ('lemur'))
			OR subscriber_id in
				(select subscriber_id from subscriber_data  where  field_id =5 and value NOT IN ('on'))
			)
			AND subscriber_ID NOT IN(  // exclude group
				SELECT subscriber_id from subscribers where status ='1' AND (
					subscriber_id in
						(select ... zzz)
					AND subsriber_id in
						(select ... zzz)
					OR subscriber_id in
						(select ... zzz)
				)
			)
			OR subscriber_ID IN(  // include group
				SELECT subscriber_id from subscribers where status ='1' AND (
					subscriber_id in
						(select ... zzz)
					AND subsriber_id in
						(select ... zzz)
					OR subscriber_id in
						(select ... zzz)
				)
			)
			*/
			
		$rules = PommoSQL::sortRules($group['rules']);
		$ands = PommoSQL::getSubQueries(PommoSQL::sortLogic($rules['and']));
		$ors = (empty($rules['or'])) ? 
			array() : 
			PommoSQL::getSubQueries(PommoSQL::sortLogic($rules['or']));
		
		$sql = ($tally) ?
			'SELECT count(subscriber_id) ' :
			'SELECT subscriber_id ';
	
		$sql .= "
			FROM {$dbo->table['subscribers']}
			WHERE status=".intval($status);
		
		$q = FALSE;
		
		if(!empty($ands)) {
			$sql .= " AND (\n";
		
			foreach($ands as $k => $s) {
				if($k != 0)
					$sql .= "\n AND ";
				$sql .= $s;
			}
			foreach($ors as $s)
				$sql .= "\n OR $s";
				
			$sql .="\n)";
			
			$q = TRUE;
		}
		
		foreach($rules['exclude'] as $gid) {
			if (!isset($groups[$gid])) {
				$sql .= "\nAND subscriber_id NOT IN (\n";
				$sql .= PommoSQL::groupSQL(current(PommoGroup::get(array('id' => $gid))));
				$sql .= "\n)";
			}
			$q = TRUE;
		}
		
		foreach($rules['include'] as $gid) {
			if (!isset($groups[$gid])) {
				$sql .= "\n".(($q) ? 'OR' : 'AND')." subscriber_id IN (\n";
				$sql .= PommoSQL::groupSQL(current(PommoGroup::get(array('id' => $gid))));
				$sql .= "\n)";
			}
			$q = TRUE;
		}
		
		// If a filter/search is requested, perform a match
		if(is_array($filter) && !empty($filter['field']) && !empty($filter['string'])) {
		
			// make MySQL LIKE() compliant
			$filter['string'] = mysql_real_escape_string(addcslashes($filter['string'],'%_'));
			
			$sql .= (is_numeric($filter['field'])) ?
				"\n AND subscriber_id in (select subscriber_id from {$dbo->table['subscriber_data']} WHERE field_id = ".(int)$filter['field']." AND value LIKE '%{$filter['string']}%')" :
				"\n AND ".mysql_real_escape_string($filter['field'])." LIKE '%{$filter['string']}%'";
		}
		return $sql;
	}
	
}
?>