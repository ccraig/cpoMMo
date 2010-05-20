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

// include the field prototype object 
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/prototypes.php');

/**
 * Field: A SubscriberField
 * ==SQL Schema==
 *	field_id		(int)			Database ID/Key
 *	field_active	('on','off')	If field is displayed on subscriber form
 *	field_ordering	(int)			Order in which field is displayed @ subscriber form	
 *	field_name		(str)			Descriptive name for field (used for short identification)
 *	field_prompt	(str)			Prompt assosiated with field on subscriber form
 *	field_normally	(str)			Default value of field on subscriber form
 *	field_array		(str)			A serialized array of  the field such as the options of multiple choice fields (drop down select)
 *	field_required	('on','off')	If field is required for subscription
 *	field_type		(enum)			checkbox, multiple, text, date, number, comment
 */
 
class PommoField {
	
	// makes a field
	// accepts a field template (assoc array)
	// return a field (array)
	function & make($in = array()) {
		$o = PommoType::field();
		return PommoAPI::getParams($o, $in);
	}
	
	// makes a field based off a database row (field schema) 
	// accepts a field template (assoc array) 
	// return a field (array)
	function & makeDB(&$row) {
		$in = @array(
		'id' => $row['field_id'],
		'active' => $row['field_active'],
		'ordering' => $row['field_ordering'],
		'name' => $row['field_name'],
		'prompt' => $row['field_prompt'],
		'normally' => $row['field_normally'],
		'required' => $row['field_required'],
		'type' => $row['field_type']);
		
		if (!empty($row['field_array']))
			$in['array'] = unserialize($row['field_array']);
		
		$o = PommoAPI::getParams(PommoType::field(),$in);
		return $o;
	}
	
	// field validation
	// accepts a field (array)
	// returns true if field ($in) is valid, false if not
	function validate(&$in) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$invalid = array();
		
		if (empty($in['name']) || substr($in['name'],0,1) == '!' || strpos($in['name'],'|')) 
			$invalid[] = 'name';
		else {
			switch (strtolower($in['name'])) {
				case 'email':
				case 'ip':
				case 'registered':
					$invalid[] = 'name';
					break;
				default:
					break;
			}	
		}
		if (empty($in['prompt'])) 
			$invalid[] = 'prompt';
		switch ($in['type']) {
			case "checkbox":
			case "multiple":
			case "text":
			case "date":
			case "number":
			case "comment":
				break;
			default:
				$invalid[] = 'type'; 
		}
		switch ($in['required']) {
			case "on":
			case "off":
				break;
			default: 
				$invalid[] = 'required';
		}
		switch ($in['active']) {
			case "on":
			case "off":
				break;
			default: 
				$invalid[] = 'active';
		}
		if (!is_numeric($in['ordering']))
			$invalid[] = 'ordering';
		if (!is_array($in['array']))
			$invalid[] = 'array';
			
		if (!empty($invalid)) {
			$logger->addErr("Field failed validation on; ".implode(',',$invalid),1);
			return false;
		}
		return true;
	}
	
	// fetches fields from the database
	// accepts a filtering array -->
	//   active (bool) toggle returning of only active fields
	//   id (array) -> an array of field IDs
	//   byName -> will order by name (alphabetical .. else by default order)
	// returns an array of fields. Array key(s) correlates to field key.
	function & get($p = array()) {
		$defaults = array('active' => false, 'id' => null, 'byName' => true);
		$p = PommoAPI :: getParams($defaults, $p);
		
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$p['active'] = ($p['active']) ? 'on' : null;
		
		$p['byName'] = ($p['byName']) ? 'field_name' : 'field_ordering';
		
		$o = array();
		
		$query = "
			SELECT *
			FROM " . $dbo->table['fields']."
			WHERE
				1
				[AND field_active='%S']
				[AND field_id IN(%C)]
			ORDER BY ".$p['byName'];
		$query = $dbo->prepare($query,array($p['active'],$p['id']));
		
		while ($row = $dbo->getRows($query))
			$o[$row['field_id']] = PommoField::makeDB($row);

		return $o;
	}
	
	// fetches field name(s) from the database [NAME ONLY!]
	// accepts a filtering array -->
	//   id (int || array) -> an array of field IDs
	// returns an array of field names. Array key(s) correlates to field ID.
	function & getNames($id = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT field_id, field_name
			FROM " . $dbo->table['fields']."
			WHERE
				1
				[AND field_id IN(%C)]
			ORDER BY field_name";
		$query = $dbo->prepare($query,array($id));
		
		while ($row = $dbo->getRows($query))
			$o[$row['field_id']] = $row['field_name'];
			
		return $o;
	}
	
	
	// fetches field's belonging to a type
	// accepts a field type or array of types
	// returns an array of field IDs
	function & getByType($type) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if(!is_array($type))
			$type = array($type);
		
		$query = "
			SELECT field_id
			FROM " . $dbo->table['fields']."
			WHERE field_type IN (%q)";
		$query = $dbo->prepare($query,array($type));
		
		return $dbo->getAll($query,'assoc','field_id');
	}
	
	// adds a field to the database
	// accepts a field (array)
	// returns the database ID of the added field or FALSE if failed
	function add(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		// set the ordering of field if not provided
		if (!is_numeric($in['ordering'])) {
			$query = "
				SELECT field_ordering
				FROM " . $dbo->table['fields'] . "
				ORDER BY field_ordering DESC";
			$query = $dbo->prepare($query);
			$in['ordering'] = $dbo->query($query, 0) + 1;
		}
		
		if (!PommoField::validate($in))
			return false;
			
		$query = "
		INSERT INTO " . $dbo->table['fields'] . "
		SET
		field_active='%s',
		field_ordering=%i,
		field_name='%s',
		field_prompt='%s',
		field_normally='%s',
		field_array='%s',
		field_required='%s',
		field_type='%s'";
		$query = $dbo->prepare($query,@array(
			$in['active'],
			$in['ordering'],
			$in['name'],
			$in['prompt'],
			$in['normally'],
			serialize($in['array']),
			$in['required'],
			$in['type']
		));
		
		return $dbo->lastId($query);
	}
	
	// removes a field from the database
	// accepts a single ID (int) or array of IDs 
	// returns the # of deleted fields (int). 0 (false) if none.
	function delete(&$id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			DELETE
			FROM " . $dbo->table['fields'] . "
			WHERE field_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		
		return $dbo->affected($query);
	}
	
	// updates a field in the database
	// accepts a field (array)
	// returns success (bool)
	function update(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$in['array'] = (empty($in['array'])) ? null : serialize($in['array']);
		
		$query = "
			UPDATE " . $dbo->table['fields'] . "
			SET
			[field_active='%S',]
			[field_ordering=%I,]
			[field_name='%S',]
			[field_prompt='%S',]
			[field_normally='%S',]
			[field_array='%S',]
			[field_required='%S',]
			[field_type='%S',]
			field_id=field_id
			WHERE field_id=%i";
			$query = $dbo->prepare($query,@array(
				$in['active'],
				$in['ordering'],
				$in['name'],
				$in['prompt'],
				$in['normally'],
				$in['array'],
				$in['required'],
				$in['type'],
				$in['id']
			));
		return ($dbo->query($query)) ? TRUE : FALSE;
	}
	
	// adds an option to a multiple choice field (adds to the array in field_array)
	// accepts a field (array)
	// accepts a value (str)
	// returns field options (array), or false (bool)
	function optionAdd(&$field, $value) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		$value = PommoHelper::trimArray(explode(',',$value));
		
		// add value to the array
		$field['array'] = array_unique(array_merge($field['array'],$value));
		$o = serialize($field['array']);
		
		
		$query = "
			UPDATE " . $dbo->table['fields'] . "
			SET field_array='%s'
			WHERE field_id=%i";
		$query = $dbo->prepare($query,array($o,$field['id']));
		
		return ($dbo->affected($query) > 0) ? $field['array'] : FALSE;
	}
	
	// deletes an option from a multiple choice field (removes from the array in field_array)
	// accepts a field (array)
	// accepts a value (str)
	// returns field options (array), or false (bool)
	function optionDel(&$field, &$value) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		// remove value from array
		$key = array_search($value,$field['array']);
		if (!is_numeric($key)) {
			$logger->addErr("Option ($value) does not exist in field_array",1);			
			return false;
		}
		unset($field['array'][$key]);
		$o = serialize($field['array']);

		$query = "
			UPDATE " . $dbo->table['fields'] . "
			SET field_array='%s'
			WHERE field_id=%i";
		$query = $dbo->prepare($query,array($o,$field['id']));
		
		return ($dbo->affected($query) > 0) ? $field['array'] : FALSE;
	}
	
	// Fetches the subscriber IDs of those who will be affected by a field change
	//  - e.g. they have data associated with this field
	// accepts a single ID (int) or array of IDs.
	// accepts a value to match (str) [optional]
	// returns an (array) of subscriber IDs.
	function subscribersAffected($id = array(), $val = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT DISTINCT subscriber_id
			FROM ".$dbo->table['subscriber_data']."
			WHERE field_id IN(%c)
			[AND value='%S']";
		$query = $dbo->prepare($query,array($id,$val));
		
		while ($row = $dbo->getRows($query,TRUE)) {
			$o[] = $row[0];
		}
		
		return $o;
	}
}
?>
