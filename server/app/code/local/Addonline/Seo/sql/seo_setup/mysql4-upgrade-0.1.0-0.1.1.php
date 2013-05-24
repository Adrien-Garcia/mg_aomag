<?php

$installer = $this;

$connection = $installer->getConnection();

$installer->startSetup();

// /* modification du scope des url_key : a été normalement fait dans app\code\core\Mage\Catalog\sql\catalog_setup\mysql4-upgrade-1.4.0.0.7-1.4.0.0.8.php 
$installer->updateAttribute('catalog_category', 'url_key', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_category', 'url_path', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);

$installer->updateAttribute('catalog_product', 'url_key', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_product', 'url_path', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);

$installer->endSetup();
