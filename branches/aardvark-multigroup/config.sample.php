<?php die(); /* DO NOT REMOVE THIS LINE! SERIOUS SECURITY RISK IF REMOVED! */ ?>
====================================================================
Welcome to the poMMo Configuration File! From here you setup your database and
  other preferences.
  
IMPORTANT: This file must be named "config.php" and saved in the "root"
  directory of your poMMo installation (where bootstrap.php is).
  
See config.simple.sample.php for a condensed, readable config file.
====================================================================

::: MySQL Database Information :::

Set the MySQL database poMMo should use

	[db_database] = "pommo"
	
Set the MySQL username **

	[db_username] = "pommo"

Set the MySQL password

	[db_password] = "pommo"
	
Set the MySQL hostname (usually "localhost"). 
	NOTE; Remote MySQL servers (e.g. mysql.yourwebhost.com) can be used.

	[db_hostname] = "localhost"
	
Set the table prefix 
  (change if you intend to have multiple poMMos running from the same database)
  
	[db_prefix] = pommo_
	
 	
::: Language Information :::
Set this to your desired locale. Current languages available are;
 bg - Bulgarian				es - Spanish
 pt - Brazilian Portugese	fr - French
 da - Danish				it - Italian
 de - German				nl - Dutch
 en - English				ro - Romanian
 en-uk - British			ru - Russian
 
	[lang] = en
	

::: Optional Configuration :::
====================================================================
Below Options are intended for debugging or overriding 
automatic configuration.
====================================================================

Set debug mode. Enable (on) or disable(off). Debug mode is useful for providing
  information to developers
  
	[debug] = off

Set the verbosity level of logging (1: Debugging 2: Informational 3: Important[default])

	[verbosity] = 3


::: Overrides :::
  Uncomment (remove the leading "**") to define the following settings.
  NOTE: These settings are auto-detected by default, and best left unchanged.
  
Set the Base URL.
  This is the path to poMMo relative to the WEB. Below are examples with value;
  (poMMo location)							(baseURL value)
  http://newsletter.mysite.com/				/
  http://www.mysite.com/me/pommo			/me/pommo/
  
  NOTE: Include trailing slash

	** [baseURL] = "/mysite/newsletter/"

Set the "working" Cache Directory
  poMMo uses this directory to cache templates. By default, it
  is set to the "cache" directory in the poMMo root, and can
  safely be left blank or commented out (default).
  
  Make sure the webserver can write to this directory! poMMo
  will NOT WORK without being able to write to this directory.
 
  If you change its location, it is recommended to set it to a path
  outside the web root (for security reasons). 
  
  DO NOT USE A RELATIVE PATH, USE THE FULL SERVER PATH: e.g.
  '/home/b/brice/pommoCache'

	** [workDir] = "/path/to/pommoCache"


Set the webserver hostname
  Default: Automatically Detected
 
	** [hostname] = www.mysite.com
	

Set the webserver port
  Default: Automatically Detected [Usually 80, 443, or 8080]	
	
	** [hostport] = 8080