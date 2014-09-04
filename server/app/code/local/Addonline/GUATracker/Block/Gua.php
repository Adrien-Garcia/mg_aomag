<?php
class Addonline_GUATracker_Block_Gua extends Mage_Core_Block_Template
{
    public $_order;

    public function getAccountId()
    {
        return Mage::getStoreConfig('google/addonline_google_tag/account_id');
    }

    public function isAnonymizeIp()
    {
        return Mage::getStoreConfigFlag('google/addonline_google_tag/anonymize_ip') ? 'true' : 'false';
    }

    public function isActive()
    {                        
        if(Mage::getStoreConfigFlag('google/addonline_google_tag/enable')
            && Mage::getStoreConfig('google/addonline_google_tag/add_to') == $this->getParentBlock()->getNameInLayout()){
                return true;
        }
        return false;
    }

    public function isSSL()
    {
        return Mage::getStoreConfigFlag('google/addonline_google_tag/force_ssl');
    }

}