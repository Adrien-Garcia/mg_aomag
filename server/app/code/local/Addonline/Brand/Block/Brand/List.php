<?php
class Addonline_Brand_Block_Brand_List extends Mage_Core_Block_Template
{
	public function _prepareLayout() 
	{
		$headBlock = $this->getLayout()->getBlock('head');
		
		$headBlock->setTitle("Toutes les marques de notre site");
		$headBlock->setDescription("Découvrez toutes les marques de notre site");
		
		return parent::_prepareLayout();
	}
	public function getBrandCollection()
	{
		$_brandCollection = Mage::getModel('brand/brand')->getCollection();
		 
		return $_brandCollection;
	}
}