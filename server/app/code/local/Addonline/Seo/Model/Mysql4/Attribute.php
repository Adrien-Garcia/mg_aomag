<?php

class Addonline_Seo_Model_Mysql4_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
	
 	protected $_isPkAutoIncrement = false;

 	public function _construct()
    {
        $this->_init('seo/attribute', 'attribute_id');
    }

}