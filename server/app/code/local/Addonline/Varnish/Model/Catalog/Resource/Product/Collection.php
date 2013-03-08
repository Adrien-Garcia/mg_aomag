<?php
class Addonline_Varnish_Model_Catalog_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
	public function addPriceData($customerGroupId = null, $websiteId = null)
	{
		$this->_productLimitationFilters['use_price_index'] = true;
	
		if (!isset($this->_productLimitationFilters['customer_group_id']) && is_null($customerGroupId)) {
			if(!Mage::registry('varnish_static')) {
				$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
			} else {
				$customerGroupId = 0;
			}
		}
		if (!isset($this->_productLimitationFilters['website_id']) && is_null($websiteId)) {
			$websiteId       = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
		}
	
		if (!is_null($customerGroupId)) {
			$this->_productLimitationFilters['customer_group_id'] = $customerGroupId;
		}
		if (!is_null($websiteId)) {
			$this->_productLimitationFilters['website_id'] = $websiteId;
		}
	
		$this->_applyProductLimitations();
	
		return $this;
	}
}