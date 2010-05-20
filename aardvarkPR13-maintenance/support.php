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
define('_IS_VALID', TRUE);
define('_IS_SUPPORT', TRUE);

require ('bootstrap.php');
require (bm_baseDir . '/install/helper.install.php');

$poMMo = & fireup('install');
session_start();

$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

// allow access to this page if not installed 
if (bmIsInstalled() && !$_SESSION['pommo']['authenticated']) {
	bmKill(sprintf(_T('Denied access. You must %s logon %s to access this page...'), '<a href="' .
	bm_baseUrl . 'index.php?referer=' . $_SERVER['PHP_SELF'] . '">', '</a>'));
	die();
}

echo<<<EOF

<hr>
<div style="width: 100%; text-align: center;">
	poMMo support v0.02
	<hr>
</div>

<ul>
	<li><a href="support.php?cmd=clearWork">Clear Work Directory</a></li>
	<br>
	<li><a href="support.php?cmd=checkSpawn">Test Mailing Processor</a></li>
	<br>
	<li><a href="support.php?cmd=killMail">Terminate Mailing</a></li>
	<br>
	<li><a href="support.php?cmd=testTime">Test ability to set Maximum Exec Time</a></li>
</ul>
<hr>

<div style="width: 100%; text-align: center;">
	Status
	<hr>
</div>
EOF;

if (isset ($_GET['cmd'])) {
	switch ($_GET['cmd']) {
		case 'clearWork' :

			function delDir($dirName) {
				if (empty ($dirName)) {
					return true;
				}
				if (file_exists($dirName)) {
					$dir = dir($dirName);
					while ($file = $dir->read()) {
						if ($file != '.' && $file != '..') {
							if (is_dir($dirName . '/' . $file)) {
								delDir($dirName . '/' . $file);
							} else {
								@ unlink($dirName . '/' . $file) or die('File ' . $dirName . '/' . $file . ' couldn\'t be deleted!');
							}
						}
					}
					$dir->close();
					if ($dirName != bm_workDir)
						@ rmdir($dirName) or die('Folder ' . $dirName . ' couldn\'t be deleted!');
				} else {
					return false;
				}
				return true;
			}

			echo (delDir(bm_workDir)) ? 'Work Directory Cleared' : 'Unable to Clear Work Directory -- Does it exist?';

			break;
			
		case 'checkSpawn' :

			$port = (defined('bm_hostport')) ? bm_hostport : $_SERVER['SERVER_PORT'];
			echo 'Attempting to spawn initial background script (HOST: ' . bm_hostname . ' PORT: ' . $port . ')... please wait.<br><br>';
			ob_flush();
			flush();

			// call background script. Script writes time() as $testTime to workdir/test.php. Include file to compare.
			bmHttpSpawn(bm_baseUrl . 'inc/sup.testmailer.php?xxx=yyy');
			sleep(5);
			@ include (bm_workDir . '/test.php');

			if (isset ($testTime) && ((time() - $testTime) < 7)) {
				echo 'Initial Background Spawning SUCCESS<br><br>';
				if ($respawnAttempt) {
					echo 'Respawn Attempt (HOST: ' . $respawnHost . ' PORT: ' . $respawnPort . ')... SUCCESS';
				} else {
					echo 'Respawn Attempt (HOST: ' . $respawnHost . ' PORT: ' . $respawnPort . ')... FAILED';
					echo '<br>Log: ';
					foreach ($logger->getErr() as $msg)
						echo $msg . ' ';
				}
			} else {
				echo 'Initial Background Spawning FAILED, mailings will not process. Seek support.';
				echo '<br>Log: ';
				foreach ($logger->getErr() as $msg)
					echo $msg . ' ';
			}
			break;
			
		case 'killMail' :
					$sql = 'TRUNCATE TABLE '.$dbo->table['mailing_current'];
					$dbo->query($sql);
					$sql = 'TRUNCATE TABLE '.$dbo->table['queue'];
					$dbo->query($sql);
				echo 'Mailing Terminated';
			break;
			
		case 'testTime' :
			$maxRunTime = 110;
			echo 'Initial Run Time: '.ini_get('max_execution_time').' seconds <br>';
			if (ini_get('safe_mode')) {
				$maxRunTime = ini_get('max_execution_time') - 3;
				echo 'Safe mode is enabled<br>';
			}
			set_time_limit($maxRunTime +5);
			echo 'End Run Time: '.ini_get('max_execution_time').' seconds <br>';
	
		default :
			break;
	}
}

bmKill();