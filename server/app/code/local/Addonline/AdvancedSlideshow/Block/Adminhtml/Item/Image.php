<?php
class Addonline_AdvancedSlideshow_Block_Adminhtml_Item_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{	
	//Permet d'afficher un picto dans le cas où la quantité de sécurité est supérieure à la quantité en stock
	public function render(Varien_Object $row)
	{
		$html = '';
		$filename = $row->getData('image');
		$media_path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$media_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		
		if(file_exists($media_path.DS.$filename)) {
			
			$html = '<img src="'.$media_url.$filename.'"';
			$get  = getimagesize($media_path.DS.$filename);
			if($get[1] > 150) {
				$html .= ' height="150"';
			}
			$html .= ' />';
		}
		
		return ($html);
	}
	
}