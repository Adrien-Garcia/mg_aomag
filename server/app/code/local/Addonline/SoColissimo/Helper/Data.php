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
}
