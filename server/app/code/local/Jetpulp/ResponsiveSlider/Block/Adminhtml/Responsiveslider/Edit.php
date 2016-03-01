<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Responsiveslider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'responsiveslider_id';
        $this->_blockGroup = 'responsiveslider';
        $this->_controller = 'adminhtml_responsiveslider';

        parent::__construct();



        //if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('responsiveslider')->__('Save slider'));
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save',
            ), -100);
        //} else {
            //$this->_removeButton('save');
        //}

        //if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('responsiveslider')->__('Delete slider'));
        //} else {
        //    $this->_removeButton('delete');
        //}

        if (Mage::registry('cms_responsiveslider')->getId()) {
            $this->_addButton('addslide', array(
                    'label'     => Mage::helper('responsiveslider')->__('Add a slide'),
                    'onclick'       => 'setLocation(\''.$this->getUrl('*/responsiveslider_item/new/', array(
                                '_current'   => true,
                                'responsiveslider_id'       => Mage::registry('cms_responsiveslider')->getId()
                                )).'\')',
                    'class'     => 'save',
                ), -100);
        }

    }

    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/' . $this->_blockGroup . '/save');
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('cms_responsiveslider')->getId()) {
            return Mage::helper('responsiveslider')->__("Edit Slider '%s'", $this->escapeHtml(Mage::registry('cms_responsiveslider')->getTitle()));
        }
        else {
            return Mage::helper('responsiveslider')->__('New Slider');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return true;
        //return Mage::getSingleton('admin/session')->isAllowed('cms/responsiveslider/' . $action);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'active_tab' => '{{tab_id}}'
        ));
    }


    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('cms_responsiveslider_edit_tabs');
        if ($tabsBlock) {
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix   = $tabsBlock->getId() . '_';
        } else {
            $tabsBlockJsObject = 'responsiveslider_tabsJsTabs';
            $tabsBlockPrefix   = 'responsiveslider_tabs_';
        }

        $this->_formScripts[] = "
            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
                var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }
                var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
                var url = template.evaluate({tab_id:tabsIdValue});
                editForm.submit(url);
            }
        ";
        return parent::_prepareLayout();
    }

}
