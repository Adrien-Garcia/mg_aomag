<?php
class Addonline_GUATracker_Block_Guacheckout extends Mage_Core_Block_Template
{
    public $_order;    

    public function isActive()
    {                        
        if(Mage::getStoreConfigFlag('google/addonline_google_checkout_tracking/enable')){
                return true;
        }
        return false;
    }
    
    public function getPage($number){
        return Mage::getStoreConfig('google/addonline_google_checkout_tracking/page_'.$number);
    }
    
    public function getTitle($number){
        return Mage::getStoreConfig('google/addonline_google_checkout_tracking/title_'.$number);
    }
    
}