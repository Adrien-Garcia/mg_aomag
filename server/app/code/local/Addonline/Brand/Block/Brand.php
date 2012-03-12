<?php
class Addonline_Brand_Block_Brand extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
    	$headBlock = $this->getLayout()->getBlock('head');
 
    	if($idMarque = $this->getRequest()->getParam("id")) {
    		$_brand = $this->getBrand($idMarque);
    		$headBlock->setTitle($_brand->getData('meta_title'));
    		$headBlock->setDescription($_brand->getData('meta_description'));
    		$headBlock->setKeywords($_brand->getData('meta_keyword'));
    	} 
		return parent::_prepareLayout();
    }
    
     public function getBrand($idMarque = 0)     
     { 
     	if($idMarque != 0) {
     		$_brand = Mage::getModel('brand/brand')->load($idMarque);
     		return $_brand;
    	} else {
    		throw new Exception("idMarque non renseign&eacute;");
    	}
     	
     }
        

    
    
    
}