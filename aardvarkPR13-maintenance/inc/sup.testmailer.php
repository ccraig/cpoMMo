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
	STARTUP ROUTINES
 *********************************/
// Tests the background Mail processor. Spawned via httpspawn. Write the time to cache directory

define('_IS_VALID', TRUE);
require ('../bootstrap.php');

$poMMo = & fireup('install');
$logger = & $poMMo->_logger;

// open file handle
if (!$handle = fopen(bm_workDir . '/test.php', 'w')) {
	die();
}
$port = (defined('bm_hostport')) ? bm_hostport : $_SERVER['SERVER_PORT'];
$fileContent = '<?php $testTime=' . time() . '; $respawnHost=' . bm_hostname . '; $respawnPort=' . $port . '; ?>';

// if this is the second attempt
if (isset ($_GET['respawn'])) {
	$fileContent .= '<?php $respawnAttempt=TRUE; ?>';
} else {
	$fileContent .= '<?php $respawnAttempt=FALSE; ?>';
	bmHttpSpawn(bm_baseUrl . 'inc/sup.testmailer.php?respawn=xxx');
}

// write to file
fwrite($handle, $fileContent);
fclose($handle);
?>