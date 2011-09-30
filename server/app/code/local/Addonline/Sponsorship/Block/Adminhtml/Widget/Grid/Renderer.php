<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Sponsorship_Block_Adminhtml_Widget_Grid_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		return ($this->_getImage($row));
	}
	
	protected function _getImage(Varien_Object $row)
	{
		$img = $row->image != '' ? '<img src="'.Mage::getBaseUrl('media').$row->image.'" alt="'.Mage::getBaseUrl('media').$row->image.'" />' : '';
		return $img;
	}	
}