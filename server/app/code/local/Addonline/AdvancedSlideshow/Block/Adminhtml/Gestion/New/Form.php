<?php

class Addonline_AdvancedSlideshow_Block_Adminhtml_Gestion_New_Form extends Mage_Adminhtml_Block_Widget_Form
{
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
        		'id' => 'edit_form', 
        		'action' => $this->getUrl('*/*/save'), 
        		'method' => 'post'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        $this->setDestElementId('new_form');
        return parent::_prepareForm();
    }

}
