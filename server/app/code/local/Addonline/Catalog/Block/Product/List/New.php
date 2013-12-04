<?php
class Addonline_Catalog_Block_Product_List_New extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
			
	        $todayStartOfDayDate  = Mage::app()->getLocale()->date()
	            ->setTime('00:00:00')
	            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	
	        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
	            ->setTime('23:59:59')
	            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        
			$collection = Mage::getResourceModel('catalog/product_collection');
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);
	        
	        
            $collection->addAttributeToFilter('news_from_date', array('or'=> array(
            		0 => array('date' => true, 'to' => $todayEndOfDayDate),
            		1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left');
            $collection->addAttributeToFilter('news_to_date', array('or'=> array(
            		0 => array('date' => true, 'from' => $todayStartOfDayDate),
            		1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left');
            $collection->addAttributeToFilter(
            		array(
            				array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
            				array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
            		)
            );
            $collection->addStoreFilter();

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
