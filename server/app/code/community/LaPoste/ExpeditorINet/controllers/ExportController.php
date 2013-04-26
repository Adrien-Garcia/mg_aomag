<?php
/**
 * LaPoste_ExpeditorINet
 * 
 * @category    LaPoste
 * @package     LaPoste_ExpeditorINet
 * @copyright   Copyright (c) 2010 La Poste
 * @author 	    Smile (http://www.smile.fr) & Jibé
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LaPoste_ExpeditorINet_ExportController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Constructor
     */
    protected function _construct()
    {        
        $this->setUsedModuleName('LaPoste_ExpeditorINet');
    }

    /**
     * Main action : show orders list
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/expeditorinet/export')
            ->_addContent($this->getLayout()->createBlock('expeditorinet/export_orders'))
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

        if (!empty($orderIds)) {
            
        	$collection = Mage::getResourceModel('sales/order_collection');
        	$collection->addAttributeToFilter('entity_id', $orderIds);
        	
        	$export = Mage::getModel('expeditorinet/export');
        	$export->export($collection);

            /* download the file */
            return $this->_prepareDownloadResponse($export->filename, $export->content, $export->fileMimeType .'; charset="'. $export->fileCharset .'"');
        }
        else {
	        $this->_getSession()->addError($this->__('No Order has been selected'));
        }
    }



}
