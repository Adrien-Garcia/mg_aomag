<?php

class Addonline_AdvancedSlideshow_Block_Adminhtml_Gestion_Items_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'advancedslideshow';
        $this->_controller = 'adminhtml_gestion_items';
		/*
        $this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                  'onclick' => 'saveAndContinueEdit()',
                  'class' => 'save',
        ), -100);
		$this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
*/
    }
    
    public function getHeaderText()
    {
    	if (Mage::registry('advancedslideshow_item') && Mage::registry('advancedslideshow_item')->getId())
    	{
    		return Mage::helper('advancedslideshow')->__("Edit Slide %s", $this->htmlEscape(Mage::registry('advancedslideshow_item')->getId()));
    	}
    	else
    	{
    		return Mage::helper('advancedslideshow')->__('New Slide');
    	}
    }
    
    public function getBackUrl()
    {
    	$_id_slideshow = Mage::registry('cur_slideshow_id');
        return $this->getUrl('*/adminhtml_advancedslideshow/edit', array('id'=>$_id_slideshow));
    }
    
}
