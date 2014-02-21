<?php
/**
 * Addonline_GLS
 *
 * @category    Addonline
 * @package     Addonline_GLS
 * @copyright   Copyright (c) 2013 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_Gls_ImportController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Addonline_Gls');
    }

    /**
     * Main action : show import form
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('gls/import')
            ->_addContent($this->getLayout()->createBlock('gls/import_form'))
            ->renderLayout();
    }

    /**
     * Import Action
     */
    public function importAction()
    {
        $import = Mage::getModel('gls/import');
        $nbr_imported = $import->import();

        if($nbr_imported){
        	Mage::getSingleton('adminhtml/session')->addSuccess($nbr_imported.' '.$this->__('Orders have been imported'));
        }else{
        	Mage::getSingleton('adminhtml/session')->addError($this->__('No orders to import in the folder ').Mage::helper('gls')->getImportFolder());
        }
        $this->_redirect('*/*/');
    }


}