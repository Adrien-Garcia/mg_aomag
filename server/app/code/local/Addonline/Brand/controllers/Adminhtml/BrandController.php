<?php

class Addonline_Brand_Adminhtml_BrandController extends Mage_Adminhtml_Controller_action
{

	/**
     * _isAllowed
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return Mage::getSingleton( 'admin/session' )->isAllowed( 'admin/catalog/brand' );
    }

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('brand/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Brands Manager'), Mage::helper('adminhtml')->__('Brand Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		if(Mage::getModel('brand/status')->_9cd4777ae76310fd6977a5c559c51820()){
			$this->_initAction()
				->renderLayout();
		} else{
			echo "Merci de renseigner la clé de licence du module";
		}
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('brand/brand')->load($id);

		if ($model->getId() || $id == 0) {
//			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
//			if (!empty($data)) {
//				$model->setData($data);
//			}

			Mage::register('brand_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('brand/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Brand Manager'), Mage::helper('adminhtml')->__('Brand Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Brand News'), Mage::helper('adminhtml')->__('Brand News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('brand/adminhtml_brand_edit'))
				->_addLeft($this->getLayout()->createBlock('brand/adminhtml_brand_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brand')->__('Brand does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setAllowCreateFolders(true);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS .'catalog'.DS.'brand'.DS;
					$uploader->save( $path, $_FILES['filename']['name'] );
					//copy( $path . $_FILES['filename']['name'], $path . 'catalog'.DS.'brand'.DS . $_FILES['filename']['name'] );
					//unlink( Mage::getBaseDir('media') . DS . $_FILES['filename']['name'] );
				} catch (Exception $e) {	
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];

			} else {
				if (isset($data['filename']['delete'])) {
					//this way the name is deleted in DB, and the file is deleted on the disk
					unlink (Mage::getBaseDir('media') . DS .$data['filename']['value']);
					$data['filename'] = '';
				} else {
					//this way the name is kept in DB
		  			unset($data['filename']);
				}
			}
	  			
	  			
			$model = Mage::getModel('brand/brand');	
			if(!$data['url_key']) {
				$data['url_key'] = $model->formatUrlKey($data['nom']);
			} else {
				$data['url_key'] = $model->formatUrlKey($data['url_key']);
			}
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				$model->saveUrlRewrite($model);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brand')->__('Brand was successfully saved'));

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brand')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('brand/brand');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Brand was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $brandIds = $this->getRequest()->getParam('brand');
        if(!is_array($brandIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select brand(s)'));
        } else {
            try {
                foreach ($brandIds as $brandId) {
                    $brand = Mage::getModel('brand/brand')->load($brandId);
                    $brand->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($brandIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $brandIds = $this->getRequest()->getParam('brand');
        if(!is_array($brandIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select brand(s)'));
        } else {
            try {
                foreach ($brandIds as $brandId) {
                    $brand = Mage::getSingleton('brand/brand')
                        ->load($brandId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($brandIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'brand.csv';
        $content    = $this->getLayout()->createBlock('brand/adminhtml_brand_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'brand.xml';
        $content    = $this->getLayout()->createBlock('brand/adminhtml_brand_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}