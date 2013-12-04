<?php
if ((string)Mage::getConfig()->getModuleConfig('OrganicInternet_SimpleConfigurableProducts')->active != 'true')
{
	class OrganicInternet_SimpleConfigurableProducts_Catalog_Block_Product_Price extends Mage_Catalog_Block_Product_Price{}
}
class Addonline_Varnish_Block_Catalog_Product_Price extends OrganicInternet_SimpleConfigurableProducts_Catalog_Block_Product_Price
{

	protected $_placeholder = false;

	/**
	 * Set the original module name to avoid breaking translations
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setModuleName('Mage_Catalog');
	}
	
	/**
	 * Render block HTML
	 *
	 * On wrappe le bloc price avec un container qui sera utilisé pour l'appel ajax qui va recharger les prix en fonction de la session utilisateur
	 *
	 * @return string
	 */
	public function _toHtml()
	{
		$html = parent::_toHtml();
		$product_id = $this->getProduct()->getId();
		//id=BlockAlias pour pouvoir le sélectionner en javascript (sans . dans le nom), par contre on met rel=NameInLayout pour pouvoir le sélectionner dans la layout (avec . dans le nom)
		$html = '<div id="catalog_product_'.$product_id.'" class="varnish_catalog_product" rel="'.$product_id.'">'.$html.'</div>';
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