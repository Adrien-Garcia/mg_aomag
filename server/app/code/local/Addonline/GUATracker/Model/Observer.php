<?php

class Addonline_GUATracker_Model_Observer extends Varien_Object
{
    public function saveGuaInfos ($observer)
    {
        try {
            $oOrder = $observer->getEvent()->getOrder();
            $guaOrderInfos = Mage::getModel('guatracker/guaordersinfos');                                      
            $guaOrderInfos->setIdQuote($oOrder->getQuoteId());
            $guaOrderInfos->setGaUniqueId($_COOKIE['_ga']);
            
            $guaOrderInfos->save();                               

        } catch (Exception $e) {
            Mage::Log('Failed to save GUA Order\'s data ', null, 'guatracker.log');
        }
    }
    
    public function sendGUAInfos ($observer){                   
        
        if($observer->getEvent()->getStatus() === Mage::getStoreConfig('google/addonline_google_ecommerce/order_status')){
            $order = $observer->getEvent()->getOrder();                    
            $guaecommerce = Mage::app()->getLayout()->createBlock('addonline_guatracker/guaecommerce');
            $guaecommerce->sendTransaction($order);
        }
    }
}
