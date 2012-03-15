<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('brand')};
CREATE TABLE {$this->getTable('brand')} (
	`brand_id` int(11) unsigned NOT NULL auto_increment,
	`filename` varchar(255) NOT NULL default '',
	`nom`  varchar(255) NOT NULL default '',
	`description` text NOT NULL default '',
	`meta_title` varchar(255) NOT NULL default '',
	`meta_description` text NOT NULL default '',
	`meta_keyword`  varchar(255) NOT NULL default '',
	`bloc_cms` smallint(6) NOT NULL default '0', 
	`status` smallint(6) NOT NULL default '0',
	`created_time` datetime NULL,
	`update_time` datetime NULL,
	PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");



// Création de l'attribut brand (marque) qui liée au produit
$installer->addAttribute('catalog_product', 'brand', array(
        'group'             => 'Général',
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Brand',
         'note'              => '',
         'input'             => 'select',
        'class'             => '',
        'source'            => 'brand/product_attribute_source_brand',
        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => true,
        'filterable'        => true,
        'comparable'        => true,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => 'simple,configurable,virtual,bundle,downloadable',
        'is_configurable'   => false,
  	'used_in_product_listing' => true,
 	 'used_for_sort_by'  => true
    ));
$installer->endSetup(); 