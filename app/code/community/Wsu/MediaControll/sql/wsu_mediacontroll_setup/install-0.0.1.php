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

$installer->endSetup(); 