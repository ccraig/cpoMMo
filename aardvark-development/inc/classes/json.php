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

class PommoJSON {

	var $_output;
	var $_successMsg;
	var $_failMsg;

	function PommoJSON($toggleEscaping = true) {
		global $pommo;
		
		if($toggleEscaping) {
			$pommo->logErrors(); // PHP Errors are logged, turns display_errors off.
			$pommo->toggleEscaping(); // Wraps _T and logger responses with htmlspecialchars()
		}
		
		$this->_output = array(
			'success' => false,
			'messages' => array(),
			'errors' => array()
		);
		
		$this->_successMsg = $this->_failMsg = false;
	}
	
	function addMsg($m) {
		$this->add('messages',$m);
	}
	
	function addErr($e) {
		$this->add('errors',$e);
	}
	
	// add a key: value to JSON output
	//   accepts a key and value ("success",true), or an array (array('success' => true, 'message' => "hola!"))
	//   beware of key conflicts (especially when passing arrays)!
	function add($key,$value = false) {
		if(is_array($key))
			$this->_output = array_merge($key, $this->_output);
		elseif(array_key_exists($key,$this->_output)) {
			if(!is_array($this->_output[$key]))
				$this->_output[$key] = array($this->_output[$key]);
			
			if(is_array($value))
				$this->_output[$key] = array_merge($this->_output[$key],$value);
			else
				$this->_output[$key][] = $value;
		}
		else
			$this->_output[$key] = $value;
	}
	
	// sets the default success/failure message
	function setFailMsg($msg) { $this->_failMsg = $msg; }
	function setSuccessMsg($msg) { $this->_successMsg = $msg; }
	
	// adds a prefix to a message. If message is an array, insert prefix to
	//   beginning of array. 
	// NOTE; message is passed by reference (modified via this function).
	function prefix($prefix, &$msg) {
		if (is_array($msg)) {
			$prefix = array($prefix);
			$msg = array_merge($prefix,$msg);
		}
		$msg = $prefix.$msg;
	}
	
	// return/output the JSON
	function serve($success = true) {
		$this->_output['success'] = $success;
		
		// if a default fail or success message exists, prefix it to the output message
		if($success && $this->_successMsg) 
			$this->prefix($this->_successMsg, $this->_output['message']);
		elseif(!$success && $this->_failMsg)
			$this->prefix($this->_failMsg, $this->_output['message']);
			
		die($this->encode($this->_output));
	}
	
	function fail($msg = false) {
		if($msg)
			$this->addErr($msg);
		$this->serve(false);
	}
	
	function success($msg = false) {
		if($msg)
			$this->addMsg($msg);
		$this->serve(true);
	}
		
	/* JSON Encoding Methods authored by Jack Sleight (below);
	----------------------------------------------------------------------
		Version 0.5
        Copyright Jack Sleight - www.reallyshiny.com
        This script is licensed under the:
            Creative Commons Attribution-ShareAlike 2.5 License
    ----------------------------------------------------------------------
	*/
		
	function encode($input) {
		$output = $this->get(NULL, $input);
		return $output;    
	}
	
	function get($key, $value, $parent = NULL) {
		$type = $this->type($key, $value);
		switch ($type) {
		case 'object': 
			$value = '{'.$this->loop($value, $type).'}';
			break;
		case 'array':
			$value = '['.$this->loop($value, $type).']';
			break;
		case 'number':
			$value = $value;
			break;
		case 'string':
			$value = '"'.$this->escape($value).'"';
			break;
		case 'boolean':
			$value = ($value) ? 'true' : 'false';
			break;
		case 'null':
			$value = 'null';
			break;
		}
		if(!is_null($key) && $parent != 'array')
		$value = '"'.$key.'":'.$value;
		return $value;
	}
	
	function type($key, $value) {
		if(is_object($value))
			$type = 'object';
		elseif(is_array($value)) {
			if($this->is_assoc($value))
				$type = 'object';
			else
				$type = 'array';
		}
		elseif(is_int($value) || is_float($value))
			$type = 'number';
		elseif(is_string($value))
			$type = 'string';
		elseif(is_bool($value))
			$type = 'boolean';
		elseif(is_null($value))
			$type = 'null';
		else
			die($key.' is of an unsupported type.');
		return $type;
	}
	
	function loop($input, $type) {
		$output = NULL;
		foreach($input as $key => $value)
			$output .= $this->get($key, $value, $type).',';
		$output = trim($output, ',');
		return $output;
	}
	
	function escape($string) {
		$find = array('\\',        '"',    '/',    "\b",    "\f",    "\n",    "\r",    "\t",    "\u");
		$repl = array('\\\\',    '\"',    '\/',    '\b',    '\f',    '\n',    '\r',    '\t',    '\u');
		$string = str_replace($find, $repl, $string);
		return $string;
	}
	
	function is_assoc($array) {
		krsort($array, SORT_STRING);
		return !is_numeric(key($array));
	}
    
}

?>
