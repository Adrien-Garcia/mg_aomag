<?php
class Addonline_Brand_Model_Product_Attribute_Source_Brand extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('brand/brand_collection')
                ->load()
                ->toOptionArray();
            array_unshift($this->_options, array('value'=>'', 'label'=>Mage::helper('catalog')->__('Please select a brand ...')));
        }
        return $this->_options;
    }
    
    public function getFlatColums() {
    	$columns = array(
    			$this->getAttribute()->getAttributeCode() => array(
    					'type'      => 'varchar(255)',
    					'unsigned'  => false,
    					'is_null'   => true,
    					'default'   => null,
    					'extra'     => null
    			)
    	);
    	return $columns;
    }
    
    public function getFlatUpdateSelect($store) {
    	return Mage::getResourceModel('eav/entity_attribute')
    	->getFlatUpdateSelect($this->getAttribute(), $store);
    }
    
    public function getFlatIndexes() {
    	$indexes = array();
    	$indexName = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
    	$indexes[$indexName] = array(
    			'type'      => 'index',
    			'fields'    => array($this->getAttribute()->getAttributeCode())
    	);
    	return $indexes;
    }
}
