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
 
/** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');
?>

FINISH MERGING IN NEW FRAMEWORK.

Forum quote; Looks like SSL,TLS for SMTP connection not supported. Gmail and etc. only accept SMTP connection this way.

PROCESS.PHP -- Include safe (compare bmReferer/SERVER['HTTP_REFERER'] to bm_http) autodetection

CLEAR SUBSCRIBER LIST (PR14)

When switching language in config.php to "de" I get the following message (same as before in PR12): "*Fatal error*: Call to undefined function: mb_detect_encoding() in */mnt/be2/08/459/00000014/htdocs/pommo13/inc/gettext/gettext.inc* on line *99". 

SECURITY ISSUE W/ SESSIONS -- e.g. If you login to demo & then acess pommo elsewhere on same domain -- you bypass login.

I'd like to include the toggling of notifications -- in which you can toggle + assign an email address on a) new subscriptions b) subscription updates c) unsubscriptions & d) newsletter sent.

merge subscribers, pending ... add status field  active, inactive, pending

Just making sure.  In PR13 the bolding of non-required fields has not been fixed, correct?  I too am encountering this bug and just wanted to make sure it wasn't me.  Thanks for the hard work.  Looking forward to future releases.

I *am* thinking about incorporating a sent/unsent list that can be downloaded at any time during the processing of a mailing.
   +++ Allow ability to manually add members to a list in bulk
   
** SANITY CHECKS**
  gettext support
  mb_detect_encoding() / MB support
  temporary_tables
  safe mode


[BEFORE 1.0]
	+ Rewritten Import
	+ Rewritten Subscriber Manage
	+ Message Templating
	+ Rewritten Default Theme (CSS, JS, TEMPLATES)
		
  
[BRICE]

	IMMEDIATE (FOR NEXT RELEASE):
		 (feature) Enhanced subscriber management
		 (feature) Add search capability to subscriber management
		 (feature) add ability to send messages to list administrator upon successfull subscription/unsubscription/changes
	
		 (feature) Store subscribers IP address on subscription
	
	SHORT TERM:
	
	
	  (API) - secure "included" files under cache -- don't include them.. rather run them through specialized parser? e.g. for embed.forms & httpSpawn tester
	  (API) - override PHPMailers error handling to use logger -- see extending PHPMailer Example @ website
	  (API) Better mailing send debugging ->
	    Change queue table to include "status" field --> ie. ENUM ('unsent','sent','failed') + error catching... (including PHP fatal errors) 
	  (API) Merge validator's is_email rules with lib.txt.php's isEmail
	  (API) Add validation schemes to subscription form (process.php)
	  (API) when inserting into subscribers_flagged, watch for duplicate keys (either add IGNORE or explicity check for flag_type...)
	  (API) Allow fetching of field id, names, + types -- NOT OPTIONS, etc... too much data being passed around manage/groups/import/etc.
	  
	  (feature) Add ability to view emails in queue (from mailing status)
	  (feature) add message templating
	  (feature) Add Date + Numeric types  [[[{html_select_date}]]]
	  (feature) Display flagged subscribers...
	  
	
	MEDIUM TERM:
	
	  (API) Get rid of pending table. Add pending flag to subscribers, as well as "code" & action...
			+ Enforce non duplicate subscribers on the DB level!
	  (API) Seperate lang files for "admin" & "user" directories --> total of 3: user, admin, install ??
	  
	  (API) REGEX group filtering
	  
	  (API) Use smartyvalidator + custom validation rules for subscription/subscriber update forms!
	     + get rid of isEmail()?
	     
	  (API) Merge current/history mailing table.
			
	  (feature) Add OR to group filtering
	  (feature) Enhanced subscriber import
	  (feature) Add 'comment' type to subscriber field which outputs a text area configured to certain # of chars & whose styling is handled via theme template
	  
	  (security) Implement Passwords on user information (login.php). Include customizable question/answer pair.
	
	  
	LONG TERM:
	
	  (API) include some kind of bandwith throttling / DOS detection / firewalling to drop pages from offending IPs / halt system for 3 mins if too many page requests ??
	  
	  (API) create embeddable friendly namespace/objects - published API (externally accessible)
	    	+ work on Wordpress, gallery, OsCommerce/ZenCart Modules
	    	
	
	  (design) New default theme
	  (design) New Installer & Upgrade script - Realtime flushing of output.
	  (design) AJAX forms processing
	  
	  (module) Visual Verrification / CAPTCHA @ subscribe form
	  
	  (feature) Allow seperate language selection for admin and user section. Include "auto" language selection based from client browser
	  (feature) Bounced mail reading
  

		 -----
	  
	  UNCAT:
	
	  With template, add predefined support. E.G. ++ADD UNSUBSCRIBE LINK++ SEE; http://www.iceburg.net/pommo/community/viewtopic.php?id=108
	  
	  4. do not forget to put a blank index in all the directories that do not have an index of their own
	  
	  Personally I would also like to see a "chain" in place for the unsubscribe, eg. it calls the unsuscribe as it does now but then continies onto another php file, by default empty. But this would allow users (admins) to implement any further processing that they wanted to do, i would imagine that the persons email address should be "posted" to this php chainer. This could make the integration of this to any other installation of anything else, eg site registration removal, so much easier.
	  

[CORINNA]

		(feature)	add + refactor http://www.phpinsider.com/php/code/SafeSQL/
			 	-> all but the Strings with escaped ''.
			 	
			 	$whereStr = ' WHERE group_id=\''.$where.'\'';
			 	[..]
			 	$safesql =& new SafeSQL_MySQL;
				$sql = $safesql->query("SELECT group_id, group_name FROM %s %s ORDER BY group_name",
					array($dbo->table['groups'], $whereStr) );
					
				Brice, what do you want for standard? SafeSQL wants "QUERY STRING in double QUOTES and the parameters in 'this quotes'"
				Also should i use always %s oder %i for the ids? Because you used 'id', but i think numbers can be without ''
				Can i convert all to this format? $stringvar = "abc'de'fgh"
				See his README in inc/safesql
				
				
		DB Scheme for Mailings current/history(ideas?) -- 
				* Eventually I think they should be merged into one table as we discussed. 
				At this time, lets focus elsewhere as there are bigger fish to fry ;). Mark this as long/medium term? 

				-> OK!
				-> You requested this in the poMMo forum if the execution times will be longer..
				With little data it is no problem and when one has a lot of data he can always 
				make a index on them (Indices).


	SHORT TERM
		
		(API) 		get rid of appendURL problem!
					+ convert to $poMMo->_state + save there
	
		(feature)	alter database design -> merge tables mailings &mailings_history and refactor
					EDIT: after finishing mailing ... database entry in mailing_current would not 
					switch to mailing_history
	
		(arch)		module integration architecture
					how hook in the modules?
		
		
		(module) 	User Administration (3 tier achitecture)
		
		(module)	LDAP Support, ADS
		
		(module)	Bounce management would be cool, as module
					Filter incoming Mails, if there is a mailer-daemon replied to 1 of 
					our mails report it to the administrator
		
		(feature)	Numeric types/sets for Demographics
		
	MIDDLE TERM
	
		(UI)		Manual, FAQ, User Doku
	
	

	LONG TERM



  -----------------------------------------------
 	[DONE]


 
  	(API) - Fix pager class. See Corinna's comments @ admin/mailings/mailings_history.php + 
	// This seems to not handle the case, that when we are on the last page of multiple pages,
	// and then choose to increase the diplay number then the start value is too great
	// eg. limit=5, 3 pages, go to page 3 -> then choose limit=10 
	// -> no mailings found because of start = 20 
	// its doing right, but less user friendly it it says no mailing, but its only that there are no mailings in this range
	// $pagelist : echo to print page navigation. -- 
	// TODO: adding appendURL to every link gets VERY LONG!!! come up w/ new plan!
	-> i started from the beginning in the case of $start geater then number of mails -> simple :/

