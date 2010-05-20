-- CONFIG <?php die('Move along...'); ?>

CREATE TABLE :::config::: (
  `config_name` varchar(64) NOT NULL default '',
  `config_value` text NOT NULL,
  `config_description` tinytext NOT NULL,
  `autoload` enum('on','off') NOT NULL default 'on',
  `user_change` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`config_name`)
);

INSERT INTO :::config::: VALUES ('admin_username', 'admin', 'Username', 'off', 'on');
INSERT INTO :::config::: VALUES ('admin_password', 'c40d70861d2b0e48a8ff2daa7ca39727', 'Password', 'off', 'on');
INSERT INTO :::config::: VALUES ('admin_email', 'nesta@iceburg.net', 'Administrator Email', 'on', 'on');
INSERT INTO :::config::: VALUES ('site_name', 'A', 'Website Name', 'on', 'on');
INSERT INTO :::config::: VALUES ('site_url', 'http://66.111.62.220/pommo', 'Website URL', 'on', 'on');
INSERT INTO :::config::: VALUES ('site_success', '', 'Signup Success URL', 'off', 'on');
INSERT INTO :::config::: VALUES ('site_confirm', '', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_name', 'A', 'List Name', 'on', 'on');
INSERT INTO :::config::: VALUES ('list_fromname', 'poMMo Administrative Team', 'From Name', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_fromemail', 'pommo@yourdomain.com', 'From Email', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_frombounce', 'bounces@yourdomain.com', 'Bounces', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_exchanger', 'sendmail', 'List Exchanger', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_confirm', 'on', 'Confirmation Messages', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_charset', 'UTF-8', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_wysiwyg', 'on', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('maxRuntime', '80', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('messages', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('notices', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('demo_mode', 'on', 'Demonstration Mode', 'on', 'on');
INSERT INTO :::config::: VALUES ('smtp_1', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('smtp_2', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('smtp_3', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('smtp_4', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('throttle_DBPP', '0', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_DP', '10', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_DMPP', '0', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_BPS', '0', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_MPS', '3', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_SMTP', 'individual', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('public_history', 'off', 'Public Mailing History', 'off', 'on');
INSERT INTO :::config::: VALUES ('version', 'Aardvark PR16.1', 'poMMo Version', 'on', 'off');
INSERT INTO :::config::: VALUES ('key', '123456', 'Unique Identifier', 'on', 'off');
INSERT INTO :::config::: VALUES ('revision', '42', 'Internal Revision', 'on', 'off');

-- DEMOGRAPHICS

CREATE TABLE :::fields::: (
  `field_id` smallint(5) unsigned NOT NULL auto_increment,
  `field_active` enum('on','off') NOT NULL default 'off',
  `field_ordering` smallint(5) unsigned NOT NULL default '0',
  `field_name` varchar(60) default NULL,
  `field_prompt` varchar(60) default NULL,
  `field_normally` varchar(60) default NULL,
  `field_array` text,
  `field_required` enum('on','off') NOT NULL default 'off',
  `field_type` enum('checkbox','multiple','text','date','number','comment') default NULL,
  PRIMARY KEY  (`field_id`),
  KEY `active` (`field_active`,`field_ordering`)
);

-- GROUP_CRITERIA  

CREATE TABLE :::group_rules::: (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned NOT NULL default '0',
  `field_id` tinyint(3) unsigned NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0' COMMENT '0: OFF, (and), 1: ON (or)',  
  `logic` enum('is','not','greater','less','true','false','is_in','not_in') NOT NULL,
  `value` text,
  PRIMARY KEY  (`rule_id`),
  KEY `group_id` (`group_id`)
);

-- GROUPS

CREATE TABLE :::groups::: (
  `group_id` smallint(5) unsigned NOT NULL auto_increment,
  `group_name` tinytext  NOT NULL,
  PRIMARY KEY  (`group_id`)
);


-- MAILING_CURRENT

CREATE TABLE :::mailing_current::: (
  `current_id` int(10) unsigned NOT NULL,
  `command` enum('none','restart','stop','cancel') NOT NULL default 'none',
  `serial` int unsigned default NULL,
  `securityCode` char(32) default NULL,
  `notices` longtext default NULL,
  `current_status` enum('started','stopped') NOT NULL default 'stopped',
  `touched` timestamp NOT NULL,
  PRIMARY KEY  (`current_id`)
);

-- MAILING_NOTICES

CREATE TABLE :::mailing_notices::: (
  `mailing_id` int(10) unsigned NOT NULL,
  `notice` varchar(255) NOT NULL,
  `touched` timestamp NOT NULL,
  `id` smallint(5) unsigned NOT NULL,
  KEY `mailing_id` (`mailing_id`)
);

-- MAILINGS

CREATE TABLE :::mailings::: (
  `mailing_id` int(10) unsigned NOT NULL auto_increment,
  `fromname` varchar(60) NOT NULL default '',
  `fromemail` varchar(60) NOT NULL default '',
  `frombounce` varchar(60) NOT NULL default '',
  `subject` varchar(60) NOT NULL default '',
  `body` mediumtext NOT NULL,
  `altbody` mediumtext default NULL,
  `ishtml` enum('on','off') NOT NULL default 'off',
  `mailgroup` varchar(60) NOT NULL default 'Unknown',
  `subscriberCount` int(10) unsigned NOT NULL default '0',
  `started` datetime NOT NULL,
  `finished` datetime default NULL,
  `sent` int(10) unsigned NOT NULL default '0',
  `charset` varchar(15) NOT NULL default 'UTF-8',
  `status` tinyint(1) NOT NULL default '1' COMMENT '0: finished, 1: processing, 2: cancelled',
  PRIMARY KEY  (`mailing_id`),
  KEY `status` (`status`)
);


-- QUEUE

CREATE TABLE :::queue::: (
  `subscriber_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL default '0' COMMENT '0: unsent, 1: sent, 2: failed',
  `smtp` tinyint(1) NOT NULL default '0' COMMENT '0: none, 1-4: Designated to SMTP relay #',
  PRIMARY KEY  (`subscriber_id`),
  KEY `status` (`status`,`smtp`)
);


-- SUBSCRIBER_DATA

CREATE TABLE :::subscriber_data::: (
  `data_id` bigint(20) unsigned NOT NULL auto_increment,
  `field_id` int(10) unsigned NOT NULL default '0',
  `subscriber_id` int(10) unsigned NOT NULL default '0',
  `value` char(60) NOT NULL default '',
  PRIMARY KEY  (`data_id`),
  KEY `subscriber_id` (`subscriber_id`,`field_id`)
);

-- SUBSCRIBER_PENDING

CREATE TABLE :::subscriber_pending::: (
  `pending_id` int(10) unsigned NOT NULL auto_increment,
  `subscriber_id` int(10) unsigned NOT NULL default '0',
  `pending_code` char(32) NOT NULL,
  `pending_type` enum('add','del','change','password') default NULL,
  `pending_array` text NULL default NULL,
  PRIMARY KEY  (`pending_id`),
  KEY `code` (`pending_code`),
  KEY `subscriber_id` (`subscriber_id`)
);

--  SUBSCRIBERS

CREATE TABLE :::subscribers::: (
  `subscriber_id` int(10) unsigned NOT NULL auto_increment,
  `email` char(60) NOT NULL default '',
  `time_touched` timestamp(14) NOT NULL,
  `time_registered` datetime NOT NULL,
  `flag` tinyint(1) NOT NULL default '0' COMMENT '0: NULL, 1-8: REMOVE, 9: UPDATE',
  `ip` int unsigned NULL default NULL COMMENT 'Stored with INET_ATON(), Fetched with INET_NTOA()',
  `status` tinyint(1) NOT NULL default '2' COMMENT '0: Inactive, 1: Active, 2: Pending',
  PRIMARY KEY  (`subscriber_id`),
  KEY `status` (`status`,`subscriber_id`),
  KEY `status_2` (`status`,`email`),
  KEY `status_3` (`status`,`time_touched`),
  KEY `status_4` (`status`,`time_registered`),
  KEY `status_5` (`status`,`ip`),
  KEY `flag` (`flag`)
);


-- TEMPLATES

CREATE TABLE :::templates::: (
  `template_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR( 60 ) NOT NULL DEFAULT 'name',
  `description` VARCHAR( 255 ) NULL ,
  `body` MEDIUMTEXT NULL ,
  `altbody` MEDIUMTEXT NULL,
  PRIMARY KEY (`template_id`)
);

-- UPDATES

CREATE TABLE :::updates::: (
  `serial` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`serial`)
);

-- SCRATCH

CREATE TABLE :::scratch::: (
  `scratch_id` int(10) unsigned NOT NULL auto_increment,
  `time` TIMESTAMP NOT NULL,
  `type` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Used to identify row type. 0 = undifined, 1 = ',
  `int` BIGINT NULL,
  `str` TEXT NULL,
  PRIMARY KEY (`scratch_id`),
  KEY `type`(`type`)
)
COMMENT = 'General Purpose Table for caches, counts, etc.';

