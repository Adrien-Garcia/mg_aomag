<?php
/**
 * Addonline_GLS
 *
 * @category    Addonline
 * @package     Addonline_GLS
 * @copyright   Copyright (c) 2013 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_Gls_ExportController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Addonline_Gls');
    }

    /**
     * Main action : show orders list
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('gls/export')
            ->_addContent($this->getLayout()->createBlock('gls/export_orders'))
            ->renderLayout();
    }

    /**
     * Export Action
     * Generates a CSV file to download
     */
    public function exportAction()
    {
	    /* get the orders */
        $orderIds = $this->getRequest()->getPost('order_ids');

        if (isset($orderIds) && ($orderIds[0] != "")) {

        	$collection = Mage::getResourceModel('sales/order_collection');
        	$collection->addAttributeToFilter('entity_id', $orderIds);

        	$export = Mage::getModel('gls/export');
        	$export->export($collection);

            /* download the file */
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Orders have been exported'));
            $this->_redirect('*/*/');
        }
        else {
	        $this->_getSession()->addError($this->__('No Order has been selected'));
	        $this->_redirect('*/*/');
        }
    }



}
