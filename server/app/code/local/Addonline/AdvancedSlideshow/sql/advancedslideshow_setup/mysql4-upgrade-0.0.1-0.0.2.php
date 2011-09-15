<?php 

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `advancedslideshow_item` DROP COLUMN product_name;

");


$installer->endSetup();