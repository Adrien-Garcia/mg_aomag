<?php 

class Addonline_AdvancedSlideshow_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_gestion';
		$this->_blockGroup = 'advancedslideshow';
		$this->_headerText = Mage::helper('advancedslideshow')->__('Slideshow Manager');
		$this->_addButtonLabel = Mage::helper('advancedslideshow')->__('Add Item');
		parent::__construct();
	}
  
}