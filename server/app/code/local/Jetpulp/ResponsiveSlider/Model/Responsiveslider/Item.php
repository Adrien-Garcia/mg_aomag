<?php

class Jetpulp_ResponsiveSlider_Model_Responsiveslider_Item extends Mage_Core_Model_Abstract
{
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('responsiveslider/responsiveslider_item');
    }

    /**
     * Prepare slide's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            Mage_Cms_Model_Page::STATUS_ENABLED => Mage::helper('responsiveslider')->__('Enabled'),
            Mage_Cms_Model_Page::STATUS_DISABLED => Mage::helper('responsiveslider')->__('Disabled'),
        ));

        return $statuses->getData();
    }

    public function getContentHtml() {
        $html = '';
        if ($this->getContent()) {
            /** @var $helper Mage_Cms_Helper_Data */
            $helper = Mage::helper('cms');
            $processor = $helper->getBlockTemplateProcessor();
            $html = $processor->filter($this->getContent());
        }
        return $html;
    }
}