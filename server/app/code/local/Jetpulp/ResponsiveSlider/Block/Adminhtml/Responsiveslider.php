<?php 

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Responsiveslider extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_responsiveslider';
		$this->_blockGroup = 'responsiveslider';
		$this->_headerText = Mage::helper('responsiveslider')->__('Manage sliders');
		$this->_addButtonLabel = Mage::helper('responsiveslider')->__('Add a slider');
		parent::__construct();
	}

}