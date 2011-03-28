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
}
