<?php
/**
 * Copyright (C) 2005, 2006, 2007  Brice Burgess <bhb@iceburg.net>
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

[LEGACY POMMO]
  Port config parser + config.php/sample

[BEFORE Aardvark Final]
	+ Remove language from default distribution, seperate download. ? opt.
	+ Remove all unused JS/CSS/TPL/PHP/PNG/GIF/ETC Files
	+ Rewrite validate.js for scoping by table row

[THEME]
	ENHACE DEFAULT SUBSCRIPTION FORM -- PLAIN TEXT IS ALWAYS AVAILABLE...
	ADD MESSAGE OUTPUT/DETECTION TO EVERY PAGE (logger messages -- esp. debugging stuff)
	Layout Fixes for IE -- see http://www.flickr.com/photos/26392873@N00/322986007/
	ReStripe rows on delete/tableSort
	ELEMENTS with TITLE="??" : Title needs to be translated -- use SAME text as INNERHTML/LINK text
	
[BRICE -- "Feel free to inherit any of these ;)" ]

	NOTES:
		MAKE BETTER USE OF PommoValidate::FUNCTIONS  (move more stuff to this file!)

	SHORT TERM: (PR16)
	=====================================
	
	  (API) Migrate to SwiftMailer
	  		- User Swift's Multi-Relay support
	  		- Rewrite PommoMailer() class (PR13 deprication!)
	  		
	  (API) Increase subscriber management performance. 
	  		- Merge sorting && limiting && ordering into sql.gen.php
	  		- Rewrite Editing
	  		- Add "quick delete"
	  		- Toggle pending status
	  		- Mail Pending Users a reminder
	  		- Add Search capability
	  
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
 
	  (fix) multiple choice multiple options in rule editing in IE  ** needs jQuery Patch
	 
	
	MEDIUM TERM: (PR16 - maintenance)
	=====================================
 	  (API) multi-byte safe -- see function overloading for common functions to replace
			- http://us3.php.net/manual/en/ref.mbstring.php
	  (API) Rewrite admin reset password request!  -- get rid of PommoPending::getBySubID()!!
	  
	  (feature) Add specific emails to a group
	  		- Allow rules to include base subscriber data such as IP && date_registered.
	  (feature) Include "first page" which encourages "testing" and loading of sample data -- detect via maintenance routine.
	  (feature) Display & mail flagged subscribers
	  (feature) Version Detection, Alerting.
	  
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
	
	  (cleanup) SWITCH "phase1" dialogs of subscriber add/delete/search/export to INLINE DISPLAY vs. AJAX POLL 
 			+ Requires unobtrusive modal window (thickbox destroys event bindings). Keep eye on Gavin's plugin
 			+  scratch Gavin's -- use jQmodal ! (mine)
 		 
	  (API) include some kind of bandwith throttling / DOS detection / firewalling to drop pages from offending IPs / halt system for 3 mins if too many page requests ??
	  (API) Plugin architecture -- allow handler & manipulation injections/replacements to API functions
	  	+ Can be used to chain the subscription process (process.php) through custom functions, add an extended authentication layer, etc.
	  
	  Phase out pommo->init('keep') and data->set || -> get routines. Use States & auto clear states

	  FIREWALL/DOS PROTECTION: Mailer, only allow x amount of mails from an IP in y time.


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

