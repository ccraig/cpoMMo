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
$serial = (empty($_GET['serial'])) ? time() : addslashes($_GET['serial']);

require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mta.php');

$pommo->init(array('sessionID' => $serial, 'keep' => TRUE, 'authLevel' => 0));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

// don't die on query so we can capture logs'
// NOTE: Be extra careful to check the success of queries/methods!
$dbo->dieOnQuery(FALSE);

// turn logging off unless verbosity is 1
if($pommo->_verbosity > 1)
	$dbo->debug(FALSE);

// start error logging
$pommo->logErrors();

/**********************************
	STARTUP ROUTINES
 *********************************/
$config = PommoAPI::configGet(array(
	'list_exchanger',
	'maxRuntime',
	'smtp_1',
	'smtp_2',
	'smtp_3',
	'smtp_4',
	'throttle_SMTP',
	'throttle_MPS',
	'throttle_BPS',
	'throttle_DP',
	'throttle_DMPP',
	'throttle_DBPP'));
	
$p = array(
	'queueSize' => 100,
	'maxRunTime' => $config['maxRuntime'],
	'serial' => $serial
);

// NOTE: PR15 removed multimode (simultaneous SMTP relays) variables + functionality!
//	we will be migrating to swiftmailer, and its multi-SMTP support/balancing in PR16-ish.


/**********************************
 * MAILING INITIALIZATION
 *********************************/

// calculate spawn # (number of times this MTA has spawned under this serial)
$pommo->_session['spawn'] = (isset($pommo->_session['spawn'])) ? $pommo->_session['spawn']+1 : 1;
$p['spawn'] = $pommo->_session['spawn'];

// initialize MTA
$mailing = new PommoMTA($p);
$logger->addMsg(sprintf(Pommo::_T('Started Mailing MTA. Spawn #%s.'),$p['spawn']),3,TRUE);

// poll mailing status
$mailing->poll();


// check if message body contains personalizations
// personalizations are cached in session

Pommo::requireOnce($pommo->_baseDir.'inc/helpers/personalize.php'); // require once here so that mailer can use
if(!isset($pommo->_session['personalization'])) {
	$pommo->_session['personalization'] = FALSE;
	$matches = array();
	preg_match('/\[\[[^\]]+]]/', $mailing->_mailing['body'], $matches);
	if (!empty($matches))
		$pommo->_session['personalization'] = TRUE;
	preg_match('/\[\[[^\]]+]]/',  $mailing->_mailing['altbody'], $matches);
	if (!empty($matches))
		$pommo->_session['personalization'] = TRUE;

	// cache personalizations in session
	if ($pommo->_session['personalization']) {
		$pommo->_session['personalization_body'] = PommoHelperPersonalize::search($mailing->_mailing['body']);
		$pommo->_session['personalization_altbody'] = PommoHelperPersonalize::search($mailing->_mailing['altbody']);
	}
}

/**********************************
 * PREPARE THE MAILER
 *********************************/
$html = ($mailing->_mailing['ishtml'] == 'on') ? TRUE : FALSE;

$mailer = new PommoMailer($mailing->_mailing['fromname'],$mailing->_mailing['fromemail'],$mailing->_mailing['frombounce'], $config['list_exchanger'],NULL,$mailing->_mailing['charset'], $pommo->_session['personalization']);
if (!$mailer->prepareMail($mailing->_mailing['subject'], $mailing->_mailing['body'], $html, $mailing->_mailing['altbody']))
	$mailer->shutdown('*** ERROR *** prepareMail() returned errors.');
	
// Set appropriate SMTP relay
if ($config['list_exchanger'] == 'smtp') {
	$mailer->setRelay(unserialize($config['smtp_1']));
	//$mailer->setRelay($config['smtp_' . $relayID]); /* PR15: depricated */
	$mailer->SMTPKeepAlive = TRUE;
}

// necessary? (better method!)
$mailing->attach('_mailer',$mailer);


/**********************************
 * INITIALIZE Throttler
 *********************************/
 
 $tid = 1; // forced shared throttler, until swiftmailer implementation
//$tid = ($config['throttle_SMTP'] == 'shared') ? 1 : $relayID; /* old shared throttle support */

if(empty($pommo->_session['throttler'][$tid]))
	$pommo->_session['throttler'] = array (
		$tid => array(
			'base' => array(
				'MPS' => $config['throttle_MPS'],
				'BPS' => $config['throttle_BPS'],
				'DP' => $config['throttle_DP'],
				'DMPP' => $config['throttle_DMPP'],
				'DBPP' => $config['throttle_DBPP'],
				'genesis' => time()
			),
			'domainHistory' => array(),
			'sent' => floatval(0),
			'sentBytes' => floatval(0)
			)
		);
 
$throttler =& new PommoThrottler(
	$pommo->_session['throttler'][$tid]['base'], 
	$pommo->_session['throttler'][$tid]['domainHistory'], 
	$pommo->_session['throttler'][$tid]['sent'],
	$pommo->_session['throttler'][$tid]['sentBytes']
	);

$byteMask = $throttler->byteTracking();
if ($byteMask > 1) // byte tracking/throttling enabled
	$mailer->trackMessageSize();
	
$mailing->attach('_byteMask',$byteMask);

// necessary? (better method!)
$mailing->attach('_throttler',$throttler);


/**********************************
 * INITIALIZE Queue 
 *********************************/

$mailing->pullQueue();
$mailing->pushThrottler();


/**********************************
   PROCESS QUEUE
 *********************************/
 
$mailing->processQueue();
?>