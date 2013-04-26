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
class LaPoste_ExpeditorINet_ImportController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Constructor
     */
    protected function _construct()
    {        
        $this->setUsedModuleName('LaPoste_ExpeditorINet');
    }

    /**
     * Main action : show import form
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/expeditorinet/import')
            ->_addContent($this->getLayout()->createBlock('expeditorinet/import_form'))
            ->renderLayout();
    }

    /**
     * Import Action
     */
    public function importAction()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_expeditor_inet_file']['tmp_name'])) {
            try {
                $trackingTitle = $_POST['import_expeditor_inet_tracking_title'];
                Mage::getModel('expeditorinet/import')->importExpeditorInetFile($_FILES['import_expeditor_inet_file']['tmp_name'], $trackingTitle);
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->addError($this->__('Invalid file upload attempt'));
            }
        }
        else {
            $this->_getSession()->addError($this->__('Invalid file upload attempt'));
        }
        $this->_redirect('*/*/index');
    }


}