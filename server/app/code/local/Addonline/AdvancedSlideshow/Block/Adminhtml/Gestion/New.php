<?php

class Addonline_AdvancedSlideshow_Block_Adminhtml_Gestion_New extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'advancedslideshow';
        $this->_controller = 'adminhtml_gestion';
        $this->_mode = 'new';
        $this->_headerText = Mage::helper('advancedslideshow')->__('Add a slideshow');
    }

}
