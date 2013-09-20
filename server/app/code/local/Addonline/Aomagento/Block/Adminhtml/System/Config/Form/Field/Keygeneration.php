<?php

class Addonline_Aomagento_Block_Adminhtml_System_Config_Form_Field_Keygeneration extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$_isAoMagento = Mage::getStoreConfig('addonline/licence/key_generation');
		$html = '<input name="hostname" id="hostname" /><span style="margin-left:10px;"></span>';
		$html .= '<button type="button" class="scalable" id="generate_key"><span>Lancer la génération</span></button>';
		if($_isAoMagento) {
			$html .= '<input type="hidden" id="is_aomagento" value="1">';
		} else {
			$html .= '<input type="hidden" id="is_aomagento" value="0">';
		}
		$html .= '<div id="key_generated" style="margin-top:7px;"></div>';
		return $html;
	}
}
