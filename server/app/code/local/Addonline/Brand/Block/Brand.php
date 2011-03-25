<?php
class Addonline_Brand_Block_Brand extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBrand()     
     { 
        if (!$this->hasData('brand')) {
            $this->setData('brand', Mage::registry('brand'));
        }
        return $this->getData('brand');
        
    }
}