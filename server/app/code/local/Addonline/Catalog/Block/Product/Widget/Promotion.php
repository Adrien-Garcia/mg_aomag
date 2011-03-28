<?php

class Addonline_Catalog_Block_Product_Widget_Promotion
    extends  Addonline_Catalog_Block_Product_Promotion
    implements Mage_Widget_Block_Interface
{
    /**
     * Internal contructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
    }

    /**
     * Retrieve how much products should be displayed.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            return parent::getProductsCount();
        }
        return $this->_getData('products_count');
    }
}
