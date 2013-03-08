<?php

/**
 * DynController
 * Renders the dynamic blocks that are requested via an ajax call
 *
 */
class Addonline_Varnish_DynController extends Mage_Core_Controller_Front_Action {

	/**
	 * Index action. This action is called by an ajax request
	 *
	 * @return void
	 */
	public function indexAction() {
	
		if (!$this->getRequest()->isXmlHttpRequest()) {
			Mage::throwException('This is not an XmlHttpRequest');
		}
	
		$response = array();
		$response['sid'] = Mage::getModel('core/session')->getEncryptedSessionId();
	
		//si on visite une page produit on ajoute le produit aux dernier produit vus
		if ($currentProductId = $this->getRequest()->getParam('currentProductId')) {
			Mage::getSingleton('catalog/session')->setLastViewedProductId($currentProductId);
		}
	
		$this->loadLayout();
		$layout = $this->getLayout();
	
		$product_ids = array();
		//on renvoie les blocs dynamiques qui ont été demandés
		$requestedBlockNames = $this->getRequest()->getParam('getBlocks');
		foreach ($requestedBlockNames as $id => $requestedBlockName) {
			$tmpBlock = $layout->getBlock($requestedBlockName);
			if ($tmpBlock) {
				$response['blocks'][$id] = $tmpBlock->toHtml();
			} else {
				$response['blocks'][$id] = 'BLOCK NOT FOUND';
			}
		}
		if (Mage::getSingleton('customer/session')->isLoggedIn() == true) {
			//on renvoie les blocs prix qui ont été demandés
			$requestedBlockNames = $this->getRequest()->getParam('getPriceBlocks');
			foreach ($requestedBlockNames as $id => $requestedBlockName) {
				array_push($product_ids, $requestedBlockName);
			}
			
			$products = Mage::getResourceModel("catalog/product_collection");
			$products->addAttributeToFilter('entity_id', array('in' => $product_ids));
			$products->addMinimalPrice();
			$products->addFinalPrice();
			$products->addTaxPercents();
			if(count($products) > 0) {
				$productBlock = $layout->getBlock("product_price_varnish");
				foreach($products as $key => $product) {
					$response['blocks']["catalog_product_".$key] = $productBlock->getPriceHtml($product);
				}
			}
		}
		$this->getResponse()->setBody(Zend_Json::encode($response));
	}

}
