<?php
/**
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('responsiveslider/responsiveslider_link'))
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Link ID')
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'unsigned'  => true,
    ), 'Item ID')
    ->addColumn('responsiveslider_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Slider ID')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
    ), 'Sort Order')
    ->addIndex($installer->getIdxName('responsiveslider/responsiveslider_item', array('item_id')),
        array('item_id'))
    ->addIndex($installer->getIdxName('responsiveslider/responsiveslider', array('responsiveslider_id')),
        array('responsiveslider_id'))
;
$installer->getConnection()->createTable($table);



$table = $installer->getConnection()
    ->newTable($installer->getTable('responsiveslider/responsiveslider_item_store'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'Slide ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Store ID')
    ->addIndex($installer->getIdxName('responsiveslider/responsiveslider_item_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('responsiveslider/responsiveslider_item_store', 'item_id', 'responsiveslider/responsiveslider_item', 'item_id'),
        'item_id', $installer->getTable('responsiveslider/responsiveslider_item'), 'item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('responsiveslider/responsiveslider_item_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Slide To Store Linkage Table');
$installer->getConnection()->createTable($table);


$installer->endSetup();
