<?php
/**
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'responsiveslider/responsiveslider'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('responsiveslider/responsiveslider'))
    ->addColumn('responsiveslider_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Slider ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Slider Title')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Slider String Identifier')
    ->addColumn('baseline', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Slider Baseline')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Slider Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Slider Modification Time')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Slider Active')
    ->setComment('CMS Slider Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'responsiveslider/responsiveslider_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('responsiveslider/responsiveslider_item'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Item ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Slide Title')
    ->addColumn('baseline', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Slide baseline')
    ->addColumn('url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Slide Url')
    ->addColumn('background_image', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Slide background Image')
    ->addColumn('alt_image', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Slide alternative Image')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
    ), 'Is Slide Active')
    ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Slide From Date')
    ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Slide To Date')
    ->addColumn('product_sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Slide Product Sku')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
    ), 'Block Content')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Block Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Block Modification Time')
    ->setComment('CMS Slider Item Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'responsiveslider/responsiveslider_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('responsiveslider/responsiveslider_store'))
    ->addColumn('responsiveslider_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'Responsiveslider ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Store ID')
    ->addIndex($installer->getIdxName('responsiveslider/responsiveslider_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('responsiveslider/responsiveslider_store', 'responsiveslider_id', 'responsiveslider/responsiveslider', 'responsiveslider_id'),
        'responsiveslider_id', $installer->getTable('responsiveslider/responsiveslider'), 'responsiveslider_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('responsiveslider/responsiveslider_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Slider To Store Linkage Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
