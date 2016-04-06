<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Responsiveslider_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Prepare form for tab
     *
     * @return string
     */
    protected function _prepareForm()
    {

        /* @var $model Jetpulp_ResponsiveSlider_Model_Responsiveslider */
        $model = Mage::registry('cms_responsiveslider');

        /*
         * Checking if user have permissions to save information
         */
        //if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        //} else {
        //    $isElementDisabled = true;
        //}

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('responsiveslider_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('responsiveslider')->__('Slider Information')));

        if ($model->getId()) {
            $fieldset->addField('responsiveslider_id', 'hidden', array(
                'name' => 'responsiveslider_id',
            ));
        }

        $fieldset->addField('identifier', 'text', array(
            'label'     => Mage::helper('responsiveslider')->__('Identifier'),
            'name'      => 'identifier',
            'class' 	=> 'required-entry',
            'required'  => true,
        ));

        $fieldset->addField('title', 'text', array(
	    	'label'     => Mage::helper('responsiveslider')->__('Title'),
	    	'name'      => 'title',
        	'class' 	=> 'required-entry',
	    	'required'  => true,
	    ));

        $fieldset->addField('baseline', 'text', array(
            'label'     => Mage::helper('responsiveslider')->__('Baseline'),
            'name'      => 'baseline',
            'required'  => false,
        ));

        /**
         * Check is single store mode */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('responsiveslider')->__('Store View'),
                'title'     => Mage::helper('responsiveslider')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled,
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('responsiveslider')->__('Status'),
            'title'     => Mage::helper('responsiveslider')->__('Slider Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => $model->getAvailableStatuses(),
            'disabled'  => $isElementDisabled,
        ));
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('responsiveslider')->__('Slider Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('responsiveslider')->__('Slider Information');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
*/

}