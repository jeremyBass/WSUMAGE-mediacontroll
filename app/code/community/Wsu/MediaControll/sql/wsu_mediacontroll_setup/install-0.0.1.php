<?php

$installer = $this;

$installer->startSetup();
$table_imgclean = $installer->getTable('wsu_mediacontroll/imgclean');
$installer->run("
    DROP TABLE IF EXISTS `{$table_imgclean}`;
    CREATE TABLE `{$table_imgclean}` (
			`imgclean_id` int(11) unsigned NOT NULL auto_increment,
			`filename` varchar(255) NOT NULL default '',
			PRIMARY KEY  (`imgclean_id`),
			UNIQUE KEY `filename` (`filename`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
    ");

$table_missassignments = $installer->getTable('wsu_mediacontroll/missassignments');
$installer->run("
    DROP TABLE IF EXISTS `{$table_missassignments}`;
    CREATE TABLE `{$table_missassignments}` (
			`missassignments_id` int(11) unsigned NOT NULL auto_increment,
			`prod_id` int(11) NOT NULL,
			`imgprofile` text NOT NULL default '',
			PRIMARY KEY  (`missassignments_id`),
			UNIQUE KEY `prod_id` (`prod_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
    ");

$table_unsorted = $installer->getTable('wsu_mediacontroll/unsorted');
$installer->run("
    DROP TABLE IF EXISTS `{$table_unsorted}`;
    CREATE TABLE `{$table_unsorted}` (
			`unsorted_id` int(11) unsigned NOT NULL auto_increment,
			`prod_id` int(11) NOT NULL,
			`imgprofile` text NOT NULL default '',
			PRIMARY KEY  (`unsorted_id`),
			UNIQUE KEY `prod_id` (`prod_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
    ");

$installer->endSetup(); 