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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

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

	case 'addSubscriber': 
		
		$json->setFailMsg(Pommo::_T('Error adding subscriber.'));
		
		// check if email is valid
		if (!PommoHelper::isEmail($_REQUEST['Email']))
			$json->fail(Pommo::_T('Invalid email.'));
			
		// check if email has unsubscribed
		if(!isset($_REQUEST['force'])) {
			$unsubscribed = PommoSubscriber::GetIDByEmail($_REQUEST['Email'],0);
			if(!empty($unsubscribed)) 
				$json->fail(sprintf(Pommo::_T('%s has already unsubscribed. To add the subscriber anyway, check the box to force the addition.'),'<strong>'.$_REQUEST['Email'].'</strong>'));
		}
		
		// check if duplicate
		if(PommoHelper::isDupe($_POST['Email']))
			$json->fail(Pommo::_T('Email address already exists. Duplicates are not allowed.'));
			
		$subscriber = array(
			'email' => $_REQUEST['Email'],
			'registered' => time(),
			'ip' => $_SERVER['REMOTE_ADDR'],
			'status' => 1,
			'data' => $_POST['d']);
		
		$flag = false;
		if (!PommoValidate::subscriberData($subscriber['data'],array('active' => FALSE, 'ignore' => TRUE))) {
			if(!isset($_REQUEST['force'])) {
				$json->addMsg(Pommo::_T('Invalid or missing information.'));
				foreach($logger->getAll() as $err)
					$json->addErr($err);
				$json->fail();
			}
			$flag = true;
			$subscriber['flag'] = 9; // 9 for "update"
		}
		
		$key = PommoSubscriber::add($subscriber);
		if (!$key)
			$json->fail();
			
		$json->addMsg(sprintf(Pommo::_T('Subscriber %s added!'),'<strong>'.$subscriber['email'].'</strong>'));
		
		if($flag)
			$json->addErr(Pommo::_T('Subscriber has been flagged for update due to invalid or missing information.'));
		
		// add the subscriber to JSON output
		$data = array(
			'key' => $key,
			'email' => $subscriber['email'],
			'registered' => $subscriber['registered'],
			'touched' => $subscriber['registered'],
			'ip' => $subscriber['ip']
		);
		
		foreach($subscriber['data'] as $k => $val) 
			$data['d'.$k] = htmlspecialchars($val);
		
		$json->add('callbackFunction','addSubscriber');
		$json->add('callbackParams',$data);
		
	break;
	
	case 'delSubscriber':

		$emails = array();
		if (isset($_REQUEST['emails'])) {
			$in = array_unique(preg_split("/[\s,]+/", $_REQUEST['emails']));
			foreach($in as $email) {
				if (PommoHelper::isEmail($email))
					array_push($emails,$email);
			}
		}

		if (count($emails) > 0)  {
			$ids = PommoSubscriber::getIDByEmail($emails,$_REQUEST['status']);
			$msg = sprintf(Pommo::_T('You have removed %s subscribers!'), 
				PommoSubscriber::delete($ids));
				
			$json->add('callbackFunction','delSubscriber');
			$json->add('callbackParams',$ids);
		}
		else 
			$msg = Pommo::_T('No subscribers were removed.');
			
		$json->success($msg);
	
	break;
	
	case 'editSubscriber':
	
		if(!is_numeric($_REQUEST['id']) || $_REQUEST['id'] == 0)
			$json->fail('ERROR; Bad Subscriber ID Received');

			$subscriber = array(
				'id' => $_REQUEST['id'],
				'email' => $_REQUEST['email'],
				'data' => $_REQUEST['d']
			);
	
			$validateOptions = array(
				'skipReq' => TRUE,
				'active' => FALSE
			);

		// check if email is valid
		if (!PommoHelper::isEmail($subscriber['email']))
			$json->fail(Pommo::_T('Invalid email.'));
		
		// check for dupe
		$lookupID = current(PommoSubscriber::getIDByEmail($subscriber['email'],array(1,2)));
		if ($lookupID && $lookupID != $subscriber['id'])
			$json->fail(Pommo::_T('Email address already exists. Duplicates are not allowed.'));
			
		if (!PommoValidate::subscriberData($subscriber['data'],$validateOptions) && 
			!isset($_REQUEST['force'])) {
			$json->addErr(Pommo::_T('Fields failed validation')." >>> ");
			$json->addErr($logger->getAll());
			$json->fail(Pommo::_T('Error updating subscriber.'));
			}	
			
			
		if (!PommoSubscriber::update($subscriber,'REPLACE_ALL'))
			$json->fail(Pommo::_T('Error updating subscriber.'));
			
		// subscriber updated successfully, build output
		$out = array('email' => $subscriber['email'],'id' => $subscriber['id']);
		
		// return human readable date formatting
		Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
		$dateFields = PommoField::getByType('date');
		
		foreach($subscriber['data'] as $k => $val) {
			$out['d'.$k] = in_array($k,$dateFields) ?
				PommoHelper::timeToStr($val) :
				htmlspecialchars($val);
		}	
		
		$json->add('callbackFunction','editSubscriber');
		$json->add('callbackParams',$out);
		
		$json->addMsg(Pommo::_T('Subscriber Updated'));
		
	break;
	default:
		die('invalid request passed to '.__FILE__);
	break;
}

$json->success();
?>