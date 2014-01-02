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
		if($shipping_data){
			Mage::getSingleton('checkout/session')->setData('gls_shipping_relay_data',null);
			$quote = $observer->getEvent()->getQuote();
			$shippingAddress = $quote->getShippingAddress();
			$shippingMethod = $shippingAddress->getShippingMethod();
			if(strpos($shippingMethod,'gls_relay') !== false){
				$shippingAddress->setData('prefix', '');
				$shippingAddress->setData('firstname', $shipping_data['name']);
				$shippingAddress->setData('lastname', $shipping_data['relayId']);
				$shippingAddress->setData('street', $shipping_data['address']);
				$shippingAddress->setData('city', $shipping_data['city']);
				$shippingAddress->setData('postcode', $shipping_data['zipcode']);
				$shippingAddress->setData('save_in_address_book', 0);
			}
		}else{
			$quote = $observer->getEvent()->getQuote();
			$shippingAddress = $quote->getShippingAddress();
			$billingAddress = $quote->getBillingAddress();
			$shippingMethod = $shippingAddress->getShippingMethod();
			if(strpos($shippingMethod,'gls_relay') !== false){
				$shippingAddress->setData('prefix', $billingAddress->getData('prefix'));
				$shippingAddress->setData('firstname', $billingAddress->getData('firstname'));
				$shippingAddress->setData('lastname', $billingAddress->getData('lastname'));
				$shippingAddress->setData('street', $billingAddress->getData('street'));
				$shippingAddress->setData('city', $billingAddress->getData('city'));
				$shippingAddress->setData('postcode', $billingAddress->getData('postcode'));
				$shippingAddress->setData('save_in_address_book', 0);
			}
		}
	}
}
