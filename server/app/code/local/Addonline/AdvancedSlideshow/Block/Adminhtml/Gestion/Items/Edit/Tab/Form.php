<?php

class Addonline_AdvancedSlideshow_Block_Adminhtml_Gestion_Items_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('advancedslideshow_slide_form', array('legend'=>Mage::helper('advancedslideshow')->__('Information')));
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        
        if( Mage::registry('advancedslideshow_item') )
        {
        	$data = Mage::registry('advancedslideshow_item');
        }
        else
        {
        	$data = array();
        }
        $_id_slideshow = Mage::registry('cur_slideshow_id');
        $data['id_slideshow'] = $_id_slideshow;
        
        $fieldset->addField('id_slideshow', 'hidden', array(
	    	'label'     => '',
	    	'name'      => 'id_slideshow',
	    ));
        $fieldset->addField('id', 'hidden', array(
	    	'label'     => '',
	    	'name'      => 'id',
	    ));
	    
	    
        $fieldset->addField('from_date', 'date', array(
	    	'label'     => Mage::helper('advancedslideshow')->__('From Date'),
	    	'name'      => 'from_date',
            'image'  	=> $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
	    ));
        $fieldset->addField('to_date', 'date', array(
	    	'label'     => Mage::helper('advancedslideshow')->__('To Date'),
	    	'name'      => 'to_date',
            'image'  	=> $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
	    ));
	    
        $fieldset->addField('url', 'text', array(
	    	'label'     => Mage::helper('advancedslideshow')->__('URL link'),
	    	'name'      => 'url',
        	'class' 	=> 'required-entry',
	    	'required'  => true,
	    ));
	    
	    
        $fieldset->addField('image', 'image', array(
	    	'label'     => Mage::helper('advancedslideshow')->__('Background image'),
	    	'name'      => 'image',
	    	'required'  => true,
	    ));
	    
	    
        $fieldset->addField('product_sku', 'text', array(
	    	'label'     => Mage::helper('advancedslideshow')->__('Linked product SKU'),
	    	'name'      => 'product_sku',
	    ));

        $fieldset->addField('sort_order', 'text', array(
	    	'label'     => Mage::helper('advancedslideshow')->__('Position'),
	    	'name'      => 'sort_order',
	    ));
	    $form->setValues($data);
	    
	    
	    return parent::_prepareForm();
    }
    
}