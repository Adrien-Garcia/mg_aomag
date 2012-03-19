<?php 

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$setup->addAttribute('customer', 'interests', array(
        'type'              => 'text',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Interets',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
    ));

Mage::getSingleton( 'eav/config' )
->getAttribute( 'customer', 'interests' )
->setData( 'used_in_forms', array( 'adminhtml_customer' ) )
->save();
    
$setup->endSetup();
?>