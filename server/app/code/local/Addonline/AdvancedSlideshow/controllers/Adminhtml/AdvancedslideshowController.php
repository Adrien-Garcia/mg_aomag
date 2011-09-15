<?php

class Addonline_AdvancedSlideshow_Adminhtml_AdvancedslideshowController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('advancedslideshow/gestion')
			->_addBreadcrumb(Mage::helper('advancedslideshow')->__('Manage Slides'), Mage::helper('advancedslideshow')->__('Manage Slideshows'));
		return $this;
	}
	
	public function indexAction()
	{
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('advancedslideshow/adminhtml_grid'));
		$this->renderLayout();
	}
	
	public function newAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('advancedslideshow/gestion');
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('advancedslideshow/adminhtml_gestion_new'))
                 ->_addLeft($this->getLayout()->createBlock('advancedslideshow/adminhtml_gestion_new_tabs'));
		
		$this->renderLayout();
	}
	
	public function saveAction()
	{
		if ( $this->getRequest()->getPost() )
		{
			try {
				$postData = $this->getRequest()->getPost();
				$_advancedslideshowModel = Mage::getModel('advancedslideshow/advancedslideshow');
				$_advancedslideshowModel->setTitle( $postData['title'] )
					->save();
					
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('advancedslideshow')->__('Slideshow was successfully saved'));
				$this->_redirect('*/*/edit', array('id' => $_advancedslideshowModel->getId()));
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/new');
				return;
			}
		}
		
		$this->_redirect('*/*/');
	}
	
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id', false);
		Mage::register('cur_slideshow_id', $id);
		$this->loadLayout();
		$this->_setActiveMenu('advancedslideshow/gestion');
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('advancedslideshow/adminhtml_gestion_items_grid'));
		
		$this->renderLayout();
	}
	
	public function deleteAction()
	{
		$id = $this->getRequest()->getParam('id_slideshow', false);

		if( $id > 0 ) {
			try {
				$model = Mage::getModel('advancedslideshow/advancedslideshow');
				$model->setId($id)
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Slideshow was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $id));
			}
		}

	}
	
}