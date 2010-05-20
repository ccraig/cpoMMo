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

/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/json.php');
$json = new PommoJSON();


// EXAMINE CALL
switch ($_REQUEST['call']) {
	
	case 'updateOrdering': 
		
		$when = '';
		foreach($_REQUEST['order'] as $order => $fieldID) { 
		if(is_numeric($order))
			$when .= $dbo->prepare("WHEN %i THEN %i",array($fieldID,$order)).' ';
		}
		
		$query = "
			UPDATE ".$dbo->table['fields']."
			SET field_ordering = 
				CASE field_id ".$when." ELSE field_ordering END";
		if (!$dbo->query($dbo->prepare($query)))
			$json->fail('Error Updating Order');
		
		$json->add('query',$query);
		$json->success(Pommo::_T('Order Updated.'));
		
	break;
	
	case 'addOption' :
	
		// validate field ID
		$field = current(PommoField::get(array('id' => $_REQUEST['field_id'])));
		if ($field['id'] != $_REQUEST['field_id'])
			die('bad field ID');
	
	
		if(!empty($_REQUEST['options']))
			$options = PommoField::optionAdd($field,$_REQUEST['options']);
			if(!options)
				$json->fail(Pommo::_T('Error with addition.'));
			$json->add('callbackFunction','updateOptions');
			$json->add('callbackParams',$options);
			$json->serve();

	break;
	
	case 'delOption' :
		
		// validate field ID
		$field = current(PommoField::get(array('id' => $_REQUEST['field_id'])));
		if ($field['id'] != $_REQUEST['field_id'])
			die('bad field ID');
	
		$affected = PommoField::subscribersAffected($field['id'],$_REQUEST['options']);
		if(count($affected) > 0 && empty($_REQUEST['confirmed'])) {
			$msg = sprintf(Pommo::_T('Deleting option %1$s will affect %2$s subscribers who have selected this choice. They will be flagged as needing to update their records.'), '<b>'.$_REQUEST['options'].'</b>', '<em>'.count($affected).'</em>');
			$msg .= "\n ".Pommo::_T('Are you sure?');
			$json->add('callbackFunction','confirm');
			$json->add('callbackParams',$msg);
			$json->serve();
		}
		else {
			
			Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
			
			$options = PommoField::optionDel($field,$_REQUEST['options']);
			if(!options)
				$json->fail(Pommo::_T('Error with deletion.'));
			
			// flag subscribers for update
			if(count($affected) > 0)
				PommoSubscriber::flagByID($affected);
			
			$json->add('callbackFunction','updateOptions');
			$json->add('callbackParams',$options);
			$json->serve();
		}
	
	break;
	
	default:
		die('invalid request passed to '.__FILE__);
	break;
}

die();
?>