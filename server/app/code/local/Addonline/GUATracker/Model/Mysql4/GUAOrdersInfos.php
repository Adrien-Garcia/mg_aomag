<?php

class Addonline_GUATracker_Model_Mysql4_GUAOrdersInfos extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the brand_id refers to the key field in your database table.
        $this->_init('guatracker/guaordersinfos', 'quote_id');
    }
    
    
    
}