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

++ jqModal warning of !firefox on first run 
write to jQ list w/ elegance of unobtrusive accordion load in config

http://www.pommo.org/community/viewtopic.php?id=292

http://www.pommo.org/community/viewtopic.php?id=288

PR15
----
+ message templates
+++ Set sockettimeout in spawn function


[LEGACY POMMO]
  Port config parser + config.php/sample

[BEFORE Aardvark Final]
	+ Message Templating
	+ Remove language from default distribution, seperate download.
	+ Remove all unused JS/CSS/TPL/PHP/PNG/GIF/ETC Files
	+ Rewrite validate.js for scoping by table row
	

[THEME]
	ENHACE DEFAULT SUBSCRIPTION FORM -- PLAIN TEXT IS ALWAYS AVAILABLE...
	ADD MESSAGE OUTPUT/DETECTION TO EVERY PAGE (logger messages -- esp. debugging stuff)
	Use TableSorter/Table layout for field, group, and group filter display
	Layout Fixes for IE -- see http://www.flickr.com/photos/26392873@N00/322986007/
	ReStripe rows on delete/tableSort
	ELEMENTS with TITLE="??" : Title needs to be translated -- use SAME text as INNERHTML/LINK text
	
[BRICE -- "Feel free to inherit any of these ;)" ]

	NOTES:
		MAKE BETTER USE OF PommoValidate::FUNCTIONS  (move more stuff to this file!)

	SHORT TERM: 
	  (API) Maintenance : clean out old/not utilized activations from subscriber_update
	  
	  (feature) add message templating
	  	
	  ADD Support Page (next to admin page in main menu bar)
		+ Enhanced support library
		+ PHPInfo()  (or specifically mysql, php, gettext, safemode, webserver, etc. versions)
		+ Database dump (allow selection of tables.. provide a dump of them)
		+ Link to README.HTML  +  local documentation
		+ Link to WIKI documentation
			+ Make a user-contributed open WIKI documentation system
			+ When support page is clicked, show specific support topics for that page
			
	  Importer:
		+ Optimize
		+ Convert uploaded files to UTF-8
		+ Protection against timeouts, status?
	
	MEDIUM TERM: (PR16)
	
	  (API) SWITCH "phase1" dialogs of subscriber add/delete/search/export to INLINE DISPLAY vs. AJAX POLL 
 		 + Requires unobtrusive modal window (thickbox destroys event bindings). Keep eye on Gavin's plugin
	  (API) Rewrite PommoMailer()  [ currently depricated with PR13 functionality ]
	  (API) Rewrite PommoThrottler() [ currently depricated with PR13 functionality ]
	  (API) Better Organize inc/helpers/messages & validate... underutilized!
	  (API) - override PHPMailers error handling to use logger -- see extending PHPMailer Example @ website
	  (API) - Rewrite admin reset password request!  -- get rid of PommoPending::getBySubID()!!
	  
	  (feature) Add 'comment' type to subscriber field which outputs a text area configured to certain # of chars & whose styling is handled via theme template
	  (feature) Add specific emails to a group
	  	++ Allow rules to include base subscriber data such as IP && date_registered.
	  	
	  (feature) Include "first page" which encourages "testing" and loading of sample data -- detect via maintenance routine.
	  (feature) Display flagged subscribers...
	  (feature) Support SSL+TLS SMTP Connections
	  
	  PR16 -- hopefully have all strings in program, notify translators, ask for review of contributors section.
	  
	  
	  (feature) Add "isEmpty" group rule
	  
	  Make groups_edit.php (rule creation page) more responsive.
	 	++ vs. page reloads, add ajax rule CRUD & with an oncomplete which refreshes
	 		group filter count + tally.	
	 	++ Integrate taconite plugin to do the updates for groups_edit.php, 
	 	  ecentually spread to mailings_status.php
	 	++ ajax rewritted (see above)
	 	
	 (API) Increase subscriber management performance. 
	 	++ Merge sorting && limiting && ordering into sql.gen.php 
	 	  
	  
	LONG TERM:
	
	  (fix) Multiple SMTP servers -- appears to alternate.. queue does not appear to be processing relays simultaneously
	  
	  (feature) Bounced mail reading
	  (feature) Add search capability to subscriber management
	  (feature) Add theme selector
	  
	  (module) Visual Verrification / CAPTCHA @ subscribe form
	  (design) client side validation of subscribe form (use validation.js), potential AJAX processing
	
	  (API) include some kind of bandwith throttling / DOS detection / firewalling to drop pages from offending IPs / halt system for 3 mins if too many page requests ??
	  (API) Plugin architecture -- allow handler & manipulation injections/replacements to API functions
	  	+ Can be used to chain the subscription process (process.php) through custom functions, add an extended authentication layer, etc.
	  	

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

