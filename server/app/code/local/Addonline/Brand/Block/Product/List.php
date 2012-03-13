<?php 
require_once 'Mage/Catalog/Block/Product/List.php';
class Addonline_Brand_Block_Product_List extends Mage_Catalog_Block_Product_List
{
	protected function _getProductCollection()
	{
		
		if ($idMarque = $this->getRequest()->getParam("id")) {
			$_productCollection = Mage::getResourceModel("catalog/product_collection")
						->addAttributeToSelect("*")
						->addAttributeToFilter("brand", $idMarque);
		}
	
		return $_productCollection;
	}
	
	public function getLoadedProductCollection()
	{
		return $this->_getProductCollection();
	}
	
}