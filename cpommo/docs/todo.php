q
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
die();
?>

Recompile FR .mo - http://bugs.pommo.org/?do=details&id=205

Import; 
  + behaviors on encountering duplicate (update records?) / unsubscribed address (resubscribe?)
  + Read column names from first line (if applicable) -- in accordance with export.
  + smart parsing of email column [allow "Brice Burgess <b@iceburg.net>" ]
  
Add ability to make bulk changes to fields [ multi-subscriber edit ]

BOUNCE MANAGE; http://www.pommo.org/community/viewtopic.php?id=487
FLASH INTEGRATION; http://www.pommo.org/community/viewtopic.php?id=1085

http://www.pommo.org/community/viewtopic.php?id=62
http://www.pommo.org/community/viewtopic.php?pid=5031#p5031

[BEFORE Aardvark Final]
	+ Remove languages from default distribution, separate download. ? opt.
	+ Remove all unused JS/CSS/TPL/PHP/PNG/GIF/ETC Files
	+ Change Debugging [not included in $logger, but piped out @ end of template display || script], Offer more helpful info in support

[THEME]
	ENHACE DEFAULT SUBSCRIPTION FORM -- PLAIN TEXT IS ALWAYS AVAILABLE...
	ADD MESSAGE OUTPUT/DETECTION TO EVERY PAGE (logger messages -- esp. debugging stuff)
	Layout Fixes for IE -- see http://www.flickr.com/photos/26392873@N00/322986007/
	ELEMENTS with TITLE="??" : Title needs to be translated -- use SAME text as INNERHTML/LINK text
	SETUP SHORTCUT FOR "<img src="{$url.theme.shared}images/loader.gif" name="loading" class="hidden" title="{t}loading...{/t}" alt="{t}loading...{/t}" />"
	
	
[BRICE -- "Feel free to inherit any of these ;)" ]


	Cleanup/Unify Admin Mailings History and User Mailings List
	Rewrite groups_edit.php to use Datagrid Plugin for rule listing.
	http://www.pommo.org/community/viewtopic.php?pid=4305#p4305
	http://www.pommo.org/community/viewtopic.php?id=829
	http://www.pommo.org/community/viewtopic.php?id=439

	NOTES:
		MAKE BETTER USE OF PommoValidate::FUNCTIONS  (move more stuff to this file!)

	SHORT TERM: (PR16)
	=====================================
	
	  (API) Migrate to SwiftMailer
	  		- User Swift's Multi-Relay support
	  		- Rewrite PommoMailer() class (PR13 deprication!)
	  
	  (API) Enhance Subscriber Import
	  		- Protection against Timeouts
	  		- "smart" column assignments
	  		- Performance increases
	  		- Convert uploaded files to UTF-8
	  		- Configurable delimiter (e.g. allow ";")
	  		
	  	
	  (feature) Support SSL+TLS SMTP Connections
	  (feature) Add "isEmpty" group rule
	  (feature) Theme switcher in configure.
 			"the benefit of using your own theme to change a file or two is that you don't need to worry about overwriting your changes during an upgrade"
 
	
	MEDIUM TERM: (PR16 - maintenance)
	=====================================
 	  (API) Increase subscriber management performance. 
	  		- Merge sorting && limiting && ordering into sql.gen.php
	  		- Toggle pending status
	  		- Mail Pending Users a reminder
 	  
 	  (API) multi-byte safe -- see function overloading for common functions to replace
			- http://us3.php.net/manual/en/ref.mbstring.php
	  (API) Rewrite admin reset password request!  -- get rid of PommoPending::getBySubID()!!
	  (API) Abstract form errors into a common routine... build a forms validation class [much repetitive code ++ possibility of duplicate translations ]]
	  
	  (feature) Add specific emails to a group
	  		- Allow rules to include base subscriber data such as IP && date_registered.
	  (feature) Include "first page" which encourages "testing" and loading of sample data -- detect via maintenance routine.
	  (feature) Display & mail flagged subscribers
	  (feature) Version Detection, Alerting.
	  (feature) Maintenance - Shortcuts to purge unsubscribed/pending subscribers older than a set date (e.g. 3 months)
	  (feature) remove debug mode... everything should work off of verbosity level! set verbosity via config.php, or PER session
	  
	  (fix) Resolve character set issues (http://tinymce.moxiecode.com/punbb/viewtopic.php?pid=17351#p17351)	
	 
	  ADD Support Page (next to admin page in main menu bar)
			+ Enhanced support library
			+ PHPInfo()  (or specifically mysql, php, gettext, safemode, webserver, etc. versions)
			+ Database dump (allow selection of tables.. provide a dump of them)
			+ Link to README.HTML  +  local documentation
			+ Link to WIKI documentation
				+ Make a user-contributed open WIKI documentation system
				+ When support page is clicked, show specific support topics for that page

	  
	LONG TERM: (Release Candidates)
	=====================================
	
	  (feature) Bounced mail reading
	  (feature) Installer Rewrite, Prevention/Minimum Requirement Checks
	  		- MySQL V., PHP V., ERROR REPORTING, ERROR DISPLAY, SAFE MODE, TIMEOUT VALUE

	  (module) Visual Verrification / CAPTCHA @ subscribe form
	  (design) client side validation of subscribe form (use validation.js), potential AJAX processing
	  
	  (cleanup) vs. smarty->assign($_POST), just use the {$smarty.post} global in templates...
	
	  (API) include some kind of bandwith throttling / DOS detection / firewalling to drop pages from offending IPs / halt system for 3 mins if too many page requests ??
	  (API) Plugin architecture -- allow handler & manipulation injections/replacements to API functions
	  	+ Can be used to chain the subscription process (process.php) through custom functions, add an extended authentication layer, etc.
	  
	  Phase out pommo->init('keep') and data->set || -> get routines. Use States & auto clear states

	  FIREWALL/DOS PROTECTION: Mailer, only allow x amount of mails from an IP in y time.

	  Get rid of confirm.tpl, use JavaScript confirmation.

[CORINNA]

	SHORT TERM
		
		(API) 		get rid of appendURL problem!
					+ convert to $pommo->_state + save there
					
			====>	BB: Corinna, See the new state handling in subscribers_manage & mailings_history
			====>   BB: I've also added mailing composition to page states -- so user can bounce around program & not loose mailing data
			
		(arch)		module integration architecture
					how hook in the modules?
		
		
		(module) 	User Administration (3 tier achitecture)
		
		(module)	LDAP Support, ADS
		
		(module)	Bounce management would be cool, as module
					Filter incoming Mails, if there is a mailer-daemon replied to 1 of 
					our mails report it to the administrator
		
		
	MIDDLE TERM
	
		(UI)		Manual, FAQ, User Doku

	LONG TERM
	
  -----------------------------------------------
 	[DONE]

