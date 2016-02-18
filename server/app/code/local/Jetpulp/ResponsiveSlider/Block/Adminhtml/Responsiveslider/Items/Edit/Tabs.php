<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Responsiveslider_Items_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('responsiveslider_items_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('responsiveslider')->__('Configuration'));
    }
    
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'     => Mage::helper('responsiveslider')->__('Slide Information'),
			'title'     => Mage::helper('responsiveslider')->__('Slide Information'),
			'content'   => $this->getLayout()->createBlock('responsiveslider/adminhtml_responsiveslider_items_edit_tab_form')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}
