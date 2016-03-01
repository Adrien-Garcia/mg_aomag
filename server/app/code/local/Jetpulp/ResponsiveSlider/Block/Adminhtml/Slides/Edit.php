<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Slides_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {

        parent::__construct();

        $this->_objectId = 'item_id';
        $this->_blockGroup = 'responsiveslider';
        $this->_controller = 'adminhtml_slides';

        $objId = $this->getRequest()->getParam($this->_objectId);
        if (! empty($objId)) {
            $this->_addButton('delete', array(
                'label' => Mage::helper('adminhtml')->__('Delete'),
                'class' => 'delete',
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    . '\', \'' . $this->getDeleteUrl() . '\')',
            ));
        }

        $this->_updateButton('save', 'label', Mage::helper('responsiveslider')->__('Save slide'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

    }

    public function getHeaderText()
    {
        $model = Mage::registry('cms_slides');
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
        return $this->getUrl('*/slides/');
    }

    public function getDeleteUrl()
    {
        $model = Mage::registry('cms_slides');
        return $this->getUrl('*/slides/delete/', array('item_id'=>$model->getId()));
    }

    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
        return parent::_prepareLayout();
    }
}
