<?php
class Addonline_Catalog_Block_Product_List_New extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
			
        	$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

			$collection = Mage::getResourceModel('catalog/product_collection');
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);
	        $collection->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
	            ->addAttributeToFilter(array(
	                        array('attribute' => 'news_to_date', 'date' => true, 'from' => $todayDate),
	                        array('attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('null'))
	            ))
                ->addStoreFilter();

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
