<?php

$installer = $this;

$installer->startSetup();

$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_category', 'balise_h1', array(
        'type'     => 'varchar',
        'label'    => 'Balise h1',
        'input'    => 'text',
        'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'required' => false,
        'default'  => "",
        'is_visible_on_front' => true
));

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'balise_h1',
    '20'
);

$attributeId = $installer->getAttributeId($entityTypeId, 'balise_h1');

$installer->run("
        INSERT INTO `{$installer->getTable('catalog_category_entity_varchar')}`
        (`entity_type_id`, `attribute_id`, `entity_id`, `value`)
        SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, NULL
        FROM `{$installer->getTable('catalog_category_entity')}`;
        ");

$installer->endSetup();
