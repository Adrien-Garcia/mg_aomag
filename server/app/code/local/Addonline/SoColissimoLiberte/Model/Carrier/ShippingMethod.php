<?php

/**
 * Addonline_SoColissimoLiberte
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimoLiberte
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Addonline_SoColissimoLiberte_Model_Carrier_ShippingMethod
	extends Owebia_Shipping2_Model_Carrier_AbstractOwebiaShipping
{
	protected $_code = 'socolissimoliberte';
	
	/**
	 * Collect rates for this shipping method based on information in $request
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {

		$rates = parent::collectRates($request);
		$shippingAddress = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
		if ($shippingAddress && $shippingAddress->getData('soco_mode_rdv')) {
			foreach ($rates->getAllRates() as $rate) {
				if ($rate->getCarrier()===$this->_code) {
					$rate->setPrice($rate->getPrice()+(int)Mage::getStoreConfig('carriers/socolissimoliberte/rdv_fees'));
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
			$shippingData = $checkoutSession->getData('socolissimoliberte_shipping_data');
			if (isset($shippingData) && count($shippingData) > 0) {
				if ($shippingData['DELIVERYMODE']=='DOM' || $shippingData['DELIVERYMODE']=='DOS') {
		    		$text = $text.' – '.Mage::helper('socolissimoliberte')->__("Shipping at home");
		    	} else if ($shippingData['DELIVERYMODE']=='RDV') {
		    		$text = $text.' – '.Mage::helper('socolissimoliberte')->__("Making appointment");
		    	} else if ($shippingData['DELIVERYMODE']=='BPR') {
		    		$text = $text.' – '.Mage::helper('socolissimoliberte')->__("Post office");
		    	} else if ($shippingData['DELIVERYMODE']=='CIT') {
		    		$text = $text.' – '.Mage::helper('socolissimoliberte')->__("Cityssimo space");
		    	} else if ($shippingData['DELIVERYMODE']=='A2P') {
		    		$text = $text.' – '.Mage::helper('socolissimoliberte')->__("Shop");
		    	} 
			}
		}
		return $text;
	}
}

?>