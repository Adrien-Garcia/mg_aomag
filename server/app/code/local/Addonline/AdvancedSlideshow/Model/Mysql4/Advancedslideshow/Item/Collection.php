<?php

class Addonline_AdvancedSlideshow_Model_Mysql4_Advancedslideshow_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancedslideshow/advancedslideshow_item');
    }
}