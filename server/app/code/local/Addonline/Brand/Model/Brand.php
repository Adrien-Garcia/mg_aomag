<?php

class Addonline_Brand_Model_Brand extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('brand/brand');
    }
    
    public function getUrlImage() {
    	$urlImg = Mage::getBaseUrl('media') . 'catalog/brand/'.$this->getData('filename');
    	return $urlImg;
    }
    
    public function saveUrlRewrite(Addonline_Brand_Model_Brand $brand) {
    	$id_path = "brand/".$brand->getId();
    	$request_path = $brand->getUrlKey();
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
    		->setStoreId(0);
    		 
    		$modelUrl->save();
    	}
    	
    }
    
}