<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Sponsorship_SponsorController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
    {
        parent::preDispatch();
    }
    
	public function indexAction()
    {
    	/*
    	 * Cette page est obsolète, elle est gardée par souci de compatibilité avec l'affiliation
    	 * Le code est dorénavent déclanché dans affiliate() de Addonline_Sponsorship_Model_Observer
        */
    	$this->_redirect('');
    }
    
    public function detailAction()
    {
    	$this->loadLayout();
    	$this->getLayout()->getBlock('customer');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
}