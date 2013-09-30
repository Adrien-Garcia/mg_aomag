<?php


class Addonline_Seo_Model_Attribute extends Mage_Core_Model_Abstract
{    
    public function _construct()
    {
        parent::_construct();
        $this->_init('seo/attribute');
    }
                      
}