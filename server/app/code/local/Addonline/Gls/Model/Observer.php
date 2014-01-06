<?php
/**
 * Addonline_Gls
 *
 * @category    Addonline
 * @package     Addonline_Gls
 * @copyright   Copyright (c) 2013 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 */

class Addonline_Gls_Model_Observer extends Varien_Object
{
   	public function __construct()
   	{
   	}

	public function checkoutEventGlsdata($observer)
	{

		$quote = $observer->getEvent()->getQuote();
		$request = Mage::app()->getRequest();

		//si on n'a pas le paramètre shipping_method c'est qu'on n'est pas sur la requête de mise à jour du mode de livraison
		//dans ce cas on ne change rien
		if (!$request->getParam('shipping_method')) {
			return $this;
		}

		$shippingAddress = $quote->getShippingAddress();
		$shippingMethod = $shippingAddress->getShippingMethod();

		return $this;
	}

	public function setShippingRelayAddress($observer){
		$shipping_data = Mage::getSingleton('checkout/session')->getData('gls_shipping_relay_data');
		$quote = $observer->getEvent()->getQuote();
		$shippingAddress = $quote->getShippingAddress();
		$shippingMethod = $shippingAddress->getShippingMethod();
		if(strpos($shippingMethod,'gls_relay') !== false){
			if($shipping_data){
				Mage::getSingleton('checkout/session')->setData('gls_shipping_relay_data',null);
				Mage::getSingleton('checkout/session')->setData('gls_shipping_warnbyphone',$shipping_data['warnbyphone']);
				Mage::getSingleton('checkout/session')->setData('gls_relay_id',$shipping_data['relayId']);
				$shippingAddress->setData('company', $shipping_data['name']);
				$shippingAddress->setData('street', $shipping_data['address']);
				$shippingAddress->setData('city', $shipping_data['city']);
				$shippingAddress->setData('postcode', $shipping_data['zipcode']);
				$shippingAddress->setData('save_in_address_book', 0);
				if($shipping_data['phone']){
					$shippingAddress->setData('telephone', $shipping_data['phone']);
				}else{
					$shippingAddress->setData('telephone', $billingAddress->getData('telephone'));
				}
			}
		}else{
			Mage::getSingleton('checkout/session')->setData('gls_shipping_relay_data',null);
			$billingAddress = $quote->getBillingAddress();
			$shippingMethod = $shippingAddress->getShippingMethod();
			if(strpos($shippingMethod,'gls_relay') !== false){
				$shippingAddress->setData('prefix', $billingAddress->getData('prefix'));
				$shippingAddress->setData('firstname', $billingAddress->getData('firstname'));
				$shippingAddress->setData('company', $billingAddress->getData('company'));
				$shippingAddress->setData('lastname', $billingAddress->getData('lastname'));
				$shippingAddress->setData('street', $billingAddress->getData('street'));
				$shippingAddress->setData('city', $billingAddress->getData('city'));
				$shippingAddress->setData('postcode', $billingAddress->getData('postcode'));
				$shippingAddress->setData('telephone', $billingAddress->getData('telephone'));
				$shippingAddress->setData('save_in_address_book', 0);
			}
		}
	}

	public function addGlsInformationsToOrder($observer){
		try {
			$quote = $observer->getEvent()->getQuote();
			$shippingAddress = $quote->getShippingAddress();
			$shippingMethod = $shippingAddress->getShippingMethod();
			if(strpos($shippingMethod,'gls_relay') !== false){
				$observer->getEvent()->getOrder()->setGlsRelayPointId(Mage::getSingleton('checkout/session')->getData('gls_relay_id'));
				$observer->getEvent()->getOrder()->setGlsWarnByPhone(Mage::getSingleton('checkout/session')->getData('gls_shipping_warnbyphone'));
				$observer->getEvent()->getOrder()->save();
			}
		} catch (Exception $e) {
			Mage::Log('Failed to save GLS data : '.print_r($shippingData, true));
		}
	}
}
