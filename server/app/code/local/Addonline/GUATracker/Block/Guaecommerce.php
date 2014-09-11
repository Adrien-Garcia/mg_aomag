<?php
class Addonline_GUATracker_Block_Guaecommerce extends Mage_Core_Block_Template
{
    public $_order;

    public function isActive()
    {       

        //On récupère la page de succès de commande
        $successPath =  Mage::getStoreConfig('google/addonline_google_ecommerce/success_url') != "" ? Mage::getStoreConfig('google/addonline_google_ecommerce/success_url') : '/checkout/onepage/success';
        //Si on est sur la page de succès de commande, on fait le tracking google eCommerce
        if(Mage::getStoreConfigFlag('google/addonline_google_ecommerce/enable') && strpos($this->getRequest()->getPathInfo(), $successPath) !== false){
            return true;
        }                    
        return false;
    }   

    public function getOrder()
    {
        if(!isset($this->_order)){
            $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            $this->_order = Mage::getModel('sales/order')->load($orderId);
        }
        return $this->_order;
    }

}