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
class Addonline_SoColissimoLiberte_Block_Selector extends Mage_Core_Block_Template
{

	private function _getShippingAddress() {
		return Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
	}
	
	public function getAddressShippingMethod() {
		if ($adress=$this->_getShippingAddress()){
			return $adress->getShippingMethod();
		} else {
			return '';
		}
	}

    /**  we don't show the selector if :
     *    - the selected shipping method is not socolissimo
     *    - there's more than one shipping method
     ***/
    public function dontShowSelector() {    	    	    	
        if (strpos($this->getAddressShippingMethod(), 'socolissimoliberte')===0 || count($this->getParentBlock()->getShippingRates())==1) {        	
            return false;
        }        
        return true;
    }

	public function getShippingStreet() {
		return $this->_getShippingAddress()->getStreetFull();
	}

	public function getShippingPostcode() {
		return $this->_getShippingAddress()->getPostcode();
	}

	public function getShippingCity() {
		return $this->_getShippingAddress()->getCity();
	}
	
	public function getRdvShippingCost() {
		return Mage::getStoreConfig('carriers/socolissimoliberte/rdv_fees'); 
	}
	
	public function isRdvAvailable() {
		$codesPostaux = explode ( ',', Mage::getStoreConfig('carriers/socolissimoliberte/rdv_conditions'));
		if (in_array($this->getShippingPostcode(), $codesPostaux)) {
			return true;
		} else {
			false;
		}		
	}
	
	public function isDomicileAvecSignature() {
		return Mage::getStoreConfig('carriers/socolissimoliberte/domicile_signature');
	}
	
}