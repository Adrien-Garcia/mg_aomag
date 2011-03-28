<?php

class Addonline_Brand_Block_Adminhtml_Brand_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('brand_form', array('legend'=>Mage::helper('brand')->__('Item information')));

      
      $fieldset->addField('nom', 'text', array(
          'label'     => Mage::helper('brand')->__('Nom'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'nom',
      ));
      
      $fieldset->addField('bloc_cms', 'select', array(
          'label'     => Mage::helper('brand')->__('Bloc cms'),
          'name'      => 'bloc_cms',
          'values'    => Mage::getModel('brand/attribute_source_bloccms')->getAllOptions()
      )); 

      $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('brand')->__('Logo'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('brand')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('brand')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('brand')->__('Disabled'),
              ),
          ),
      ));      

      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('brand')->__('Description'),
          'title'     => Mage::helper('brand')->__('Description'),
          'style'     => 'width:400px; height:200px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));
      
      $fieldset->addField('meta_title', 'text', array(
          'label'     => Mage::helper('brand')->__('Meta title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'meta_title',
      ));   
            
      $fieldset->addField('meta_description', 'editor', array(
          'name'      => 'meta_description',
          'label'     => Mage::helper('brand')->__('Meta description'),
          'title'     => Mage::helper('brand')->__('Meta description'),
          'style'     => 'width:400px; height:200px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));

      $fieldset->addField('meta_keyword', 'editor', array(
          'name'      => 'meta_keyword',
          'label'     => Mage::helper('brand')->__('Meta keyword'),
          'title'     => Mage::helper('brand')->__('Meta keyword'),
          'style'     => 'width:400px; height:200px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));      
     
      if ( Mage::getSingleton('adminhtml/session')->getBrandData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBrandData());
          Mage::getSingleton('adminhtml/session')->setBrandData(null);
      } elseif ( Mage::registry('brand_data') ) {
          $form->setValues(Mage::registry('brand_data')->getData());
      }
      return parent::_prepareForm();
  }
}