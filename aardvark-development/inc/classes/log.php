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

// TODO -> class will eventually extend to act as error handler
// TODO -> implement get limit @ some point.... ;)

// NOTE: messages are cleared upon page load (as inc/classes/pommo.php calls new constructor)
// TODO -> add message "revival" from SESSION (if ever deemed necessary)
class PommoLog {
	
	var $_errors;
	var $_log;
	var $_messages;
	var $_verbosity;
	var $_escape; // htmlspecialchars escaping (disabled by default)
	
	function PommoLog($verbosity = 3) {
		$this->_errors = array();
		$this->_messages = array();
		$this->_log = FALSE;
		$this->_verbosity = $verbosity;
		$this->_escape = false; 
	}
	
	function toggleLogging($toggle = TRUE) {
		$this->_log = $toggle;
		return $toggle;
	}
	
	function toggleEscaping($toggle = TRUE) {
		$this->_escape = $toggle;
		return $toggle;
	}
	
	function Add(& $msgs, $level, $timestamp, &$stack ) {
		if ($this->_verbosity > $level)
			// don't add message if verbosity level is below indicated message level
			return false;
		if (!is_array($msgs))
			$msgs = array($msgs);
		if ($timestamp)
			$timestamp = date('H:i:s').' > ';
		foreach($msgs as $msg)
			$stack[] = $timestamp.$msg;
		return true;
	}
	
	function addMsg($messages, $level = 3, $timestamp = FALSE) {
		return $this->Add($messages, $level, $timestamp, $this->_messages);
	}
	
	function addErr($messages, $level = 3, $timestamp = FALSE) {
		return $this->Add($messages, $level, $timestamp, $this->_errors);
	}
	
	function & Get($limit, $clear, & $stack) {
		$msgs = $stack;
		if ($clear)
			$stack = array();
		
		if ($this->_escape)
			array_walk($msgs,'htmlspecialchars');
		return $msgs;
	}
	
	function getMsg($limit = FALSE, $clear = TRUE) {
		return $this->Get($limit,$clear,$this->_messages);
	}
	
	function getErr($limit = FALSE, $clear = TRUE) {
		return $this->Get($limit,$clear,$this->_errors);
	}
	
	function getAll($limit = FALSE, $clear = TRUE) {
		return array_merge($this->Get($limit,$clear,$this->_errors),$this->Get($limit,$clear,$this->_messages));
	}
	
	function isMsg() {
		return count($this->_messages);	
	}
	
	function isErr() {
		return count($this->_errors);
	}
	
	function clear() {
		$this->_messages = array();
		$this->_errors = array();
		return true;		
	}
}
?>