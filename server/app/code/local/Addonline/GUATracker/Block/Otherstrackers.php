<?php
class Addonline_GUATracker_Block_Otherstrackers extends Mage_Core_Block_Template
{
    public $_order;    

    public function isActive()
    {                        
        if(Mage::getStoreConfigFlag('google/addonline_others_trackers/enable')){
                return true;
        }
        return false;
    }
    
    public function getConfigurationHead(){
        return Mage::getStoreConfig('google/addonline_others_trackers/configuration_header');                
    }    

    public function getConfigurationBody(){
        return Mage::getStoreConfig('google/addonline_others_trackers/configuration_endbody');        
    }
    
}