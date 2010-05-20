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


class PommoWYSIWYG {
	var $_editors = array();
	var $_default;
	
	function PommoWYSIWYG() {
		
/**
 *  Register available WYSIWYG editors and their javascript
 *   dependencies. Javascripts path is relative to "themes/wysiwyg/"
 */
		
		$this->registerEditor('FCKEditor', array(
			'fckeditor.lib.js',
			'fckeditor/fckeditor.js')
		);
		
		// $this->registerEditor('TinyMCE', array('lib.tinymce.js','tinymce/tinymce.js'));
	
		// Determine editor to load during mail composition.
		// TODO; allow selection of editor via mailing configuration page.
		
		$this->_default = 'FCKEditor';
	}
	
/**
 *  No need to edit below this line.
 */
	
	function registerEditor($name, $dependencies) {
		if(empty($name) || empty($dependencies) || !is_array($dependencies))
			return false;
		$this->_editors[$name] = $dependencies;
	}
	
	function loadEditor($name = false) {
		if(!$name)
			$name = $this->_default;
		return (isset($this->_editors[$name])) ? $this->_editors[$name] : false;
	}
}

?>