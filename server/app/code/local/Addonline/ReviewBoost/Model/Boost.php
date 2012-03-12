<?php

class Addonline_ReviewBoost_Model_Boost 
{

	public function run() {
		
		$delay = Mage::getStoreConfig('catalog/review/boost_delay');
		//Lister les expéditions avec date creation = date du jour - 5
		$shipments = Mage::getResourceModel('sales/order_shipment_collection')
		             ->addFieldToFilter('created_at', array('field_expr'=>"DATE_FORMAT(DATE_ADD(created_at, INTERVAL $delay DAY), '%m-%d-%Y')", 'eq'=>date('m-d-Y')))
		             ->load();
		             
		$translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
		             
		foreach ($shipments as $shipment) {
			$order = $shipment->getOrder();
			$store = Mage::app()->getStore($order->getStoreId());
            $tpl = Mage::getModel('core/email_template');
            $tpl->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    Mage::getStoreConfig('catalog/review/boost_template', $store),
                    Mage::getStoreConfig('catalog/review/boost_identity', $store),
                    $order->getCustomerEmail(),
                    $order->getCustomerName(),
                    array(
                        'website_name'  => $store->getWebsite()->getName(),
                        'group_name'    => $store->getGroup()->getName(),
                        'store_name'    => $store->getFrontendName(), 
                        'customer_name' => $order->getCustomerName(),
                    	'order'			=> $order,
                    	'shipment'		=> $shipment
                    )
            );
		}
		
		$translate->setTranslateInline(true);  
	}
	
}
