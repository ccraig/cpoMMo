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

define('_IS_VALID', TRUE);
 
require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_subscribers.php');
require_once (bm_baseDir.'/inc/db_fields.php');
$poMMo =& fireup("secure");
$dbo = & $poMMo->_dbo;


if (empty($_GET['group_id']) || empty($_GET['table']))
	bmKill('Export failed - no group_id or table supplied.');
	
$group_id = str2db($_GET['group_id']);
$table = str2db($_GET['table']);


$fields = & dbGetFields($dbo);

if ($group_id == 'all')
	$subscribers = & dbGetSubscriber($dbo, $group_id, 'detailed',$table);
elseif (is_numeric($group_id)) {
	require_once (bm_baseDir.'/inc/db_sqlgen.php');
	$subscribers = & dbGetSubscriber($dbo, dbGetGroupSubscribers($dbo, $table, $group_id,'list'),'detailed', $table);
}
else
	bmKill('Bad group sent to export');

$encaser = "\"";
$delim = ", ";
$newline = "\n";
$empty = "NULL";

$csv_output = $encaser."email".$encaser.$delim;
foreach ( array_keys($fields) as $field_id ) {
  $csv_output .= $encaser.addslashes($fields[$field_id]['name']).$encaser.$delim;
}
$csv_output .= $encaser."date".$encaser.$newline;


foreach (array_keys($subscribers) as $subscriber_id) {
	$subscriber =& $subscribers[$subscriber_id];
	
	if (empty($subscriber['email']))
		$csv_output .= $empty.$delim;
	else
		$csv_output .= $encaser.$subscriber['email'].$encaser.$delim;
	foreach ( array_keys($fields) as $field_id) {
			if (empty($subscriber['data'][$field_id]))
				$csv_output .= $empty.$delim;
			else
				$csv_output .= $encaser.$subscriber['data'][$field_id].$encaser.$delim;
	}
	if (empty($subscriber['date']))
		$csv_output .= $empty.$delim;
	else
		$csv_output .= $encaser.$subscriber['date'].$encaser.$newline;
}

$size_in_bytes = strlen($csv_output);
header("Content-disposition:  attachment; filename=subscribers_" .
date("Y-m-d").".csv; size=$size_in_bytes");

print $csv_output;
exit;  
 ?>