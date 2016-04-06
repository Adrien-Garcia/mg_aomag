<?php
class Jetpulp_ResponsiveSlider_Block_Adminhtml_Responsiveslider_Items_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{	

	public function render(Varien_Object $row)
	{
		$html = '';
		$filename = $this->_getValue($row);
		$mediaPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		
		if(file_exists($mediaPath.DS.$filename)) {
			
			$html = '<img src="'.$mediaUrl.$filename.'"';
			$get  = getimagesize($mediaPath.DS.$filename);
			if($get[1] > 150) {
				$html .= ' height="150"';
			}
			$html .= ' />';
		}
		
		return ($html);
	}
	
}