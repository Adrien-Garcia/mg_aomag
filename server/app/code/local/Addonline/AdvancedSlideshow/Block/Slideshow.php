<?php
class Addonline_AdvancedSlideshow_Block_Slideshow extends Mage_Catalog_Block_Product_Abstract
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function setSlideshow($id)
    {
    	$this->setData("id_slideshow", $id);	
    }
    
	public function getSlideshowData()
	{
		$id_slideshow = $this->getData("id_slideshow");
		
		$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATE_INTERNAL_FORMAT);
        
		$_slideshow_items = Mage::getModel('advancedslideshow/advancedslideshow_item')->getCollection();
		$_slideshow_items->addFieldToFilter('id_slideshow', $id_slideshow);
		$_slideshow_items->addFieldToFilter('from_date', array('date' => true, 'to' => $todayDate));
		$_slideshow_items->addFieldToFilter('to_date', array('date' => true, 'from' => $todayDate));
		$_slideshow_items->getSelect()->order('sort_order', 'asc');
		
		$block_data = $_slideshow_items->getData();
		$result = array();
		if(!count($block_data))
		{
			return $result;
		}
		foreach($block_data as $id => $block)
		{
			
			$result[$id]['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $block['image'];
			$result[$id]['url'] = $block['url'];
			$result[$id]['is_product'] = false;
			
			$sku = $block['product_sku'];
			if(!empty($sku))
			{
				$result[$id]['image'] = null;
				$product = Mage::getModel('catalog/product');
				$productId = $product->getIdBySku($sku);
				if($productId)
				{
					$product->load($productId);
					
					$result[$id]['is_product'] = true;
					$result[$id]['product']          = $product;
					$result[$id]['product']['name']  = $product->getName();
					$result[$id]['product']['price'] = Mage::helper('core')->currency($product->getPrice());
					$result[$id]['product']['url']   = Mage::helper('catalog/product')->getProductUrl($product);
					
					$result[$id]['product']['image'] = $product->getImageUrl();
				}
				else
				{
					
				}
			}
		}
		
		return $result;	
	}
}