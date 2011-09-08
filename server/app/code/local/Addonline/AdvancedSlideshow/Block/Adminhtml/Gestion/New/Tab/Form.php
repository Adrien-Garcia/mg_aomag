<?php

class Addonline_AdvancedSlideshow_Block_Adminhtml_Gestion_New_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('advancedslideshow_form', array('legend'=>Mage::helper('advancedslideshow')->__('Information')));
        
        $fieldset->addField('title', 'text', array(
	    	'label'     => Mage::helper('advancedslideshow')->__('Title'),
	    	'name'      => 'title',
        	'class' 	=> 'required-entry',
	    	'required'  => true,
	    ));
	    
	    return parent::_prepareForm();
    }
    
}