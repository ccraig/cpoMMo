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

class PommoRules {
	
	// returns the legal(logical) group selections for new filters 
	// accepts a group object
	// accepts an array of all groups
	// returns array of group names. Array key correlates to group's ID
	function & getLegalGroups(&$group, &$groups) {
		$o = array();
		
		foreach($groups as $id => $g) {
			if($g['name'] != $group['name'])
				$o[$id] = $g['name'];
		}
		
		// remember; for is_in/not_in .. field ID should be NULL, value is ID of group to include/exclude
		foreach($group['rules'] as $r) {
			if ($r['logic'] == 'is_in' || $r['logic'] == 'not_in')
				unset($o[$r['value']]);
		}
		
		return $o;
	}
	
	// returns the legal(logical) selections for new filters based off current rules
	// accepts a group object (can be empty -- thus returning all legal field filters)
	// accepts a array of fields
	// returns an array of logics. Array key correlates to field_id.
	function & getLegal(&$group, $fields) {
		$c = array();
		
		$legalities = array(
			'checkbox' => array('true','false'),
			'multiple' => array('is','not'),
			'text' => array('is','not'),
			'date' => array('is','not','greater','less'),
			'number' => array('is','not','greater','less'),
			'comment' => array()
		);
		
		foreach ($fields as $field)
			$c[$field['id']] = $legalities[$field['type']];
		
		if(empty($group['rules']))
			return $c;
		
		// subtract illogical selections from $c
		foreach ($group['rules'] as $rule) {	
			
			if (!isset($c[$rule['field_id']]))
				continue;
			
			// create reference to this field's legalities 
			$l =& $c[$rule['field_id']];
			
			switch($rule['logic']) {
				case 'true' :
				case 'false' :
					// if rule is true or false, field cannot be ANYTHING else
					unset($l[array_search('true', $l)]);
					unset($l[array_search('false', $l)]);
					break;
				case 'is' :
				case 'not' :
					unset($l[array_search('not', $l)]);
					unset($l[array_search('is', $l)]);
					break;
				case 'greater' :
					unset($l[array_search('greater', $l)]);
					break;
				case 'less':
					unset($l[array_search('less', $l)]);
					break;
			}
		}
		
		foreach($c as $key => $val) {
			if (empty($val))
				unset($c[$key]);
		}
		
		return $c;
	}
	
	function getEnglish($str = null) {
		$english = array(
			'is' => Pommo::_T('is'),
			'not' => Pommo::_T('is not'),
			'true' => Pommo::_T('is checked'),
			'false' => Pommo::_T('is not checked'),
			'greater' => Pommo::_T('is greater than'),
			'less' => Pommo::_T('is less than'),
			'is_in' => Pommo::_T('or in group'),
			'not_in' => Pommo::_T('and not in group')
		);
		

		if(is_array($str)) {
			$out = array();
			foreach($str as $val) {
				if(array_key_exists($val,$english))
					$out[$val] = $english[$val];
			}
			return $out;
		}
		
		return (empty($str)) ? $english : $english[$str]; 
	}
	
	function addBoolRule(&$group, &$match, &$logic) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			INSERT INTO " . $dbo->table['group_rules']."
			SET
				group_id=%i,
				field_id=%i,
				logic='%s'";
		$query=$dbo->prepare($query,array($group,$match,$logic));
		return $dbo->affected($query);
	}
	
	function addGroupRule(&$groupID, &$match, &$logic) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			INSERT INTO " . $dbo->table['group_rules']."
			SET
				group_id=%i,
				value=%i,
				logic='%s'";
		$query=$dbo->prepare($query,array($groupID,$match,$logic));
		return $dbo->affected($query);
	}
	
	function addFieldRule(&$group, &$field, &$logic, &$values, $type = 0) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$type = ($type == 'or')? 1 : 0;
		
		// remove previous filters
		PommoRules::deleteRule($group, $field, $logic);
		
		// get the field
		Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
		$field = current(PommoField::get(array('id' => $field)));

		foreach($values as $value) {
			// if this is a date type field, convert the values from human readable date
			//  strings to timestamps appropriate for matching
			if($field['type'] == 'date')
				$value = PommoHelper::timeFromStr($value);
			$v[] = $dbo->prepare("(%i,%i,'%s','%s',%i)",array($group, $field['id'], $logic, $value, $type));
		}
		
		$query = "
			INSERT INTO " . $dbo->table['group_rules']."
			(group_id, field_id, logic, value, type)
			VALUES ".implode(',', $v);
		return $dbo->affected($query);
	}
	
	
	function deleteRule($gid, $fid, $logic) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$where = ($logic == 'is_in' || $logic == 'not_in') ? 
			"AND field_id=0 AND rule_id=%i" :
			"AND field_id=%i";
			
		$query = "
			DELETE FROM " . $dbo->table['group_rules']."
			WHERE group_id=%i AND logic='%s' ".$where;
		$query = $dbo->prepare($query,array($gid, $logic, $fid));
		return ($dbo->affected($query));	
	}
	
	function changeType($gid, $fid, $logic, $type) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$type = ($type == 'or') ? 1 : 0;
			
		$query = "
			UPDATE " . $dbo->table['group_rules']."
			SET type=$type
			WHERE group_id=%i AND logic='%s' AND field_id=%i";
		$query = $dbo->prepare($query,array($gid, $logic, $fid));	
		return ($dbo->affected($query));	
	}
}
?>