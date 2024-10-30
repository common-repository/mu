CREATE TABLE IF NOT EXISTS `mu` (
	`id` int(10) NOT NULL auto_increment,
	`post_id` int(10) NOT NULL,
	`post_text` varchar(140) default NULL,
	`url` varchar(140) default NULL,
	`twitter` int(1) NOT NULL default '0',
	`plurk` int(1) NOT NULL default '0',
	`identica` int(1) NOT NULL default '0',
	`friendfeed` int(1) NOT NULL default '0',
	`jaiku` int(1) NOT NULL default '0',
	`last_saved` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM;
