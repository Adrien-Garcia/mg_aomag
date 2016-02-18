<?php

class Addonline_Aomagento_Block_Adminhtml_System_Config_Form_Field_Keygeneration extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$_isAoMagento = Mage::getStoreConfig('addonline/licence/key_generation');
		
		$url = $this->getUrl('adminhtml/aomagento_generation/getlicence');
		
		$html = '<input type="text" name="hostname" class="hostname" /><span style="margin-left:10px;"></span>';
		$html .= '<input type="hidden" name="url_getlicence" class="url_getlicence" value="'.$url.'"/>';
		$html .= '<button type="button" class="scalable generate_key"><span>Lancer la génération</span></button>';
		$html .= '<div class="key_generated" style="margin-top:7px;"></div>';
		if($_isAoMagento) {
			$html .= '<input type="hidden" class="is_aomagento" value="1">';
		} else {
			$html .= '<input type="hidden" class="is_aomagento" value="0">';
		}
		return $html;
	}
}
