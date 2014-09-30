<?php
class Addonline_GUATracker_Model_Guaordersinfo extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('guatracker/guaordersinfos');
    }
    
    public function getOrderGUAFromQuteId($quoteId){
        $this->load($quoteId);
        return $this;
    }
}
?>