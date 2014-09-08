<?php

class Addonline_GUATracker_Model_System_Config_Source_Addto
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'head', 'label'=>Mage::helper('addonline_guatracker')->__('Head')),
            array('value' => 'before_body_end', 'label'=>Mage::helper('addonline_guatracker')->__('Before Body End')),
        );
    }
}