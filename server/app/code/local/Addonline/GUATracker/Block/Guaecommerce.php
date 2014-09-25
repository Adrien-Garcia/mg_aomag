<?php
use UnitedPrototype\GoogleAnalytics\Exception;

class Addonline_GUATracker_Block_Guaecommerce extends Mage_Core_Block_Template
{
    public $_order;

    public function isActive()
    {       
        if(Mage::getStoreConfigFlag('google/addonline_google_ecommerce/enable')){
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
	
	public function sendTransaction($oOrder){	    
	    
	    //On inclue la bibliothèque php-ga UA
        if (file_exists(dirname(__FILE__) . '/Addonline_GUATracker_phpga_universal-analytics.php'))
            include_once 'Addonline_GUATracker_phpga_autoload.php';             
        else
            include_once Mage::getModuleDir('', 'Addonline_GUATracker') . DS .'phpga'.DS.'universal-analytics.php'; 
        
        //On récupère le client_id
        $guaOrderInfos = Mage::getModel('guatracker/guaordersinfos')->load($oOrder->getQuoteId(), 'id_quote');
        $oTracker = new Tracker(/* web property id */ Mage::getStoreConfig('google/addonline_google_tag/account_id'), /* client id */ $guaOrderInfos->getGaUniqueId(), /* user id */ null);                                
        // Send a transaction
        $oTracker->send('transaction', array(
                'transactionId' => $oOrder->getId(),
                'transactionAffiliation' => Mage::getBaseUrl(),
                'transactionRevenue' => ($oOrder->getGrandTotal() - $oOrder->getShippingAmount()), // not including tax or shipping
                'transactionShipping' => $oOrder->getShippingAmount(),
                'transactionTax' => $oOrder->getShippingAmount()
        ));
        
        foreach($oOrder->getAllItems() as $item){
        
            // Send an item record related to the preceding transaction
            $oTracker->send('item', array(
                    'transactionId' => $oOrder->getId(),
                    'itemName' => $item->getName(),
                    'itemCode' => $item->getSku(),
                    'itemCategory' => '',
                    'itemPrice' => $item->getPrice(),
                    'itemQuantity' => $item->getQtyToInvoice()
            ));
        }       

        Mage::Log('sendTransaction end', null, 'guatracker.log');
	}
	

}