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
class Addonline_SoColissimoFlexibilite_Block_Selector extends Mage_Core_Block_Template
{

	private $socolissimoAvaliable;
	private $rdvPointRetraitAcheminement;
	
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
        if (strpos($this->getAddressShippingMethod(), 'socolissimoflexibilite')===0 || count($this->getParentBlock()->getShippingRates())==1) {
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
		return Mage::getStoreConfig('carriers/socolissimoflexibilite/rdv_fees'); 
	}

	public function isRdvAvailable() {
		if ($this->isSocolissmoAvailable()) {
			$rdv = $this->getRdvPointRetraitAcheminement();
			return $rdv->rdv;
		} else {
			$codesPostaux = explode ( ',', Mage::getStoreConfig('carriers/socolissimoflexibilite/rdv_conditions'));
			if (in_array($this->getShippingPostcode(), $codesPostaux)) {
				return true;
			} else {
				false;
			}		
		}
	}

	public function isSocolissmoAvailable() {
		if (!$this->socolissimoAvaliable) {
			$this->socolissimoAvaliable = file_get_contents ("http://ws.colissimo.fr/supervision-wspudo/supervision.jsp");
		}
		return 	trim($this->socolissimoAvaliable) === "[OK]";	
	}
	
	public function isDomicileAvecSignature() {
		return Mage::getStoreConfig('carriers/socolissimoflexibilite/domicile_signature');
	}
	
	public function getRdvPointRetraitAcheminement() {
		if (!$this->rdvPointRetraitAcheminement) {
			$this->rdvPointRetraitAcheminement = Mage::getModel('socolissimoflexibilite/service')->findRDVPointRetraitAcheminement($this->getShippingStreet(), $this->getShippingPostcode(), $this->getShippingCity(), 0);
		}
		return $this->rdvPointRetraitAcheminement; 	
	}
	
	public function getListePointRetrait() {
		return $this->getRdvPointRetraitAcheminement()->listePointRetraitAcheminement;
	}
	
	public function _toHtml(){				
		if(Mage::getModel('socolissimoflexibilite/observer')->_9cd4777ae76310fd6977a5c559c51820()){
			echo (parent::_toHtml());
		}else{
			echo ("<H1>La clé de licence du module est invalide</H1>");
		}
	} 
}