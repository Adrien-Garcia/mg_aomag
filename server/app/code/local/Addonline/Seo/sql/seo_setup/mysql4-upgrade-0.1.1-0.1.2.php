<?php

$installer = $this;

$connection = $installer->getConnection();

$installer->startSetup();

$installer->run("ALTER TABLE {$installer->getTable('cms/page')} ADD COLUMN `meta_robots` varchar(20) NOT NULL default '' after meta_description");

$installer->endSetup();
