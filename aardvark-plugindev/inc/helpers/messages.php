<?php
/**
 * Copyright (C) 2005, 2006, 2007  Brice Burgess <bhb@iceburg.net>
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

class PommoHelperMessages {

	// send a confirmation message
 	// accepts to address (str) [email]
 	// accepts a confirmation code (str)
 	// accepts a confirmation type (str) either; 'subscribe', 'activate', 'password', 'update'
	function sendConfirmation($to, $confirmation_key, $type) {
	 	global $pommo;
		$logger = & $pommo->_logger;
		
		if (empty($confirmation_key) || empty ($to) || empty($type)) 
			return false;
		
		$dbvalues = PommoAPI::configGet('messages');
		$messages = unserialize($dbvalues['messages']);

		$subject = $messages[$type]['sub'];
	
		$url = ($type == 'activate') ? 
			$pommo->_http.$pommo->_baseUrl.'user/update_activate.php?codeTry=true&Email='.$to.'&code='.$confirmation_key :
			$pommo->_http.$pommo->_baseUrl.'user/confirm.php?code='.$confirmation_key;
			
		$body = preg_replace('@\[\[URL\]\]@i',$url,$messages[$type]['msg']);
		
		if ($type == 'activate') 
			$body = preg_replace('@\[\[CODE\]\]@i',$confirmation_key,$body);
	
		
		if (empty($subject) || empty($body)) 
			return false;
	
		Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailer.php');
		$mail = new PommoMailer();
	
		// allow mail to be sent, even if demo mode is on
		$mail->toggleDemoMode("off");
	
		// send the confirmation mail
		$mail->prepareMail($subject, $body);
		
		$ret = true;
		if (!$mail->bmSendmail($to)) {
			$logger->addErr(Pommo::_T('Error Sending Mail'));
			$ret = false;
		}
		// reset demo mode to default
		$mail->toggleDemoMode();
		return $ret;
	}
	
	function resetDefault($section = 'all') {
		global $pommo;
		$dbo =& $pommo->_dbo;

		$messages = array();
		if ($section != 'all') {
			$config = PommoAPI::configGet(array('messages'));
			$messages = unserialize($config['messages']);
		}

		if ($section == 'all' || $section == 'subscribe') {
		$messages['subscribe'] = array();
		$messages['subscribe']['msg'] = sprintf(Pommo::_T('You have requested to subscribe to %s. We would like to validate your email address before adding you as a subscriber. Please click the link below to be added ->'), $pommo->_config['list_name'])."\r\n\t[[url]]\r\n\r\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['subscribe']['sub'] = Pommo::_T('Subscription request'); 
		$messages['subscribe']['suc'] = Pommo::_T('Welcome to our mailing list. Enjoy your stay.');
		}
		
		if ($section == 'all' || $section == 'activate') {
		$messages['activate'] = array();
		$messages['activate']['msg'] =  sprintf(Pommo::_T('You have requested to activate your records for %s.'),$pommo->_config['list_name']).' '.sprintf(Pommo::_T('Your activation code is %s'),"[[CODE]]\r\n\r\n").Pommo::_T('You can access your records by visiting the link below ->')."\r\n\t[[url]]\r\n\r\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['activate']['sub'] = Pommo::_T('Verify your address'); 
		}
		
		
		if ($section == 'all' || $section == 'password') {
		$messages['password'] = array();
		$messages['password']['msg'] =  sprintf(Pommo::_T('You have requested to change your password for %s.'),$pommo->_config['list_name']).' '.Pommo::_T('Please validate this request by clicking the link below ->')."\r\n\t[[url]]\r\n\r\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['password']['sub'] = Pommo::_T('Change Password request'); 
		$messages['password']['suc'] = Pommo::_T('Your password has been reset. Enjoy!');
		}
		
		if ($section == 'all' || $section == 'unsubscribe') {
		$messages['unsubscribe'] = array();
		$messages['unsubscribe']['suc'] = Pommo::_T('You have successfully unsubscribed. Enjoy your travels.');
		}
		
		if ($section == 'all' || $section == 'update') {
			$messages['update'] = array();
			$messages['update']['msg'] =  sprintf(Pommo::_T('You have requested to update your records for %s.'),$pommo->_config['list_name']).' '.Pommo::_T('Please validate this request by clicking the link below ->')."\n\n\t[[url]]\n\n".Pommo::_T('If you have received this message in error, please ignore it.');
			$messages['update']['sub'] = Pommo::_T('Update Records request'); 
			$messages['update']['suc'] = Pommo::_T('Your records have been updated. Enjoy!');
		}

		$input = array('messages' => serialize($messages));
		PommoAPI::configUpdate($input, TRUE);

		return $messages;
	}
	
	function testExchanger($to,$exchanger) {
		global $pommo;
		Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailer.php');
		
		$subject = Pommo::_T('poMMo test message');
		$body = sprintf(Pommo::_T("This message indicates that poMMo is able to use the %s exchanger."),$exchanger);
		
		$mail = new PommoMailer();
	
		// allow mail to be sent, even if demo mode is on
		$mail->toggleDemoMode("off");
	
		// send the confirmation mail
		$mail->prepareMail($subject, $body);
		
		$ret = true;
		if (!$mail->bmSendmail($to)) {
			$logger->addErr(Pommo::_T('Error Sending Mail'));
			$ret = false;
		}
		// reset demo mode to default
		$mail->toggleDemoMode();
		return $ret;
	}
	
	function notify(&$notices,&$sub,$type) {
		global $pommo;
		Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailer.php');
		
		$mails = PommoHelper::trimArray(explode(',',$notices['email']));
		if(empty($mails[0]))
			return;
			
		$subject = $notices['subject'].' ';
		$body = sprintf(Pommo::_T('poMMo %s Notice'),$type);
		$body .= "  [".date("F j, Y, g:i a")."]\n\n";
		
		$body .= "EMAIL: ".$sub['email']."\n";
		$body .= "IP: ".$sub['ip']."\n";
		$body .= "REGISTERED: ".date("F j, Y, g:i a",$sub['registered'])."\n";
		$body .= "DATA:\n";
		
		Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
		$fields = PommoField::getNames();
		
		foreach($sub['data'] as $fid => $v)
			$body .= "\t".$fields[$fid].": $v\n";
				
		switch($type) {
			case 'subscribe':
				$subject .= Pommo::_T('new subscriber!');
				break;
			case 'unsubscribe':
				$subject .= Pommo::_T('user unsubscribed.');
				break;
			case 'pending':
				$subject .= Pommo::_T('new pending!');
				break;
			case 'update':
				$subject .= Pommo::_T('subscriber updated.');
				break;
		}
		
		$mail = new PommoMailer();
	
		// allow mail to be sent, even if demo mode is on
		$mail->toggleDemoMode("off");
	
		// send the confirmation mail
		$mail->prepareMail($subject, $body);
		
		foreach($mails as $to)
			$mail->bmSendmail($to);
			
		// reset demo mode to default
		$mail->toggleDemoMode();
		return;
	}
}