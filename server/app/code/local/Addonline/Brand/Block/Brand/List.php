<?php
class Addonline_Brand_Block_Brand_List extends Mage_Core_Block_Template
{
	public function getBrandCollection()
	{
		$_brandCollection = Mage::getModel('brand/brand')->getCollection();
		 
		return $_brandCollection;
	}
}