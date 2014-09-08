<?php
class Addonline_GUATracker_Block_Guatracking extends Mage_Core_Block_Template
{
    public $_order;    

    public function isActive()
    {                        
        if(Mage::getStoreConfigFlag('google/addonline_google_tracking/enable')){
                return true;
        }
        return false;
    }
    
    public function getConfigurationArray(){
        $config =  Mage::getStoreConfig('google/addonline_google_tracking/configuration');
        $EOL = PHP_EOL;

        $aReturn = explode($EOL,$config);
        return $aReturn;
    } 
    
}