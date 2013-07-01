<?php

$installer = $this;

$connection = $installer->getConnection();

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('seo_attribute')}` (
  `attribute_id` smallint(5) unsigned NOT NULL,
  `meta_robots` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY  (`attribute_id`),
  CONSTRAINT `FK_seo_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");

$installer->endSetup();
