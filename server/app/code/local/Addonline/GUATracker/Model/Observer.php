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
        $order = $observer->getEvent()->getOrder();        
        $state = $observer->getEvent()->getState();
        $status = $observer->getEvent()->getStatus();
        $guaecommerce = Mage::app()->getLayout()->createBlock('addonline_guatracker/guaecommerce');
        $guaecommerce->sendTransaction($order);
    }
}
