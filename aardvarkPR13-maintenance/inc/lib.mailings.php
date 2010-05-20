<?php /** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/
 
 // TODO: Combine these mailing confirmation functions... they repeat.
 
 /** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

// send a confirmation email
function bmSendConfirmation($to, $confirmation_key, $type) {
	if (empty($confirmation_key) || empty ($to) || empty($type)) 
		return false;
	
	global $poMMo;
	$logger = & $poMMo->_logger;
		
	$dbvalues = $poMMo->getConfig(array('messages'));
	$messages = unserialize($dbvalues['messages']);
	
	$subject = $messages[$type]['sub'];
	
	$url = bm_http.bm_baseUrl.'user/confirm.php?code='.$confirmation_key;
	$body = preg_replace('@\[\[URL\]\]@i',$url,$messages[$type]['msg']);  
	
	if (empty($subject) || empty($body))
		return false;
	
	require_once(bm_baseDir.'/inc/class.bmailer.php');
	$message = new bMailer;
	
	// allow mail to be sent, even if demo mode is on
	$message->toggleDemoMode("off");
	
	// send the confirmation mail
	$message->prepareMail($subject, $body);
	if ($message->bmSendmail($to)) {
		$message->toggleDemoMode();
		return true; // mailing was a sucess...
	}
	// reset demo mode to default
	$message->toggleDemoMode();
	
	$logger->addErr(_T('Error Sending Mail'));
	return false;	
}

// Sends a "test" mailing to an address, returns <string> status.
function bmSendTestMailing(&$to, &$input) {
	require_once (bm_baseDir.'/inc/class.bmailer.php');
	require_once (bm_baseDir.'/inc/lib.txt.php');
		$Mail = new bMailer($input['fromname'], $input['fromemail'], $input['frombounce'],NULL,NULL,$input['charset']);
		$altbody = NULL;
		$html = FALSE;
		if ($input['ishtml'] == 'html')
			$html = TRUE;
		if (!empty($input['altbody']) && $input['altInclude'] == 'yes')
			$altbody = str2str($input['altbody']);
		if (!$Mail->prepareMail(str2str($input['subject']), str2str($input['body']), $html, $altbody)) 
			return '(Errors Preparing Test)';
		
		if (!$Mail->bmSendmail($to))
			return _T('Error Sending Mail');
		return sprintf(_T('Test sent to %s'), $to);
}
	
?>