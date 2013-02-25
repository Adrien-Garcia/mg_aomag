<?php

/**
 * Varnish Static Placeholder
 *
 * @category   Addonline
 * @package    Addonline_Varnish
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_Varnish_Block_Placeholder extends Mage_Core_Block_Abstract
{

	protected $_replacedBlockName;
	
	
	/**
	 * Render block HTML
	 * 
	 * On wrappe le bloc Links avec un container qui sera utilisé pour l'appel ajax qui va recharger les liens en fonction de la session utilisateur
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$html = "";
		if (!Mage::registry('varnish_dyn')) {
			Mage::log("placeholder");
			//id=BlockAlias pour pouvoir le sélectionner en javascript (sans . dans le nom), par contre on met rel=NameInLayout pour pouvoir le sélectionner dans la layout (avec . dans le nom) 
			$html = '<div id="'.($this->getReplacedBlockAlias()).'" class="varnish_placeholder" rel="'.($this->_replacedBlockName).'" ></div>';
		}
		return $html;
	}

	/**
	 * Set the ReplacedBlockName
	 *
	 * @return void
	 */
	public function setReplacedBlockName($replacedBlockName)
	{
		$this->_replacedBlockName = $replacedBlockName;		
		if (!Mage::registry('varnish_dyn')) {
			$this->getParentBlock()->unsetChild($this->_replacedBlockName);
		}
	}
	
	/**
	 * Get the ReplacedBlockAlias
	 *
	 * @return string
	 */
	public function getReplacedBlockAlias() {
		$block = $this->getParentBlock()->getChild($this->_replacedBlockName);
		if ($block && $block->getBlockAlias()) {
			return $block->getBlockAlias();
		} else {
			return str_replace(".", "_", $this->_replacedBlockName);
		}
	}
	
}
