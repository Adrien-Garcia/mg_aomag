<?php

class Addonline_SoColissimo_Block_Adminhtml_System_Config_Form_Field_Liberte extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

		return '<button type="button" class="scalable" onclick="window.open(\''.$this->getUrl('socolissimobatch/adminhtml_batch').'\');"><span>Lancer l\'import manuellement</span></button>'.$element->getElementHtml();

	}
}
