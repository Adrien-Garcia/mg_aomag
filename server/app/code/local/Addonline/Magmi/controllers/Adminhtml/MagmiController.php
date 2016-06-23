<?php

class Addonline_Magmi_Adminhtml_MagmiController extends Mage_Adminhtml_Controller_Action {

	/**
	 * _isAllowed
	 *
	 * @return boolean
	 */
	public function _isAllowed()
	{
		return Mage::getSingleton( 'admin/session' )->isAllowed( 'admin/system/magmi' );
	}

	public function indexAction()
	{

		
		$this->_title($this->__('System'))->_title($this->__('Magmi'));
				
		$this->loadLayout();
		
		$this->_setActiveMenu('system/magmi');
		
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('Magmi'), $this->getUrl('*/system'));
		
		$this->_addContent($this->getLayout()->createBlock('magmi/magmi'));
		
		$this->renderLayout();
		
		return $this;
	}
	

	
}