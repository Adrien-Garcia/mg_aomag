<?php

class Addonline_Brand_Block_Adminhtml_Brand_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  
	
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('brand_form', array('legend'=>Mage::helper('brand')->__('Brand information')));

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

      //$fieldset->addType('image_brand', Mage::getConfig()->getBlockClassName('brand/adminhtml_form_element_image'));
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

      $fieldset->addField('url_key', 'text', array(
          'label'     => Mage::helper('brand')->__('Url Key'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'url_key',
          'note'	  => Mage::helper('brand')->__("Please enter a lower-case value"),
      ));
     
	 if ( Mage::registry('brand_data') ) {
      		$brand = Mage::registry('brand_data');
      		if ($brand->getFilename()) {
	      		$brand->setFilename('catalog/brand/'.$brand->getFilename());
      		}
	      	$form->setValues($brand->getData());
      }
	  
      return parent::_prepareForm();
  }
  
    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('brand/adminhtml_form_element_image')
        );
    }
}