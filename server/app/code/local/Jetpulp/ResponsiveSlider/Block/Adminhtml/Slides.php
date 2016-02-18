<?php 

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Slides extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_slides';
		$this->_blockGroup = 'responsiveslider';
		$this->_headerText = Mage::helper('responsiveslider')->__('Manage slides');
		$this->_addButtonLabel = Mage::helper('responsiveslider')->__('Add a slide');
		parent::__construct();
	}

}