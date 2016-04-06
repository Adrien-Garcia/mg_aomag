<?php

class Jetpulp_ResponsiveSlider_Block_Adminhtml_Responsiveslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('responsiveslider_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('responsiveslider')->__('Slider Information'));
    }

    protected function _beforeToHtml()
    {
        $model = Mage::registry('cms_responsiveslider');
        if ($model->getId()) {
            $this->addTab('items', array(
                'label'     => Mage::helper('responsiveslider')->__('Slides'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/items', array('_current' => true)),
            ));
            $this->setActiveTab('items');
        }

        $this->_updateActiveTab();
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        /* magento logic would like we active the last edited tab,
         but here we prefer display slides grid
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
        */
    }

}
