<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isFlexibilite() {
		return Mage::getStoreConfig('carriers/socolissimo/contrat') == 'flexibilite';
	}
	
	public function isDomicileAvecSignature() {
		return Mage::getStoreConfig('carriers/socolissimo/domicile_signature');
	}
	
	public function getQuoteWeight() {
		//on récupère le poids du colis en gramme de type entier
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$weight = 0;
		foreach ($quote->getAllItems() as $item) {
			$weight += $item->getRowWeight();
		}
		$weight = round($weight*1000);
		if ($weight==0) {
			$weight=1;
		}
		return $weight;
	}
	
	
	public function getShippingDate() {
	
		$shippingDate = new Zend_Date();
		if ($delay = Mage::getStoreConfig('carriers/socolissimo/shipping_period')) {
			$shippingDate->addDay($delay);
		} else {
			$shippingDate->addDay(1);
		}
		return $shippingDate->toString('dd/MM/yyyy');
	}
}
