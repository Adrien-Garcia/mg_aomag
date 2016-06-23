<?php
class Jetpulp_ResponsiveSlider_Block_Responsiveslider extends Mage_Catalog_Block_Product_Abstract
{
    
    public function setSlider($id)
    {
    	$this->setData("id", $id);
    }

	public function setSliderIdentifier($identifier)
	{
		$this->setData("identifier", $identifier);
	}
    
	public function getResponsiveslider()
	{
		$id = $this->getData("id");
		if ( isset($id) && $id != null ) {
			$model = Mage::getModel('responsiveslider/responsiveslider')->load($id);
		} else {
			$model = Mage::getModel('responsiveslider/responsiveslider')->loadByIdentifier($this->getData("identifier"));
		}

		if (!$model->getId() && $id) {
			return null;
		}
		return $model;
	}
}