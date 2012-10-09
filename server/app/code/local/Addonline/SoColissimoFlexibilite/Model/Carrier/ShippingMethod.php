<?php

/**
 * Addonline_SoColissimoFlexibilite
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimoFlexibilite
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Addonline_SoColissimoFlexibilite_Model_Carrier_ShippingMethod
	extends Owebia_Shipping2_Model_Carrier_AbstractOwebiaShipping
{
	protected $_code = 'socolissimoflexibilite';
	
	/**
	 * Collect rates for this shipping method based on information in $request
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {

		$rates = parent::collectRates($request);
		$shippingAddress = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();		
		
		if ($shippingAddress && $shippingAddress->getData('soco_product_code') == 'RDV') {
			foreach ($rates->getAllRates() as $rate) {			
				if ($rate->getCarrier()===$this->_code) {										
					$rate->setPrice($rate->getPrice()+(int)Mage::getStoreConfig('carriers/socolissimoflexibilite/rdv_fees'));
				}
			}
		}
		if ($shippingAddress && $shippingAddress->getData('soco_product_code') == 'A2P') {
			foreach ($rates->getAllRates() as $rate) {
				if ($rate->getCarrier()===$this->_code) {
					$price = $rate->getPrice()-(float)Mage::getStoreConfig('carriers/socolissimoflexibilite/remise_commercant');
					if($price < 0){ $price = 0; }
					$rate->setPrice($price);
				}
			}
		}
		return $rates;
	}
	
	/**
	 * Surcharge pour 
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	protected function _getMethodText($process, $row, $property)
	{
		$text = parent::_getMethodText($process, $row, $property);
		if ($property == 'label') {

			$checkoutSession = Mage::getSingleton('checkout/session');
			$shippingData = $checkoutSession->getData('socolissimoflexibilite_shipping_data');
			if (isset($shippingData) && count($shippingData) > 0) {
				if ($shippingData['DELIVERYMODE']=='DOM' || $shippingData['DELIVERYMODE']=='DOS') {
		    		$text = $text.' – '.Mage::helper('socolissimoflexibilite')->__("Shipping at home");
		    	} else if ($shippingData['DELIVERYMODE']=='RDV') {
		    		$text = $text.' – '.Mage::helper('socolissimoflexibilite')->__("Making appointment");
		    	} else if ($shippingData['DELIVERYMODE']=='BPR') {
		    		$text = $text.' – '.Mage::helper('socolissimoflexibilite')->__("Post office");
		    	} else if ($shippingData['DELIVERYMODE']=='CIT') {
		    		$text = $text.' – '.Mage::helper('socolissimoflexibilite')->__("Cityssimo space");
		    	} else if ($shippingData['DELIVERYMODE']=='A2P') {
		    		$text = $text.' – '.Mage::helper('socolissimoflexibilite')->__("Shop");
		    	} 
			}
		}
		return $text;
	}
}

?>