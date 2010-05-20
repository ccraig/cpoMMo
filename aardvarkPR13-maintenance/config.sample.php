<?php 
/**
 * poMMo Configuration File:
 *   This file sets up your database, language, and debugging options.
 *
 *   IMPORTANT: File must be named "config.php" and saved in the
 * 	"root" directory of your poMMo installation (where bootstrap.php is)
 */
// DO NOT REMOVE OR CHANGE THE BELOW LINE
defined('_IS_VALID') or die('Move along...');

/************************************************************************
 * ::: MySQL Database Information :::
 *   in order to use poMMo, you must have access to a valid MySQL database.
 *   Contact your webhost for details if you are unsure of its details.
************************************************************************/

// * Set your MySQL username
$bmdb['username'] = 'pommo';

// * Set your MySQL password
$bmdb['password'] = 'pommo';

// * Set your MySQL hostname ("localhost" if  your MySQL database is running on the webserver)
$bmdb['hostname'] = 'localhost';

// * Set the name of the MySQL database used by poMMo
$bmdb['database'] = 'pommo'; 

// * Set the table prefix  (change if you intend to have multiple poMMos running from the same database)
$bmdb['prefix'] = 'pommo_';


/************************************************************************
 * ::: Language Information :::
 *   Set this to your desired locale  -- this is a work in progress
 * 
 *	bg - Bulgarian					es - Spanish
 *	br - Brazilian Portugese		fr - French
 *	da - Danish						it - Italian
 *	de - German						nl - Dutch
 *	en - English						ro - Romanian
************************************************************************/
define('bm_lang','en');


/******************[ OPTIONAL CONFIGURATION ]*******************
 * (Below options intended for debugging and overriding 
 * automatic configuration)
*/

/************************************************************************
 * ::: Debugging Information :::
 *   Only modify these values if you'd like to provide information
 *   to the developers.
*/

// enable (on) or disable (off) debug mode. Set this to 'on' to provide debugging information
//  to the developers. Make sure to set it to 'off' when you are finished collecting information.
define('bm_debug','off');

// set the verbosity level of logging.
//  1: Debugging
//  2: Informational
//  3: Important (default)
define('bm_verbosity',3);

/************************************************************************
 * Uncomment (remove leading "//") and define the following 
 * settings to override default values.
 */


/************************************************************************
 * ::: Base URL :::
 * 
 * This is the path to pommo relative to the WEB.
 * For example, if poMMo is http://newsletter.mydomain.com/, the baseURL
 * would be '/'. If poMMo is http://www.mydomain.com/mysite/pommo, the
 * baseURL would be '/mysite/pommo/'
 * 
 * Default: Automatically Detected
 * NOTE: Include trailing slash
 */
//define('bm_baseUrl', '/mysite/newsletter');

/************************************************************************
 * ::: Cache Directory :::
 * 
 *   poMMo uses this directory to cache templates. By default, it
 *   is set to the "cache" directory in the poMMo root, and can
 *   safely be left blank or commented out (default).
 * 
 *   Make sure the webserver can write to this directory! poMMo
 *   will NOT WORK without being able to write to this directory.
 * 
 *   If you change its location, it is recommended to set it to a path
 *   outside the web root (for security reasons). 
 *  
 *   DO NOT USE A RELATIVE PATH, USE THE FULL SERVER PATH: e.g.
 *   '/home/b/brice/pommoCache'
 * 
*/
//define('bm_workDir','/path/to/pommoCache');

/************************************************************************
 * ::: Webserver Hostname :::
 * 
 * This is the hostname of the webserver running poMMo
 * 
 * Default: Automatically Detected
 */
//define('bm_hostname','www.mysite.com');
 
 /************************************************************************
 * ::: Webserver Port :::
 * 
 * This is the port number of the webserver running poMMo
 * 
 * Default: Automatically Detected [Usually 80, 8080, or 443]
 */
//define('bm_hostport','8080'); 
?>