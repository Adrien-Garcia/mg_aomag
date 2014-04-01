<?php

$installer = $this;

$installer->startSetup();


$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_category', 'navigation_type',  array(
		'type'     => 'int',
		'label'    => 'Navigation Type',
		'input'    => 'select',
		'source'   => 'catalog/category_attribute_source_navigationtype',
		'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'required' => false,
		'default'  => 0
));

$installer->addAttribute('catalog_category', 'page_cms',  array(
		'type'     => 'varchar',
		'label'    => 'CMS Page',
		'input'    => 'select',
		'source'   => 'catalog/category_attribute_source_cmspage',
		'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'required' => false
));

$installer->addAttributeToGroup(
		$entityTypeId,
		$attributeSetId,
		$attributeGroupId,
		'navigation_type',
		'11'
);

$installer->addAttributeToGroup(
		$entityTypeId,
		$attributeSetId,
		$attributeGroupId,
		'page_cms',
		'12'
);

$attributeId = $installer->getAttributeId($entityTypeId, 'navigation_type');

$installer->run("
		INSERT INTO `{$installer->getTable('catalog_category_entity_int')}`
		(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
		SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '0'
		FROM `{$installer->getTable('catalog_category_entity')}`;
		");

$installer->endSetup();
