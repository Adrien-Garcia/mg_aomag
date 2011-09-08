<?php 

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS `advancedslideshow` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`title` varchar(255) default '',
	
	PRIMARY KEY(`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
");

$installer->run("

CREATE TABLE IF NOT EXISTS `advancedslideshow_item` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`id_slideshow` int(10) unsigned NOT NULL,
	`from_date` date default '0000-00-00',
	`to_date` date default '0000-00-00',
	`url` varchar (255) default NULL,
	`image` varchar (255) default NULL,
	`product_sku` varchar (64) default NULL,
	`product_name` varchar (64) default NULL,
	`sort_order` tinyint(4) NOT NULL default '0',
	
	PRIMARY KEY(`id`),
	CONSTRAINT `FK_SLIDESHOW` FOREIGN KEY (`id_slideshow`) REFERENCES {$this->getTable('advancedslideshow')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AO Advanced Slideshow Items';
");


$installer->endSetup();