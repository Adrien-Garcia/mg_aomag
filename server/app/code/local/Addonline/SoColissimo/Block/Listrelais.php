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
class Addonline_SoColissimo_Block_Listrelais extends Mage_Core_Block_Template
{

	private $_listRelais = array();
	
	public function getListRelais() {
		return $this->_listRelais;
	}
	
	public function setListRelais($list) {
		$this->_listRelais = $list;
	}		
	
}
