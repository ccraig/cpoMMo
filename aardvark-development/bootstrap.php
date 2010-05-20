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

// Start Output buffering
ob_start();

// while poMMo is in development state, we'll attempt to display PHP notices, warnings, errors
ini_set('display_errors', '1');

// error_reporting(E_ALL); // [DEVELOPMENT]
error_reporting(E_ALL ^ E_NOTICE); // [RELEASE] 

// Include core components
require(dirname(__FILE__) . '/inc/helpers/common.php'); // base helper functions
require(dirname(__FILE__) . '/inc/classes/api.php'); // base API
require(dirname(__FILE__) . '/inc/classes/pommo.php'); // base object

// Setup the core global. All utility is tucked away within this global to reduce namespace
// pollution and possible collissions when poMMo is embedded in another application.
$GLOBALS['pommo'] = new Pommo(dirname(__FILE__) . '/');

/*
 * Disable session.use_trans_sid to mitigate performance-penalty
 * (do it before any output is started) [from gallery2]
 */
if (!defined('SID')) {
    @ini_set('session.use_trans_sid', 0);
}

// soft turn off magic quotes -- NOTE; this may break embedded scripts?
// clean user input of slashes added by magic quotes. TODO; optimize this.
if (get_magic_quotes_gpc()) { 
	$_REQUEST = PommoHelper::slashStrip($_REQUEST); 
	$_GET = PommoHelper::slashStrip($_GET); 
	$_POST = PommoHelper::slashStrip($_POST); 
}

// disable escaping from DB
set_magic_quotes_runtime(0);

// Assign alias to the core global which can be used by the script calling bootstrap.php
$pommo =& $GLOBALS['pommo'];
$pommo->preinit();
?>