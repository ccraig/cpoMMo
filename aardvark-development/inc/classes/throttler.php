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


// TODO -> This class needs rewritting -- better/clearer methods!

// NOTE: Due to lack of support for usleep in PHP < 5.0 on IIS/Windows, Throttling is limited to *seconds*
class PommoThrottler {

	// THROTTLE VARIABLES, EXPLANATION
	// -----------------------------------------------

	/**
	 * Beginning time of first mailing process script (seconds)
	 * @var int
	 */
	var $_genesis;

	/**
	 * Time current processing script started (seconds)
	 * @var int
	 */
	var $_startTime;

	/**
	 * The mode that this throttler is engaged in,
	 * 1: Hesitate	| Sets mode to evaluate, Pauses, Releases a mail from queue.
	 * 2: Run 		| Increments cycle count, Sets mode to hesitate if at end of cycle, Releases a mail from queue
	 * 3: Evaluate	| Evaluates mails per second, Adjusts periods and run cycle, Sleeps or changes mode
	 * @var int
	 */
	var $_mode = 3;

	/**
	 * Number of mails to release during a run cycle before hesitation 
	 * @var int
	 */
	var $_cycleSize = 1;

	/**
	 * Mails sent by a run cycle
	 * @var int
	 */
	var $_cycleCount = 0;

	/**
	 * Period (seconds) to hesitate before releaseing a mail from the queue
	 * Set when mode is evaluate
	 * @var int
	 */
	var $_hesitatePeriod = 1;

	/**
	 * Target mails per period (second) to achieve -- averaged
	 * @var float
	 */
	var $_targetMPS;

	/**
	 * Target bytes per period (second) to NOT exceed -- averaged
	 * @var float
	 */
	var $_targetBPS;

	/**
	 * Calculated actual/realtime mails per second -- averaged
	 * @var float
	 */
	var $_actualMPS = 0.0;

	/**
	 * Calculated actual/realtime bytes per second -- averaged
	 * @var float
	 */
	var $_actualBPS = 0.0;

	/**
	 * Calculated average bytes per mailing
	 * @var float
	 */
	var $_averageBytes = 0.0;

	/**
	* Total number of mails releaseed by throttler (cummulative)
	* @var float
	*/
	var $_sentMails = 0.0;

	/**
	 * Total bytes sent via email (cummulative)
	 * @var float
	 */
	var $_sentBytes = 0.0;

	/**
	 * The throttler queue of emails.
	 *  Array(array(0 => 'me@example.com', 1 => 'example.com'))
	 *  $_queue[x][0] == .the email.
	 *  $_queue[x][1] == .the domain.
	 * @var array
	 */
	var $_queue;


	// DOMAIN THROTTLING VARIABLES

	/**
	 * Quarentined emails (emails that if sent, would exceed domain throttling thresholds)
	 *  Array(array(0 => 'me@example.com', 1 => 'example.com'))
	 *  $_quarantine[x][0] == .the email.
	 *  $_quarantine[x][1] == .the domain.
	 * @var array
	 */
	var $_quarantine;

	/**
	 * Domain meta data. Holds array containing the # of mails and bytes sent to a domain in a period
	 *  Array(example.com => array(0 => '183673892212', 1 => '3', 2 => '14812'))
	 *  $_domain[example.com][0] == .time (in seconds) period began.
	 *  $_domain[example.com][1] == .number of mails sent this period.
	 *  $_domain[example.com][2] == .number of bytes sent this period.
	 * @var array
	 */
	var $_domain;

	/**
	 * Length of domain period (in seconds) to throttle against
	 * @var int
	 */
	var $_domPeriod;

	/**
	 * Maximum # of mails sent to a domain per period
	 * @var int
	 */
	var $_domMPP;

	/**
	 * Maximum # of bytes sent to a domain per period
	 * @var int
	 */
	var $_domBPP;

	// DEFAULT CONSTRUCTOR
	// ------------------------------

	// bThrottler() - simple initialization of class variables.
	function PommoThrottler($p = array(), & $history, &$sent, &$sentBytes) { 
		global $pommo;
		
		$this->logger =& $pommo->_logger;
		
		$this->_targetMPS = floatval($p['MPS']);
		$this->_targetBPS = floatval($p['BPS']);
		$this->_domPeriod = floatval($p['DP']);
		$this->_domMPP = floatval($p['DMPP']);
		$this->_domBPP = floatval($p['DBPP']);
		$this->_genesis = $p['genesis'];
		
		$this->_domain =& $history;
		
		$this->_sentBytes =& $sentBytes;
		$this->_sentMails =& $sent;
		
		$this->_startTime = time();
		$this->_messages = array ();
		
		$this->_mode = $this->smartInit();
		
		$this->clearQueue();
		
		// alter adjustment to better handle very slow MPS settings
		if ($this->_targetMPS < 0.35)
			$this->_adjust = 0.002;
		elseif($this->_targetMPS < 0.55)
			$this->_adjust = 0.009;
		elseif($this->_targetMPS < 0.8)
			$this->_adjust = 0.11;
		else
			$this->_adjust = 0.15;
		
		$this->logger->addMsg('bThrottler initialized. [Genesis] ' . $this->_genesis . ' [Queue Size] ' . count($this->_queue) . ' [Target MPS] ' . $this->_targetMPS . ' [Target BPS] ' . $this->_targetBPS . ' [Domain Period] ' . $this->_domPeriod . ' [Domain MPP] ' . $this->_domMPP . ' [Domain BPP] ' . $this->_domBPP, 1);		
	}

	// PARENT METHODS (executed by mailing script)
	// -----------------------------------------------------------

	function clearQueue() {
		$this->_quarantine = array();
		$this->_queue = array();
		return;
	}
	
	function pushQueue(&$q) {
		$this->_queue = & $q;
		return;
	}

	// updateBytes() - Updates the bytes sent. Called by parent when byte throttling is enabled.
	function updateBytes($bytes = 0, $domain = FALSE) {
		$this->_sentBytes += $bytes;

		if ($this->_domBPP > 0 && isset($this->_domain[$domain])) {
			$this->_domain[$domain][2] += $bytes;
			$this->logger->addMsg('updateBytes() called, added ' . $bytes . ' bytes to total and ' . $domain, 2);
		} else
			$this->logger->addMsg('updateBytes() called, added ' .
			$bytes . ' bytes to total', 2);
		return;
	}

	// mailsInQueue() - Returns TRUE if there are mails left to process
	function mailsInQueue() {
		if (empty ($this->_queue))
			return FALSE;
		return TRUE;
	}
	
	// adds more emails to the queue.
	function loadQueue( & $queue) {
		$this->_queue = array_merge($this->_queue,$queue);
		return;
	}
	
	// returns status of byte tracking - '1' if disabled, '2' if enabled, '3' if domain enabled, '4' if both enabled
	function byteTracking() {
		$mask = 1;
		if ($this->_targetBPS > 0)
			$mask += 1;
		if ($this->_domBPP > 0)
			$mask += 2;
		return $mask;
	}


	// pullQueue() - pops & returns an email address from the queue or quarantine. Returns FALSE if throttled back.
	function pullQueue() {

		$retVal = FALSE; // return false by default

		// behave according to the set mode
		switch ($this->_mode) {

			case 1 : // **hesitate**

				$this->logger->addMsg('pullQueue() called - in hesitate mode',1);
				
				$this->_mode = 3; // set mode to evaluate
				$this->pause();

				// release an email from the queue
				$retVal = $this->release();
				break;

			case 2 : // **run**
			
				$this->_cycleCount++;
				$this->logger->addMsg('pullQueue() called, in run mode (cycle ' . $this->_cycleCount . ' of ' . $this->_cycleSize . ')', 1);
	
				if ($this->_cycleSize == $this->_cycleCount) {
					$this->_mode = 1; // set mode to hesitate
					$this->_cycleCount = 0; // reset Cycle
				}

				// release an email from the queue
				$retVal = $this->release();
				break;

			case 3 : // **evaluate** 

				$this->logger->addMsg('pullQueue() called, in evaluate mode', 1);

				if ($this->_targetBPS > 0) { // byte throttling is enabled
					// BEHAVIOR: Jump to maximum bytes per second, then throttle back when/if reached

					// calculate actual bytes per second
					$this->_actualBPS = ($this->_sentBytes / (time() - $this->_genesis));

					// calculate average bytes per mailing [calculated from first 10 mails to avoid large computation]
					if ($this->_sentMails <= 10) {
						$this->_averageBytes = ($this->_sentMails == 0) ? $this->_sentBytes : $this->_sentBytes / $this->_sentMails;
					}

					$this->logger->addMsg('BPS Throttle -> calculated to ' . $this->_actualBPS . 'BPS with average message size of ' . $this->_averageBytes . ' bytes and a total of ' . $this->_sentBytes . ' bytes sent.', 2);

					// attempt to slow the engine					
					$this->slowBytes();
				}

				if ($this->_targetMPS > 0) { // mail throttling is enabled
					// BEHAVIOR: Attempt to match the targeted mails per second through small speedups and slowdowns

					// calculate actual mails per second
					$this->_actualMPS = ($this->_sentMails > 0) ? 
						$this->_sentMails / (time() - $this->_genesis) :
						0;
					$this->logger->addMsg('MPS Throttle -> calculated to ' . $this->_actualMPS . 'MPS', 2);
					
					$suggest = 3; // suggest returning to evaluate mode by default
					
					if ($this->_actualMPS === 0) {
						// keep in eval if unleashing a mail will be way above threshold
						if ((1 / ((time() - $this->_genesis)+0.5)) < $this->_targetMPS + 0.15)
							$suggest = 1;
						else
							$this->pause();
					}
					elseif ($this->_actualMPS > ($this->_targetMPS + $this->_adjust))
						$suggest = $this->slowdown();
					elseif ($this->_actualMPS < ($this->_targetMPS - $this->_adjust)) $suggest = $this->speedup();
					else { // on target! recall initialize?
						$this->logger->addMsg('MPS Throttle -> at "sweet" spot', 2);
						$suggest = $this->smartInit();
					}
					$this->_mode = $suggest;
				}
				else { // throttling disabled
					$retVal = $this->release();
				}
				break;
		}

		if ($retVal) { // an email was successfully pulled from the queue / quarantine
			$this->_sentMails += 1;
			$this->logger->addMsg('pullQueue() released ' . $retVal[0] . '. ' . $this->_sentMails . ' mails released so far.', 2);
		}
		return $retVal;

	}
	
	// attempts to release an email from the queue or quarantine
	function release() {
		if ($this->_domMPP > 0 || $this->_domMPP > 0)  // domain throttling enabled
			return $this->domainRelease(); // call domain thrtottler release
		else // domain throtting NOT enabled
			$email = array_pop($this->_queue);
		return $email;
	}
	
	// attempts to release an email from the queue or quarantine, after checking with domain controller
	function domainRelease($attempt = 0) {
		$attempt++;
		$this->logger->addMsg('domainRelease(), Attempt #'.$attempt.' to release from domain controller',1);
		
		if ($attempt > 10)
			sleep(1);
			
		if ($attempt == 15)
			$this->logger->addMsg('Domain Throttle Controller is bottlenecking, release attempts are exceeding thresholds.',3);
			
		if ($attempt == 20)
			return false;
			
		// decide if we'll be attempting to release from queue or quarantine	
		if (empty($this->_quarantine)) 
				$queue = TRUE;
			elseif ($attempt < 10) // prioritize removal of entries in queue...
				$queue = FALSE;
			elseif ($attempt % 2 == 0 && !empty($this->_queue)) 
				$queue = TRUE;
			else
				$queue = FALSE;
				
		if ($queue) { // poll queue
			$email = array_pop($this->_queue);
			if ($this->domainCheck($email[1]))
				return $email;

			$this->logger->addMsg('domainRelease() - Quarantining ' . $email[0] . '. Domain '.$email[1] . ' has reached its limits.', 2);
			$this->_quarantine[] = $email;
		}
		else { // poll quarantine
			srand((float) microtime() * 10000000);
			$key = array_rand($this->_quarantine);
			$email = $this->_quarantine[$key];
			if ($this->domainCheck($email[1])) {
				unset ($this->_quarantine[$key]);
				return $email;
			}
		}
		// recursively loop through attempts. TODO -> would a iterative method be more efficient?
		return $this->domainRelease($attempt);
	}

	// domainCheck() - validates that a domain has not exceeded its limit per period
	function domainCheck(& $domain) {

		/**
		* Domain meta data. Holds array containing the # of mails and bytes sent to a domain in a period
		*  Array(example.com => array(0 => '183673892212', 1 => '3', 2 => '14812'))
		*  $_domain[example.com][0] == .time (in seconds) period began.
		*  $_domain[example.com][1] == .number of mails sent this period.
		*  $_domain[example.com][2] == .number of bytes sent this period.
		* @var array
		*/

		// create a new entry for this domain if one doesn't exist'
		if (!isset ($this->_domain[$domain]))
			$this->_domain[$domain] = array (
			time(), 0, 0);

		// check to see if we're within limits of mails per period + bytes per second
		if (($this->_domain[$domain][1] < $this->_domMPP) && ($this->_domain[$domain][2] <= $this->_domBPP)) {
			$this->_domain[$domain][1]++;
			return true;
		}

		// limit has been reached. compare times for reset. 
		if ((time() - $this->_domain[$domain][0]) > $this->_domPeriod) {
			$this->_domain[$domain][0] = time();
			$this->_domain[$domain][1] = 1;
			return true;
		} // period has not expired, threshold reached.
		return false;
	}

	function slowBytes() {
		if (($this->_averageBytes + $this->_sentBytes) / (time() - $this->_genesis) > $this->_targetBPS) { // bytes per second threshold at limit

			// calculate optimal rest period, add cycleSize of seconds of buffer
			$rest = $this->_cycleSize + ((($this->_averageBytes + $this->_sentBytes) / $this->_targetBPS) - (time() - $this->_genesis));
			$this->logger->addMsg('Bytes Per Second threshold reached (currently ' . $this->_actualBPS . 'BPS, target: ' . $this->_targetBPS . 'BPS). Sleeping for ' . $rest . ' seconds.', 3);
			sleep($rest);

			// reset cyclesize so we don't far exceed bytes / second (a gap could possibly grow...?)
			$this->_cycleSize = 1;
		}
	}

	function speedup() {
		$this->logger->addMsg('MPS Throttle -> Speedup requested', 2);
		if ($this->_hesitatePeriod > 5) {  // max slow state, prioritize call
			$this->_hesitatePeriod = 2;
			$this->_cycleSize = 1;
			return 1; // hesitate mode
		}
		elseif ($this->_hesitatePeriod > 1) 
			$this->_hesitatePeriod = 1;
		
		if ($this->_cycleSize < 7) // don't exceed run cycles > 7
			$this->_cycleSize++;
		return 2; // run mode
	}

	function slowdown() {
		$this->logger->addMsg('MPS Throttle -> Slowdown requested', 2);
		if ($this->_cycleSize > 3) { // max fast state, prioritize call
			$this->_hesitatePeriod = 1;
			$this->_cycleSize = 2;
			return 2; // run mode
		}
		elseif ($this->_cycleSize > 1) 
			$this->_cycleSize = 1;

		if ($this->_hesitatePeriod == 7) { // don't exceed 7 second hesitate periods
			$this->pause();
			return 3; // evaluate mode
		}
		else
			$this->_hesitatePeriod++;
		return 1; // hesitate mode		
	}
	
	function pause() {
		$this->logger->addMsg('Throttle pausing for '.$this->_hesitatePeriod.' seconds',1);
		sleep($this->_hesitatePeriod);
	}

	// sets the mode, hP, and cS according to target mails per second
	function smartInit() {
		
		if ($this->_targetMPS < 0.15) {
			$this->_hesitatePeriod = 7;
			$this->pause();
			return 3;
		}
		if ($this->_targetMPS < 0.179) {
			$this->_hesitatePeriod = 6;
			$this->_cycleSize = 1;
			return 1;
		}
		if ($this->_targetMPS < 0.225) {
			$this->_hesitatePeriod = 5;
			return 1;
		}
		if ($this->_targetMPS < 0.3) {
			$this->_hesitatePeriod = 4;
			return 1;
		}
		if ($this->_targetMPS < 0.45) {
			$this->_hesitatePeriod = 3;
			return 1;
		}
		if ($this->_targetMPS < 0.745) {
			$this->_hesitatePeriod = 2;
			return 1;
		}
		if ($this->_targetMPS < 1.45) {
			$this->_hesitatePeriod = 2;
			$this->_cycleSize = 1;
			return 2;
		}
		if ($this->_targetMPS < 2.5) {
			return 2;
		}
		if ($this->_targetMPS < 3.5) {
			$this->_hesitatePeriod = 4;
			$this->_cycleSize = 2;
			return 2;
		}
		if ($this->_targetMPS < 4.5) {
			$this->_cycleSize = 2;
			return 2;
		} else { // 5 per second..
			$this->_cycleSize = 3;
			return 2;
		}
	}
}
?>
