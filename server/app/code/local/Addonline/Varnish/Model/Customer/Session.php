<?php

/**
 * Customer session model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_Varnish_Model_Customer_Session extends Mage_Customer_Model_Session
{
  
    public function isLoggedIn()
    {
        /*
         * Les pages statiques qui sont mises en cache doivent correspondre au cas d'un internaute non loggé
         */
        return parent::isLoggedIn() && !Mage::registry('varnish_static');
    }
    
    public function getCustomerGroupId()
    {
    	if ($this->getData('customer_group_id')) {
    		return $this->getData('customer_group_id');
    	}
    	
    	if (!Mage::registry('varnish_static') || Mage::getStoreConfig('system/external_page_cache/varnish_customer_group_cache')) {
	    	if (parent::isLoggedIn() && $this->getCustomer()) {
	    		return $this->getCustomer()->getGroupId();
	    	}
    	}
    	return Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
    }

}
