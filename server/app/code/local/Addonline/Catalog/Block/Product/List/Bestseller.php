<?php
class Addonline_Catalog_Block_Product_List_Bestseller extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
			$collection = Mage::getResourceModel('reports/product_collection');
	        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
			$this->_addProductAttributesAndPrices($collection);
	
			if ($category = $this->getCurrentCategory()) {
				$collection->addCategoryFilter($category);
			}

	        $collection->addOrderedQty()
            	->addStoreFilter()
	            ->addAttributeToSort('ordered_qty','desc')
            	->setPageSize($this->getToolbarBlock()->getLimit());

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
