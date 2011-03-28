<?php
class Addonline_Brand_Model_Product_Attribute_Source_Bloccms extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('brand/attribute_source_bloccms')
                ->load()
                ->toOptionArray();
            array_unshift($this->_options, array('value'=>'', 'label'=>Mage::helper('catalog')->__('Please select a static block ...')));
        }
        return $this->_options;
    }
}
