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

class PommoForm {

	/**
     * ID for this form - allows multiple (independant validation) of forms per page
     * @var string
     * 
     */
	protected $_id;
	
	/**
     * 
     * Template for each form fields
     * 
     * Keys are ...
     * 
     * `name`
     * : (string) The name of a form field (e.g. <input name="myName" type=... />) [HTML Escaped]
     * 
     * `value`
     * : (string) The value of a form field (e.g. <input value="Brice" ... />) [HTML Escaped]
     * 
     * `label`
     * : (string) The prompt for a form field (e.g. <label for="myName">Enter your Name</label> [HTML Escaped]
     * 
     * `hint`
     * : (string) The sub-label / explanation for a form field. [HTML Escaped]
     * 
     * `type`
     * : (string) The type of a form field, MUST BE valid W3C control type or select or textarea...
     *     text|password|checkbox|radio|submit|reset|file|hidden|image|button|select|textarea
     * 
     * `attribs`
     * : (array) Attributes to be added to the form element in key/value pair; e.g.
     *      array('maxlength' => '60', 'size' => '30') [HTML Escaped]
     *
     *  
     * `error`
     * : (string) Message to display if field does not pass validation.
     *  
     * @var array
     * 
     */
	protected $_fieldTemplate = array(
		'name' => null,
		'value' => null,
		'label' => null,
		'hint' => null,
		'type' => 'text',
		'attribs' => array(),
		'rules' => array(),
		'error' => null
	);
	
	/**
     * Active form fields.
     * @var array
     * 
     */
	protected $_fields = array();
	
	/**
     * Groups of fields, consisting of group name (key), containing a list (array) of field IDs.
     * Fields are added to the "default" group if no group is specified.
     * @var array
     * 
     */
	protected $_groups = array('default' => array());
	
	public function __construct($name = 'PommoForm') {
		$this->_id = $name;
	}
	
	public function addField($field = array('name' => 'unamed'), $group = 'default') {
		if(is_string($field))
			$field = array('name' => $field);
		
		if(!isset($this->_groups[$group]))
			$this->_groups[$group] = array();

		// ensure a list of fields was passed
		if(!is_array($field[key($field)]) || array_key_exists('name',$field))
			$field = array($field);
		
		foreach($field as $key => $val) {
			// sanitize name - often name is passed shorthand as the key of an array of fields vs. explicitly as a key:value pair
			if (is_string($key) && empty($val['name']))
				$val['name'] = $key;
			
			// sanitize / extract rules - often type is embedded in the rules shorthand via $rules = 'type:rules,list'
			if(isset($val['rules']) && !is_array($val['rules'])) {
				$typePos = strpos($val['rules'], ':');
				$type = ($typePos) ? substr($val['rules'],0,$typePos) : 'text';
				$val['rules'] = explode(',',(($typePos) ? substr($val['rules'],$typePos+1) : $val['rules']));
				if(!isset($val['type']))
					$val['type'] = $type;
			}
			
			// add the field to $this->_fields while simultaneously pushing it's ID to the field's group.
			$this->_groups[$group][] = (array_push($this->_fields, array_merge($this->_fieldTemplate,$val)) -1);
		}
	}
	
	
	public function validate($group = false) {
		$fieldIDs = ($group) ? $this->_groups[$group] : array_keys($this->_fields);
		
		foreach($fieldIDs as $id) {
			foreach($this->_fields[$id]['rules'] as $rule) {
				if !($this->_checkRule($rule,$this->_fields[$id]['value']))
					// mark invalid...
			}
		}
		
	
	}
	
	protected function _checkRule($rule, &$value) {
		
		
	}
	
    
}

?>