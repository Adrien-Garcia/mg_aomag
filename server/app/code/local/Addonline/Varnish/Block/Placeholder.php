<?php

/**
 * Varnish Static Placeholder
 * 
 * @category   Addonline
 * @package    Addonline_Varnish
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_Varnish_Block_Placeholder extends Mage_Core_Block_Template
{

	protected $_replacedBlockName;
	
	
	/**
	 * Render block HTML
	 * 
	 * Ce bloc affiche un div "placeholder" dans le cas d'un affichage "static" pour le cache varnish
	 * 
	 * Il peut être utilisé de deux manières : 
	 * 	- Soit à la place d'un bloc core/template : on remplace le type du bloc par varnish/placeholder, dans le cas normal il se comporte comme  core/template, dans le cas "static" il affiche le div "placeholder"
	 *  - Soit en substitution à un bloc existant dans un bloc core/text_list, en appelant setReplacedBlockName , dans le cas normal il ne fait rien, 
	 *     dans le cas "static" il va supprimer le bloc désigné (quelque soit son type) et le remplacer par le div "placeholder"
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$html = "";
		if (Mage::registry('varnish_static')) {
			//id=BlockAlias pour pouvoir le sélectionner en javascript (sans . dans le nom), par contre on met rel=NameInLayout pour pouvoir le sélectionner dans la layout (avec . dans le nom) 
			$html = '<div id="'.($this->getReplacedBlockAlias()).'" class="varnish_placeholder" rel="'.($this->getReplacedBlockName()).'" ></div>';
		} else {
			$html = parent::_toHtml();
		}
		return $html;
	}

	public function getReplacedBlockName()
	{
		if (!$this->_replacedBlockName) {
			$this->_replacedBlockName = $this->getNameInLayout();
		}
		return $this->_replacedBlockName;
	}
	
	
	/**
	 * Set the ReplacedBlockName
	 *
	 * @return void
	 */
	public function setReplacedBlockName($replacedBlockName)
	{
		$this->_replacedBlockName = $replacedBlockName;		
		if (Mage::registry('varnish_static')) {
			$this->getParentBlock()->unsetChild($this->getReplacedBlockName());
		}
	}
	
	/**
	 * Get the ReplacedBlockAlias
	 *
	 * @return string
	 */
	public function getReplacedBlockAlias() {
		$block = $this->getParentBlock()->getChild($this->getReplacedBlockName());
		if ($block && $block->getBlockAlias()) {
			return $block->getBlockAlias();
		} else {
			return str_replace(".", "_", $this->getReplacedBlockName());
		}
	}
	
}
