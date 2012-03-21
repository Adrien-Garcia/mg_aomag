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
class Addonline_SoColissimoLiberte_Block_Listrelais extends Mage_Core_Block_Template
{

	private $_listRelais = array();
	
	public function getListRelais() {
		return $this->_listRelais;
	}
	
	public function setListRelais($list) {
		$this->_listRelais = $list;
	}		
	
	public function _toHtml(){
		if(Mage::getModel('socolissimoliberte/observer')->_9cd4777ae76310fd6977a5c559c51820()){
			echo (parent::_toHtml());
		}else{
			echo ("<H1>La clé de licence du module est invalide</H1>");
		}
	}
	
}