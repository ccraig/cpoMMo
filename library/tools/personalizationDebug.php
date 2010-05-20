<?php
/** [BEGIN HEADER] **
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

/**********************************
	INITIALIZATION METHODS
 *********************************/
require ('bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/personalize.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');


$pommo->init(array('authLevel' => 0));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


$subscriber = current(PommoSubscriber::get(array('limit' => 3)));

$mailing = array(
	'body' => "Email/XXX --> [[Email|XXX]]

A Date/YYY --> [[a date|YYY]]

Num/n\"qu'te&'XX --> [[num|an\"qu'te&'XX]]

Be good.");


$_SESSION['pommo']['personalization'] = FALSE;
	$matches = array();
	preg_match('/\[\[[^\]]+]]/', $mailing['body'], $matches);
	if (!empty($matches))
		$_SESSION['pommo']['personalization'] = TRUE;
	preg_match('/\[\[[^\]]+]]/', $mailing['altbody'], $matches);
	if (!empty($matches))
		$_SESSION['pommo']['personalization'] = TRUE;

	// cache personalizations in session
	if ($_SESSION['pommo']['personalization']) {
		$_SESSION['pommo']['personalization_body'] = PommoHelperPersonalize::get($mailing['body']);
		$_SESSION['pommo']['personalization_altbody'] = PommoHelperPersonalize::get($mailing['altbody']);
	}
	

$x = PommoHelperPersonalize::body($mailing['body'],$subscriber,$_SESSION['pommo']['personalization_body']);

var_dump($x);

var_dump($_SESSION['pommo']);

var_dump($subscriber);



?>