<?php
/**
 * GLS
 *
 * @category    Addonline
 * @package     Addonline_GLS
 * @copyright   Copyright (c) 2013 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_Gls_Block_Export_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'gls';
        $this->_controller = 'export_orders';
        $this->_headerText = Mage::helper('gls')->__('Export');
        parent::__construct();
        $this->_removeButton('add');
    }

}
