-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_page_i18nl10n`
-- 

CREATE TABLE `tl_page_i18nl10n` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `language` varchar(2) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `alias` varbinary(128) NOT NULL default '',
  `type` varchar(32) NOT NULL default '',
  `pageTitle` varchar(255) NOT NULL default '',
  `dateFormat` varchar(32) NOT NULL default '',
  `timeFormat` varchar(32) NOT NULL default '',
  `datimFormat` varchar(32) NOT NULL default '',
  `description` text NULL,
  `cssClass` varchar(64) NOT NULL default '',
  `published` char(1) NOT NULL default '',
  `start` varchar(10) NOT NULL default '',
  `stop` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `language` (`language`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_content`
-- 

CREATE TABLE `tl_content` (
 `language` varchar(2) NOT NULL default '',
  KEY `language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_article`
-- 

CREATE TABLE `tl_article` (
 `language` varchar(2) NOT NULL default '',
  KEY `language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
