-- 
-- Table structure for table `shortenedurls`
-- 

CREATE TABLE `shortenedurls` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `long_url` TEXT NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `creator` char(15) NOT NULL,
  `referrals` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `long` (`long_url`),
  KEY `referrals` (`referrals`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;