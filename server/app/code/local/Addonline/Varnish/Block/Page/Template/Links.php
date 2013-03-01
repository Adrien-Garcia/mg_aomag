<?php

/**
 * Simple links list block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_Varnish_Block_Page_Template_Links extends Mage_Page_Block_Template_Links
{

	protected $_placeholder = false;

	/**
	 * Render block HTML
	 * 
	 * On wrappe le bloc Links avec un container qui sera utilisé pour l'appel ajax qui va recharger les liens en fonction de la session utilisateur
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$html = parent::_toHtml();
		if ($this->_placeholder && Mage::registry('varnish_static')) {
			//id=BlockAlias pour pouvoir le sélectionner en javascript (sans . dans le nom), par contre on met rel=NameInLayout pour pouvoir le sélectionner dans la layout (avec . dans le nom) 
			$html = '<div id="'.($this->getBlockAlias()).'" class="varnish_placeholder" rel="'.($this->getNameInLayout()).'" >'.$html.'</div>';
		}
		return $html;
	}

	/**
	 * Add Placeholder wrapper Flag
	 *
	 * @return void
	 */
	public function addPlaceholder($placeholder = true)
	{
		$this->_placeholder = $placeholder;		
	}
	
}
