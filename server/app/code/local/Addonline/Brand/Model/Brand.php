<?php

class Addonline_Brand_Model_Brand extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('brand/brand');
    }
    
    public function getUrlImage() {
    	if($this->getData('filename')) {
    		$urlImg = Mage::getBaseUrl('media') . 'catalog/brand/'.$this->getData('filename');
    		return $urlImg;
    	} else {
    		return;
    	}
    }
    
    public function urlKeyNormalize(Addonline_Brand_Model_Brand $brand) {
    	if($brand->getUrlKey()) {
    		return $this->formatUrlKey($brand->getUrlKey());
    	} else {
    		return $this->formatUrlKey($brand->getNom());
    	}
    }
    
    public function formatUrlKey($str)
    {
    	$urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
    	$urlKey = strtolower($urlKey);
    	$urlKey = trim($urlKey, '-');
    
    	return $urlKey;
    }
    
    public function saveUrlRewrite(Addonline_Brand_Model_Brand $brand) {
    	$id_path = "brand/".$brand->getId();
    	$request_path = $this->urlKeyNormalize($brand).".html";
    	$target_path = "brand/index/marque/id/".$brand->getId();
    	
    	$modelUrl = Mage::getModel("core/url_rewrite");
    	$modelUrl->loadByIdPath($id_path);
    	if($modelUrl->getIdPath() == $id_path) {
    		$modelUrl->setRequestPath($request_path);
    		$modelUrl->save();
    	} else {
    		$modelUrl->setIdPath($id_path)
    		->setTargetPath($target_path)
    		->setRequestPath($request_path)
    		->setIsSystem(1)
    		->setStoreId(Mage::app()->getStore()->getStoreId());
    		 
    		$modelUrl->save();
    	}
    	
    }
    
}