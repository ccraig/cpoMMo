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

$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/lib/phpmailer/class.phpmailer.php');

// TODO; class depricated since PR13.2 .. needs major overhaul!
//  OLDSCHOOL KLUDGE
//   -- NEEDS TO USE parent:: !!

// create bMailer class (an extension of PHPMailer)
class PommoMailer extends PHPMailer {

	var $_charset;
	var $_personalize;
	
	var $_fromname;
	var $_fromemail;
	var $_frombounce;

	var $_subject;
	var $_body;
	var $_altbody;

	var $_exchanger; // sendmail,mail,smtp ... currently mail or sendmail are used TODO add smtp
	var $_sentCount; // counter for mails sent sucessfully.

	var $_demonstration;
	var $_validated; // if this is TRUE, skip all validation checks + setting of all parameters other than "to" .. this is used for bulk mailing

	var $logger; // references the global logger
	
	// default constructor....

	// called like $pommo = new bMailer(fromname,fromemail,frombounce, exchanger)
	//  If an argument is not supplied, resorts to default value (from setup/config.php).
	function PommoMailer($fromname = NULL, $fromemail = NULL, $frombounce = NULL, $exchanger = NULL, $demonstration = NULL, $charset = NULL, $personalize = FALSE) {
		global $pommo;
		$this->logger =& $pommo->_logger;
		
		// TODO -> only make this call if passed values don't exist ..'
		
		$listConfig = PommoAPI::configGet(array (
			'list_fromname',
			'list_fromemail',
			'list_frombounce',
			'list_exchanger',
			'list_charset'
		));
		
		
		if (empty ($fromname))
			$fromname = $listConfig['list_fromname'];

		if (empty ($fromemail))
			$fromemail = $listConfig['list_fromemail'];

		if (empty ($frombounce))
			$frombounce = $listConfig['list_frombounce'];

		if (empty ($exchanger))
			$exchanger = $listConfig['list_exchanger'];

		if (empty ($demonstration))
			$demonstration = $pommo->_config['demo_mode'];
			
		if (empty($charset))
			$charset = $listConfig['list_charset'];

		// initialize object's values

		$this->_fromname = $fromname;
		$this->_fromemail = $fromemail;
		$this->_frombounce = $frombounce;
		$this->_exchanger = $exchanger;
		$this->_demonstration = $demonstration;
		$this->_charset = $charset;
		$this->_personalize = $personalize;

		$this->_subject = NULL;
		$this->_body = NULL;
		$this->_altbody = NULL;

		$this->_validated = FALSE;

		$this->_sentCount = 0;
		
		$langPath = $pommo->_baseDir . 'inc/lib/phpmailer/language/';
		if (!$this->SetLanguage('en', $langPath))
			return false;
	}

	// toggles demonstration mode on or off if sepcified, or else uses the configured mode. Returns value.
	function toggleDemoMode($val = NULL) {
		global $pommo;
		if ($val == "on")
			$this->_demonstration = "on";
		elseif ($val == "off") 
			$this->_demonstration = "off";
		else
			$this->_demonstration = $pommo->_config['demo_mode'];
		return $this->_demonstration;
	}

	// enable to track size (in bytes) of sent messages.
	function trackMessageSize($bool = TRUE) {
		$this->SaveMessageSize = $bool;
	}

	// returns the size (in bytes) of the last sent message
	function GetMessageSize() {
		return $this->LastMessageSize;
	}

	// sets the SMTP relay for this mailer
	function setRelay(& $smtp) {
		if (!empty ($smtp['host']))
			$this->Host = $smtp['host'];
		if (!empty ($smtp['port']))
			$this->Port = $smtp['port'];
		if (!empty ($smtp['auth']) && $smtp['auth'] == 'on') {
			$this->SMTPAuth = TRUE;
			if (!empty ($smtp['user']))
				$this->Username = $smtp['user'];
			if (!empty ($smtp['pass']))
				$this->Password = $smtp['pass'];
		}
	}

	// Gets called before sending a mail to make sure all is proper (during prepareMail). Returns false if messages were created must pass global poMMo object (TODO maybe rename to site??)
	function validate() {

		if (empty ($this->_fromname)) {
			$this->logger->addMsg("Name cannot be blank.");
			return false;
		}

		if (!PommoHelper::isEmail($this->_fromemail)) {
			$this->logger->addMsg("From email must be a valid email address.");
			return false;	
		}

		if (!PommoHelper::isEmail($this->_frombounce)) {
			$this->logger->addMsg("Bounce email must be a valid email address.");
			return false;	
		}

		if (empty ($this->_subject)) {
			$this->logger->addMsg("Subject cannot be blank.");
			return false;	
		}

		if (empty ($this->_body)) {
			$this->logger->addMsg("Message content cannot be blank.");
			return false;	
		}

		return true;
	}

	// Sets up the mail message. If message is HTML, indicate by setting 3rd argument to TRUE.
	// TODO -> pass by reference??
	function prepareMail($subject = NULL, $body = NULL, $HTML = FALSE, $altbody = NULL) {
		
		$this->_subject = $subject;
		$this->_body = $body;
		$this->_altbody = $altbody;

		// ** Set PHPMailer class parameters

		if ($this->_validated == FALSE) {

			// Validate mail parameters
			if (!$this->validate()) {
				return false;
			}
			// TODO -> should I just set PHPMailer parameters in the 1st place & skip $this->_paramName ?
			// TODO -> pass these by reference ??
			
			// set the character set
			$this->CharSet = $this->_charset;

			$this->FromName = $this->_fromname;
			$this->From = $this->_fromemail;
			$this->Subject = $this->_subject;
			
			// set Sender (bounce address)
			$this->Sender = $this->_frombounce;
			
			// if safe mode is on && using sendmail, force php mail() as excahnger [sendmail will not send w/ safe mode on]
			if (ini_get('safe_mode') && $this->_exchanger == 'sendmail')
				$this->_exchanger = 'mail';

			// make sure exchanger is valid, DEFAULT to PHP Mail
			if ($this->_exchanger != "mail" && $this->_exchanger != "sendmail" && $this->_exchanger != "smtp")
				$this->_exchanger = "mail";				
			
			$this->Mailer = $this->_exchanger;

			if ($this->Mailer == 'smtp') { // loads the default relay (#1) -- use setRelay() to change.
				$config = PommoAPI::configGet('smtp_1');
				$smtp = unserialize($config['smtp_1']);
	
				if (!empty ($smtp['host']))
					$this->Host = $smtp['host'];
				if (!empty ($smtp['port']))
					$this->Port = $smtp['port'];
				if (!empty ($smtp['auth']) && $smtp['auth'] == 'on') {
					$this->SMTPAuth = TRUE;
					if (!empty ($smtp['user']))
						$this->Username = $smtp['user'];
					if (!empty ($smtp['pass']))
						$this->Password = $smtp['pass'];
				}
			}

			// if altbody exists, set message type to HTML + add alt body
			if ($HTML) {
				$this->IsHTML(TRUE);
				if (!empty ($this->_altbody))
					$this->AltBody = $this->_altbody;
			}
			
			$this->Body = $this->_body;

			// passed all sanity checks...
			$this->_validated = TRUE;
		}
		return TRUE;
	}

	// ** SEND MAIL FUNCTION --> pass an array of senders, or a single email address for single mode
	function bmSendmail(& $to, $subscriber = FALSE) { // TODO rename function send in order to not confuse w/ PHPMailer's Send()?

		if ($this->_validated == FALSE) {
			$this->logger->addMsg("poMMo has not passed sanity checks. has prepareMail been called?");
			return false;
		}
		// make sure $to is valid, or send errors...
		elseif (empty ($to)) {
			$this->logger->addMsg("To email supplied to send() command is empty.");
			return false;
		}

		$errors = array ();

		if ($this->_demonstration == "off") { // If poMMo is not in set in demonstration mode, SEND MAILS...

			// if $to is not an array (single email address has been supplied), simply send the mail.
			if (!is_array($to)) {
				$this->AddAddress($to);
				
				// check for personalization personaliztion and override message body
				if ($this->_personalize) {
					global $pommo;
					$this->Body = PommoHelperPersonalize::replace($this->_body, $subscriber, $pommo->_session['personalization_body']);
					if (!empty($this->_altbody))
						$this->AltBody = PommoHelperPersonalize::replace($this->_altbody,$subscriber,$pommo->_session['personalization_altbody']);
				}

				// send the mail. If unsucessful, add error message.
				if (!$this->Send())
					$errors[] = Pommo::_T("Sending failed: ") . $this->ErrorInfo;
				
				$this->ClearAddresses();

			} else {
				// MULTI MODE! -- antiquated.
				// incorporate BCC+Enveloping in here if type is SMTP
				// TODO Play w/ the size limiting of arrays sent here
			}
		} else {
			$this->logger->addMsg(sprintf(Pommo::_T("Mail to: %s not sent. Demonstration mode is active."),(is_array($to)) ? implode(',', $to) : $to));
			return true;
		}

		// if message(s) exist, return false. (Sending failed w/ error messages)
		if (!empty ($errors)) {
			$this->logger->addMsg($errors);
			return false;
		}
		return true;
	}
}
?>