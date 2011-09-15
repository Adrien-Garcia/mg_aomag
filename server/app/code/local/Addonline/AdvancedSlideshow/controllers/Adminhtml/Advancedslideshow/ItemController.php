<?php

class Addonline_AdvancedSlideshow_Adminhtml_Advancedslideshow_ItemController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('advancedslideshow/gestion')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function editAction()
	{
		$_slideitem_id = $this->getRequest()->getParam('id');
		$_id_slideshow = $this->getRequest()->getParam('id_slideshow');
		
        Mage::register('cur_slideitem_id', $_slideitem_id);
        Mage::register('cur_slideshow_id', $_id_slideshow);
        
        $model = Mage::getModel('advancedslideshow/advancedslideshow_item');
	    if ($_slideitem_id) {
            $model->load($_slideitem_id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This slide no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
		
        $this->_title($model->getId() ? $this->__('Edit Slide').' : '.$model->getId() : $this->__('New Item'));
        
        
        Mage::register('advancedslideshow_item', $model);
		
		$this->loadLayout();
		$this->_setActiveMenu('advancedslideshow/gestion');
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('advancedslideshow/adminhtml_gestion_items_edit'))
			->_addLeft($this->getLayout()->createBlock('advancedslideshow/adminhtml_gestion_items_edit_tabs'));
		
		$this->renderLayout();
	}
	
	public function saveAction()
	{
		if ( $data = $this->getRequest()->getPost() )
		{
			$data = $this->_filterDates($data, array('from_date', 'to_date'));
			if(isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name'])))
			{
				try {
					$uploader = new Varien_File_Uploader('image');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['image']['name']);
					$data['image'] = $_FILES['image']['name'];
				}catch(Exception $e) {
					
				}
			}
			else
			{
				if(isset($data['image']['delete']) && $data['image']['delete'] == 1)
				{
					$data['image'] = '';
				}
				else
				{
					unset($data['image']);
				}
			}
			$model = Mage::getModel('advancedslideshow/advancedslideshow_item');
			$id = $this->getRequest()->getParam('id');
			if ($id)
			{
                $model->load($id);
            }
            
			try {
				$_id_slideshow = $data['id_slideshow'];
				if ($id)
				{
					$model->setId($id);
				}
				else
				{
					unset($data['id']);
					
				}
				//TODO : vérifier l'existence du produit corerspondant au SKU, vider l'url saisie ... 
				
				$model->setData($data);
				$model->save();
				
				if (!$model->getId())
				{
                    Mage::throwException(Mage::helper('advancedslideshow')->__('Error saving item'));
                }
					
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('advancedslideshow')->__('Slide was successfully saved'));
				
				if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/adminhtml_advancedslideshow/edit', array('id' => $_id_slideshow));
                }
				
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/new');
			}
			return;
		}
		
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancedslideshow')->__('No data found to save.'));
		$this->_redirect('*/*/', array('id' => $model->getId()) );
	}
	
	public function deleteAction()
	{
		$_slideitem_id = $this->getRequest()->getParam('id');
		
		if( $_slideitem_id > 0 ) {
			try {
				$model = Mage::getModel('advancedslideshow/advancedslideshow_item');
				$model->load($_slideitem_id);
				$model->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Slide was successfully deleted'));
				Mage::log($this->getUrl('*/adminhtml_advancedslideshow/edit/', array('id' => $model->getIdSlideshow())));
				$this->_redirect('*/adminhtml_advancedslideshow/edit/', array('id' => $model->getIdSlideshow()));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $id));
			}
		}

	}
	
	
	
	public function indexAction()
	{
		$this->_redirect('*/*/');
	}

}