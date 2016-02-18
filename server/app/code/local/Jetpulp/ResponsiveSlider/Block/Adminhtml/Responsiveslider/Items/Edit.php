<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Responsiveslider_Items_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'responsiveslider';
        $this->_controller = 'adminhtml_responsiveslider_items';
        $this->_updateButton('save', 'label', Mage::helper('responsiveslider')->__('Save slide'));

    }

    public function getHeaderText()
    {
        $model = Mage::registry('cms_responsiveslider_item');
    	if ($model && $model->getId())
    	{
    		return Mage::helper('responsiveslider')->__("Edit Slide %s", $this->htmlEscape($model->getId()));
    	}
    	else
    	{
    		return Mage::helper('responsiveslider')->__('New Slide');
    	}
    }
    
    public function getBackUrl()
    {

        return $this->getUrl('*/responsiveslider/edit', array('responsiveslider_id'=>$this->getRequest()->getParam('responsiveslider_id')));
    }
    
}
