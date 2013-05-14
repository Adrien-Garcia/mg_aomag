<?php

class Addonline_SoColissimo_Block_Adminhtml_System_Config_Form_Field_Config extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

		return $element->getElementHtml().'<a href="/doc-socolissimo-config.htm'.'" target="_blank">'.$this->__('Help to configure').'</a>';

	}
}
