<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Sponsorship_Block_Adminhtml_Sponsorship extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {  	
    $this->_controller = 'adminhtml_sponsorship';
    $this->_blockGroup = 'sponsorship';
    $this->_headerText = $this->__('Invitations list');
    parent::__construct();
    $this->removeButton('add');    
  }
}