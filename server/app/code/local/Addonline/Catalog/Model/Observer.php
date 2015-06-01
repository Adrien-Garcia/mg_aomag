<?php
/*class Addonline_Catalog_Model_Observer extends Varien_Object {
	
	function limitProductCompare($event) {
		$limit = Mage::getStoreConfig('addonline/aocompare/limit');
		
		if (Mage::helper('catalog/product_compare')->getItemCount()<$limit) return;
	
		$session = Mage::getSingleton('catalog/session');
		Mage::getSingleton('catalog/product_compare_list')->removeProduct($event->getProduct());
	
		$session->getMessages()->clear();
		$session->addNotice(Mage::helper('catalog/product_compare')->__('You have reached the limit of products to compare. Remove one and try again.'));
	}
}*/