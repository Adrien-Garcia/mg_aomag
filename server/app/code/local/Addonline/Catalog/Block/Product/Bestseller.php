<?php
class Addonline_Catalog_Block_Product_Bestseller extends Mage_Catalog_Block_Product_Abstract
{
    protected $_productsCount = null;

    const DEFAULT_PRODUCTS_COUNT = 5;

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);

        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
    	$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();    	
    	$currentCategoryId=0;
    	if ($category = $this->getCurrentCategory()) {
			$currentCategoryId=$category->getId();
		}
    	
    	return array(
           'CATALOG_PRODUCT_BESTSELLER',
           Mage::app()->getStore()->getId(),
    	   $currentCategoryId,
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
           $this->getProductsCount(),
           $currentCurrencyCode
        );
    }

    /**
     * Prepare collection with new products and applied page limits.
     *
     * return Mage_Catalog_Block_Product_New
     */
    protected function _beforeToHtml()
    {

		$collection = Mage::getResourceModel('reports/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
		$this->_addProductAttributesAndPrices($collection);

		if ($category = $this->getCurrentCategory()) {
			$collection->addCategoryFilter($category);
		}
		
		$collection->addOrderedQty()
		            	->addAttributeToSort('ordered_qty','desc')
		            	->addStoreFilter()
		            	->setPageSize($this->getProductsCount());
        
        $this->setProductCollection($collection);

        return parent::_beforeToHtml();
    }

    /**
     * Set how much product should be displayed at once.
     *
     * @param $count
     * @return Mage_Catalog_Block_Product_New
     */
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }
    
    /**
     * Retrieve current category model object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', Mage::registry('current_category'));
        }
        return $this->getData('current_category');
    }

    public function getBestsellerProductCollection() {

        $aProductCollection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect(array('*'))
            //->addOrderedQty()
            ->addAttributeToSort('ordered_qty','desc')
            ->addStoreFilter()
            ->setPageSize($this->getProductsCount());
        
        return $aProductCollection;
    }
}
