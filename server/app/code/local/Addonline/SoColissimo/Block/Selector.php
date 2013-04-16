<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_SoColissimo_Block_Selector extends Mage_Core_Block_Template
{

	/**
	private $socolissimoAvaliable;
	private $rdvPointRetraitAcheminement;
	*/
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

	public function getShippingStreet() {
		return $this->_getShippingAddress()->getStreetFull();
	}

	public function getShippingPostcode() {
		return $this->_getShippingAddress()->getPostcode();
	}

	public function getShippingCity() {
		return $this->_getShippingAddress()->getCity();
	}
	
	public function getShippingCountry() {
		return $this->_getShippingAddress()->getCountry();
	}
	
	public function getTelephone() {
		return $this->_getShippingAddress()->getTelephone();
	}
	
	
/**
	public function isSocolissmoAvailable() {
		if (!$this->socolissimoAvaliable) {
			if (Mage::helper('socolissimo')->isFlexibilite()) {
				try {
				 	$this->socolissimoAvaliable = file_get_contents ("http://ws.colissimo.fr/supervision-wspudo/supervision.jsp");
				 } catch(Exception $e){
	          		$this->socolissimoAvaliable = "[KO]";
	        	 }
			} else {
				$this->socolissimoAvaliable = "[OK]";
			}
		}
		return 	trim($this->socolissimoAvaliable) === "[OK]";	
	}
**/
	
	public function _toHtml(){			
		$storeId = Mage::app()->getStore()->getStoreId();			
		if(Mage::getModel('socolissimo/observer')->_9cd4777ae76310fd6977a5c559c51820($storeId)){
			echo (parent::_toHtml());
		}else{
			echo ("<H1>La clé de licence du module SoColissimo est invalide</H1>");
		}
	} 
}