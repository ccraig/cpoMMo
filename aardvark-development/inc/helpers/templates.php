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
 
// include the mailing prototype object 
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/prototypes.php');

/**
	 * Template: A Template for Mailings
	 * ==SQL Schema==
	 *	template_id		(int)		Database ID/Key
	 *	name			(str)		Descriptive name for field (used for short identification)
	 *	description		(str)		Summary of Template
	 *  body			(str)		HTML body
	 *  altbody			(str)		Text body
	 */

class PommoMailingTemplate {
	
	// make a mailing template
	// accepts a mailing template (assoc array)
	// return a template object (array)
	function & make($in = array()) {
		$o = PommoType::template();
		return PommoAPI::getParams($o, $in);
	}
	
	// make a mailing template based off a database row (mailing* schema)
	// accepts a mailing template (assoc array)
	// return a template object (array)	
	function & makeDB(&$row) {
		$in = @array(
		'id' => $row['template_id'],
		'name' => $row['name'],
		'description' => $row['description'],
		'body' => $row['body'],
		'altbody' => $row['altbody']);
		
		$o = PommoAPI::getParams(PommoType::template(),$in);
		return $o;
	}
	
	// template validation
	// accepts a template object (array)
	// returns true if template ($in) is valid, false if not
	function validate(&$in) {
		global $pommo;

		$invalid = array();

		if (empty($in['name']))
			$invalid[] = 'name';
		if (empty($in['body']) && empty($in['altbody']))
			$invalid[] = Pommo::_T('Both HTML and Text cannot be empty');
		
		if (!empty($invalid)) {
			$pommo->_logger->addErr(implode(',',$invalid),3);
			return false;
		}
		return true;
	}
	
	// fetches templates from the database
	// accepts a filtering array -->
	//   id (array||str) -> A single or an array of template IDs
	//   name (str) name of mailing template
	// returns an array of mailings. Array key(s) correlates to template ID.
	function & get($p = array()) {
		$defaults = array('id' => null, 'name' => null);
		$p = PommoAPI :: getParams($defaults, $p);
		
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT *
			FROM " . $dbo->table['templates']."
			WHERE
				1
				[AND name='%S']
				[AND template_id IN(%C)]";
		$query = $dbo->prepare($query,array($p['name'],$p['id']));
		
		while ($row = $dbo->getRows($query)) {
			$o[$row['template_id']] = PommoMailingTemplate::makeDB($row); }

		return $o;
	}
	
	// fetches templates names from the database
	// accepts a filtering array -->
	//   id (array||str) -> A single or an array of template IDs
	//   name (str) name of mailing template
	// returns an array of mailings. Array key(s) correlates to template ID.
	function & getNames($p = array()) {
		$defaults = array('id' => null, 'name' => null);
		$p = PommoAPI :: getParams($defaults, $p);
		
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT template_id, name
			FROM " . $dbo->table['templates']."
			WHERE
				1
				[AND name='%S']
				[AND template_id IN(%C)]
			ORDER BY name";
		$query = $dbo->prepare($query,array($p['name'],$p['id']));
		
		while ($row = $dbo->getRows($query)) 
			$o[$row['template_id']] = $row['name'];
			
		return $o;
	}
	
	// fetches templates descriptions from the database
	// accepts a filtering array -->
	//   id (array||str) -> A single or an array of template IDs
	//   name (str) name of mailing template
	// returns an array of mailings. Array key(s) correlates to template ID.
	function & getDescriptions($p = array()) {
		$defaults = array('id' => null, 'name' => null);
		$p = PommoAPI :: getParams($defaults, $p);
		
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT template_id, description
			FROM " . $dbo->table['templates']."
			WHERE
				1
				[AND name='%S']
				[AND template_id IN(%C)]
			ORDER BY name";
		$query = $dbo->prepare($query,array($p['name'],$p['id']));
		
		while ($row = $dbo->getRows($query)) 
			$o[$row['template_id']] = $row['description'];
			
		return $o;
	}
	
	// adds a template to the database
	// accepts a template (array)
	// returns the database ID of the added mailing
	function add(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if (!PommoMailingTemplate::validate($in)) 
			return false;
		
		$query = "
			INSERT INTO " . $dbo->table['templates'] . "
			SET
			[description='%S',]
			[body='%S',]
			[altbody='%S',]
			name='%s'";
		$query = $dbo->prepare($query,@array(
			$in['description'],
			$in['body'],
			$in['altbody'],
			$in['name']));
		
		// fetch new subscriber's ID
		$id = $dbo->lastId($query);
		
		return (!$id) ? false : $id;
	}
	
	// removes a mailing from the database
	// accepts a single ID (int) or array of IDs 
	// returns the # of deleted subscribers (int). 0 (false) if none.
	function delete(&$id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			DELETE
			FROM " . $dbo->table['templates'] . "
			WHERE template_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		
		return $dbo->affected($query);
	}
}
?>
