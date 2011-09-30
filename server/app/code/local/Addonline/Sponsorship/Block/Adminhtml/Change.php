<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Sponsorship_Block_Adminhtml_Change extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_change';
		$this->_blockGroup = 'sponsorship';
		$this->_headerText = $this->__('Points exchanges list');
		parent::__construct();
		$this->removeButton('add');
	}
}