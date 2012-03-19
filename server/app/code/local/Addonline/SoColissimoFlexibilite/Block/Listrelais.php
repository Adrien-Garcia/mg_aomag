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
class Addonline_SoColissimoFlexibilite_Block_Listrelais extends Mage_Core_Block_Template
{

	private $_listRelais = array();
	
	public function getListRelais() {
		return $this->_listRelais;
	}
	
	public function setListRelais($list) {
		$this->_listRelais = $list;
	}		
	
	public function _toHtml(){
		if(Mage::getStoreConfig('socolissimoflexibilite/licence/active')){
			echo (parent::_toHtml());
		}else{
			echo ("<H1>La clé de licence du module est invalide</H1>");
		}
	} 
}