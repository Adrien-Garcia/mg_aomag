<?php

class Addonline_AdvancedSlideshow_Block_Adminhtml_Gestion_Items_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('advancedslideshow_items_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('advancedslideshow')->__('Configuration'));
    }
    
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'     => Mage::helper('advancedslideshow')->__('Slide Information'),
			'title'     => Mage::helper('advancedslideshow')->__('Slide Information'),
			'content'   => $this->getLayout()->createBlock('advancedslideshow/adminhtml_gestion_items_edit_tab_form')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}
