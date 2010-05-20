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
require('../bootstrap.php');
$pommo->init(array('authLevel' => 1));

$install = TRUE;

if ($install) {
	
	$prefix = "pommo_";		// Change this to $pommo->tables->...
	$pluginprefix = "pommomod_";
	$dbstr = "ENGINE = innodb ";
	$charset = "DEFAULT CHARSET = latin1 ";
	
	
	echo "<html><body style='font-size: 0.5em; font-family: Courier, Courier New, sans-serif; color:black; '><div style='white-space:nowrap;'>";
	
	$link = $pommo->_link;
	$safesql = & $pommo->_dbo->_safeSQL; 	//$safesql =& new SafeSQL_MySQL;
	
	/*	$link = mysql_connect($host, $user, $pass)
			or die("No DB connection: " . mysql_error());  echo "Database connection successful.<br>";
	mysql_select_db($database) 
			or die("Database selection failed.<br>"); 	*/
	
	
	
	$sqltab[] = $safesql->query("DROP TABLE IF EXISTS `%sbounce` , `%smailingqueue`, " .
			"`%slist` , `%slist_rp` , `%srp_group`, `%sresponsibleperson` , `%suser`, `%spermgroup`, `%spermission`, `%spg_perm`, " .
			"`%splugin`, `%splugincategory` , `%splugindata`  ", 
			array($pluginprefix,$pluginprefix,$pluginprefix,$pluginprefix,$pluginprefix,
				  $pluginprefix,$pluginprefix,$pluginprefix,$pluginprefix,$pluginprefix,
				  $pluginprefix,$pluginprefix,$pluginprefix) );


		/****************************** CREATE TABLES *********************************/  
		//SMALLINT UNSIGNED  0 - 65535                         (SIGNED 	-32768 	32767)
  	  	//ON DELETE / ON UPDATE CASCADE  FOREIGN KEY
		//PRIMARY KEY ( `group_id` )


		$sqltab[] = $safesql->query("CREATE TABLE `%splugincategory` ( " .
							"`cat_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`cat_name` VARCHAR( 75 ) NOT NULL UNIQUE, " .
							"`cat_desc` VARCHAR( 250 ) NOT NULL, " .
							"`cat_active` BOOL NOT NULL  DEFAULT '0' " .
							") %s %s; ",
						array($pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%splugin` ( " .
							"`plugin_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " . 
							"`plugin_uniquename` VARCHAR( 75 ) NOT NULL, " . 	//UNIQUE
							"`plugin_name` VARCHAR( 100 ) NOT NULL , " . 		//TEXT?
							"`plugin_desc` VARCHAR( 250 ) NOT NULL , " . 
							"`plugin_active` BOOL NOT NULL  DEFAULT '0' , " . 
							"`plugin_version` VARCHAR( 10 ) NOT NULL DEFAULT '0', " .  
							"`cat_id` SMALLINT UNSIGNED NOT NULL REFERENCES %splugincategory(cat_id) " . 
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%splugindata` ( " .
							"`data_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`data_name` VARCHAR( 75 ) NOT NULL UNIQUE, " .
							"`data_value` VARCHAR( 150 ) NOT NULL, " .
							"`data_type` ENUM( 'TXT', 'NUM', 'BOOL' ) NOT NULL DEFAULT 'TXT', " . 
							"`data_desc`  VARCHAR( 250 ) NOT NULL, " . 
							"`plugin_id` SMALLINT UNSIGNED NOT NULL REFERENCES %splugin(plugin_id) " . 
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $dbstr, $charset) );


		// like mailtable
		$sqltab[] = $safesql->query("CREATE TABLE `%smailingqueue` ( " . 
							"`qid` SMALLINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT , " . 
							"`fromname` varchar( 60 ) NOT NULL default '', " . 
							"`fromemail` varchar( 60 ) NOT NULL default '', " . 
							"`frombounce` varchar( 60 ) NOT NULL default '', " . 
							"`subject` varchar( 60 ) NOT NULL default '', " . 
							"`body` mediumtext NOT NULL , " . 
							"`altbody` mediumtext, " . 
							"`ishtml` enum( 'on', 'off' ) NOT NULL default 'off', " . 
							"`mailgroup` varchar( 60 ) NOT NULL default 'Unknown', " . 
							"`date` datetime default NULL , " . 
							"`sent` int( 10 ) unsigned NOT NULL default '0', " . 
							"`notices` longtext, " . 
							"`charset` varchar( 15 ) NOT NULL default 'UTF-8' " . 
							") %s %s; ",
						array($pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%sbounce` ( " .
							"`bounce_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`bounce_email_bounced` VARCHAR( 100 ) NOT NULL , " .
							"`bounce_mail` MEDIUMTEXT NOT NULL , " .		//siehe altbody
							"`bounce_reason` VARCHAR( 200 ) NOT NULL , " .
							"`subscriber_id` SMALLINT UNSIGNED NOT NULL REFERENCES %ssubscribers(subscribers_id) " .
							") %s %s; ",
						array($pluginprefix, $bmdb['prefix'], $dbstr, $charset) );


		// SHOULD BE NAMED PERMGROUP
		// little bit messy
		$sqltab[] = $safesql->query("CREATE TABLE `%spermgroup` ( " .
							"`permgroup_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`permgroup_name` VARCHAR( 75 ) NOT NULL UNIQUE, " .
							"`permgroup_desc` VARCHAR( 250 ) NOT NULL  " .
							") %s %s; ",
						array($pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%spermission` ( " .
							"`perm_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`perm_name` VARCHAR( 75 ) NOT NULL UNIQUE " .
							") %s %s; ",
						array($pluginprefix, $dbstr, $charset) );


		$sqltab[] = $safesql->query("CREATE TABLE `%suser` ( " .
							"`user_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`user_name` VARCHAR( 75 ) NOT NULL UNIQUE , " .
							"`user_pass` VARCHAR( 150 ) NOT NULL , " .										//MD5
							"`user_created` DATETIME NOT NULL , " .
							"`user_lastlogin` DATETIME NOT NULL , " .
							"`user_logintries` SMALLINT UNSIGNED NOT NULL, " .					//AUTO_INCREMENT
							"`user_lastedit` DATETIME NOT NULL , " .
							"`user_permissionlvl` TINYINT NOT NULL DEFAULT '1', " .
							"`user_active` BOOL NOT NULL  DEFAULT '0' , " .
							"`user_authtype` enum( 'POMMODB', 'LDAP', 'QLDAP', 'HTACCESS', 'EXDB') NOT NULL  DEFAULT 'POMMODB' , " .
							"`permgroup_id` SMALLINT UNSIGNED NULL REFERENCES %spermgroup(permgroup_id) " .
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%sresponsibleperson` ( " .
							"`user_id` SMALLINT UNSIGNED NOT NULL REFERENCES %suser(user_id) , " .
							"`rp_realname` VARCHAR( 150 ) NOT NULL UNIQUE, " .
							"`rp_bounceemail` VARCHAR( 100 ) NOT NULL , " .
							"`rp_sonst` VARCHAR( 250 ) NOT NULL  " .
							//"`bounce_id` SMALLINT UNSIGNED NULL REFERENCES %sbounce(bounce_id) " . 
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $dbstr, $charset) );


		$sqltab[] = $safesql->query("CREATE TABLE `%slist` ( " .
							"`list_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`list_name` VARCHAR( 150 ) NOT NULL , " .
							"`list_senderinfo` VARCHAR( 150 ) NOT NULL , " .
							"`list_desc` VARCHAR( 250 ) NOT NULL , " .
							"`list_created` DATETIME NOT NULL , " .
							"`list_sentmailings` INT UNSIGNED NOT NULL  , " .	//AUTO_INCREMENT
							"`list_active` BOOL NOT NULL  DEFAULT '0'  " .
							/* list_mailing ist auch n:m. Mailings mit listen verknüpfen  */
							") %s %s; ",
						array($pluginprefix, $dbstr, $charset) );


		/* n:m Relations */
		
		$sqltab[] = $safesql->query("CREATE TABLE `%spg_perm` ( " .
							"`permgroup_id` SMALLINT UNSIGNED NOT NULL REFERENCES %spermgroup(permgroup_id) , " .
							"`perm_id` SMALLINT UNSIGNED NOT NULL REFERENCES %spermission(perm_id) , " .
							"`pgp_grant` BOOL NOT NULL DEFAULT FALSE  " .
							/* sonstige daten wie zuteilungsdatum oder so??*/
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $pluginprefix, $dbstr, $charset) );
		
		$sqltab[] = $safesql->query("CREATE TABLE `%slist_rp` ( " .
							"`list_id` SMALLINT UNSIGNED NOT NULL REFERENCES %slist(list_id) , " .
							"`user_id` SMALLINT UNSIGNED NOT NULL REFERENCES %sresponsibleperson(user_id)  " .
							/* sonstige daten wie zuteilungsdatum oder so??*/
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%srp_group` ( " .	//group is a pommo-table
							"`user_id` SMALLINT UNSIGNED NOT NULL REFERENCES %sresponsibleperson(user_id) , " .
							"`group_id` SMALLINT UNSIGNED NOT NULL REFERENCES %sgroups(group_id) " .			
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $bmdb['prefix'], $dbstr, $charset) );


		
		//---Install Tables---
		for ($i = 0; $i < count($sqltab); $i++) {
			$result = mysql_query($sqltab[$i]) or die("Query failed: <b style='color: red'>" . mysql_error() . 
															"<br> --->Statement" . $sqltab[$i] . "</b><br>");
				echo "{$sqltab[$i]} "; echo "<br> --->"; echo "<b>". $result. "</b><br>";
		}





		/***********************  INSERT DATA ***************************/

		/* bounce nix */
		/* mailing queue nix */
	
		/* plugincategory */
		$sql[] = $safesql->query("INSERT INTO `%splugincategory` (cat_name, cat_active, cat_desc) " .
									"VALUES ('auth', '0', 'Multiuser category. If you plan to install multiuser support with pommo, here are the tools+setup for it. Authenticate users with varios methods. Install here what methods to use') ", 
						array($pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO `%splugincategory` (cat_name, cat_active, cat_desc) " .
									"VALUES ('utils', '0', 'Useful tools for Klickverfolgung, mailing queue and so on') ", 
						array($pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO `%splugincategory` (cat_name, cat_active, cat_desc) " .
									"VALUES ('bounce', '0', 'bounce mail setup, various bounce methods') ", 
						array($pluginprefix) );	
		
		/* plugins */
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('simpleldapauth', 'Simple LDAP Authentication', 'Authenticate users with a ldap bind to a ldap/ads server in your network. No need to know further user/pass details or query details.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='auth' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('queryldapauth', 'LDAP Auth with Query', 'Authenticate users with a ldap query on a ldap/ads server. You need to know further user/pass details or query details.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='auth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('dbauth', 'Database Authentication', 'Authenticate users on database data. The Administrator can add, delete set permissions for users. To use this activate User management!', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='auth' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('useradmin', 'User Administration Plugin', 'Thius makes sense in combination with a authentication method (Usually:dbauth)', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='auth' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('bouncepop', 'Bounce Mail Handler', 'Redirect bounced / unzustellbare Mails zu responsible Persons with POP oder with a web interface. for this you need a mailbox where the bounces are stored.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='bounce' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('bouncehandler', 'Bounce Mail Handler', 'Redirect bounced / unzustellbare Mails zu responsible Persons with POP oder with a web interface. for this you need a mailbox where the bounces are stored.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='bounce' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('mailingqueue', ' Mail Queue', 'Store Sendings in the database for later sending.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='utils' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('clickstats', 'Mouse Click Statistics', 'Look how many of your mailings are viewed or: how many oif the links in the mail are followed.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='utils' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );	

		/* plugindata */
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('simpleldap_server', 'ldaps://domcon.ict.tuwien.ac.at/', 'The server where the LDAP bind is directed.', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='simpleldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('simpleldap_port', '636', 'The port of the server for LDAP bind (Usually 636).', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='simpleldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('simpleldap_dn', '@ICT.TUWIEN.AC.AT', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='simpleldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	

		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_server', 'ldaps://domcon.ict.tuwien.ac.at/', 'Server blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_port', '636', 'Port... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_base', 'dn=ICT,dn=TUWIEN,dn=AC,dn=AT', ' base ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_dn', '@ICT.TUWIEN.AC.AT', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_user', 'myuser', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_pass', 'mypassw', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	

		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('dbauth_writeldapusertodb', 'TRUE', 'The server where the LDAP bind is directed.', " .
				"'BOOL', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='dbauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );
		
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('useradmin_maxuser', '20', 'Maximal users in DB!.', " .
				"'NUM', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='useradmin' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );
		
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('bouncepop_server', 'mail.gmx.net', '.', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='bouncepop' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('bouncepop_port', '636', 'The port of the server for POPPING (Usually 636).', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='bouncepop' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('bouncepop_user', 'corinna-pommo@gmx.net', '.', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='bouncepop' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('bouncepop_pass', 'A6Q00VAAS', '.', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='bouncepop' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );

								/* zu entscheiden */				
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('mailqueue_opt', 'blah', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='mailingqueue' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('clickstats_opt', 'blah', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='clickstats' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	



		/************** OTHER READY MADE TEST DATA ************************/
$sql[] = $safesql->query("INSERT INTO %slist (list_id, list_name, list_senderinfo, list_desc, list_created, 
		list_sentmailings, list_active) VALUES 
		(1, 'Elektrotechniker Mailingsliste', 'info@eletriker.com', 'Die Eletronischen Elektriker', '2006-12-13 17:46:40', 3, 1),
		(2, 'Informatiker Mailingsliste', 'info@profipc.it', 'Die Computerprofis', '2006-12-12 17:47:25', 0, 0),
		(3, 'Mathematiker Mailingliste', 'maths@mats.com', 'Die wahnsinnigen kommen', '2006-12-13 18:05:37', 3, 1),
		(4, 'Mathematiker Mailingliste', 'maths@mats.com', 'Die wahnsinnigen kommen', '2006-12-13 18:05:37', 3, 1),
		(5, 'neich', 'Ab. S. Ender', 'blah', '2007-01-12 18:26:00', 0, 1),
		(6, 'neich', 'Ab. S. Ender', 'blah', '2007-01-12 18:41:33', 0, 1); ", array($pluginprefix) );


$sql[] = $safesql->query("INSERT INTO %slist_rp (list_id, user_id) VALUES (1, 1), (2, 1), (3, 2); ", array($pluginprefix) );


//permission data
//TODO ADD the right things! 'send, compose, maillists, history, bounce, useradmin, subscribers, groups' 'send, compose',

$sql[] = $safesql->query("INSERT INTO %spermgroup (permgroup_id, permgroup_name, permgroup_desc) 
		VALUES (1, 'Admin', 'Admingroup can do all'),
				(2, 'Senders', 'This group can only send'); ", array($pluginprefix) );

$sql[] = $safesql->query("INSERT INTO %spermission (perm_id, perm_name) 
		VALUES (1, 'SEND'), (2, 'COMPOSE'), (3, 'PLUGINSETUP'), (4, 'PLUGINADMIN'), (5, 'HISTORY'); ", array($pluginprefix) );

$sql[] = $safesql->query("INSERT INTO %spg_perm (permgroup_id, perm_id, pgp_grant) 
		VALUES (1, 1, FALSE),  (1, 2, FALSE), (1, 3, FALSE), (1, 4, FALSE), (1, 5, FALSE), (2, 1, FALSE), (2, 2, FALSE), (2, 5, FALSE); ", array($pluginprefix) );
/*OLD
 * $sql[] = $safesql->query("INSERT INTO %sperm (perm_id, perm_name, perm_perm, perm_desc) 
		VALUES (1, 'Admin', 'send, compose, maillists, history, bounce, useradmin, subscribers, groups', 'Admingroup can do all'),
				(2, 'Senders', 'send, compose', 'This group can only send'); ", array($pluginprefix) );
*/			
				

$sql[] = $safesql->query("INSERT INTO %sresponsibleperson (user_id, rp_realname, rp_bounceemail, rp_sonst) 
		VALUES (1, 'Corinna Thoeni', 'corinn@gmx.net', 'blah'),
			(2, 'Franz Schiaber', 'blah@hahaha.com', 'Only RP name! Testing purposes'),
			(4, 'Schiaba Franz', 'info@hintotux.at', 'EIn RP ohne user! geht nicht!'); ", array($pluginprefix) );

$sql[] = $safesql->query("INSERT INTO %srp_group (user_id, group_id) VALUES (1, 4), (1, 3), (1, 2), (4, 1), (2, 2); ", array($pluginprefix) );

$sql[] = $safesql->query("INSERT INTO %suser (user_id, user_name, user_pass, permgroup_id, user_created, user_lastlogin, user_logintries, 
			user_lastedit, user_active, user_permissionlvl) VALUES 
(1, 'corinna', 'cedb35a74c19383eb196cb02636dd045', 1, '2006-12-11 18:10:51', '2007-01-19 18:08:51', 0, '2006-12-14 13:11:07', 1, 1),
(2, 'franz', 'e7f169c9a5847fc2e7825747a2d52dfe', 2, '2006-12-11 18:11:26', '0000-00-00 00:00:00', 0, '2007-01-18 17:10:24', 1, 2),
(3, 'franziska', '5eedb9ea471e2661e6483f0a3ba19804', 1, '2006-12-11 18:11:41', '0000-00-00 00:00:00', 0, '2007-01-18 17:10:14', 1, 3),
(4, 'sonscheice', '4c87310c446f36f101b8fafb569c5e6c', 1, '2007-01-18 17:10:00', '0000-00-00 00:00:00', 0, '2007-01-18 17:10:00', 1, 4);", 
		array($pluginprefix));



		/******************************************************************/

			// Execute queries 
			for ($i = 0; $i < sizeof($sql); $i++) {
					$result = mysql_query($sql[$i]) or die("<b style='color: red; '>Query failed: " . mysql_error() . 
											"<br> --->Statement" . $sql[$i] . "</b><br>");
					echo $sql[$i]; echo "<br> --->"; echo "<b>". $result. "</b><br>";
			}
				
			echo "<b>Install complete.</b><br><br>";
			//mysql_close($link);

			echo "</div><body><html>";
	
} else {
	echo "Could not install plugins. Please mark the config variable useplugins as TRUE";
} 
	

?>


