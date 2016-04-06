<?php

class Jetpulp_ResponsiveSlider_Adminhtml_Responsiveslider_ItemController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Init actions
	 *
	 * @return Jetpulp_ResponsiveSlider_Adminhtml_Responsiveslider_ItemController
	 */
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('cms/responsiveslider')
			->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
			->_addBreadcrumb(
				Mage::helper('responsiveslider')->__('Responsive Sliders'),
				Mage::helper('responsiveslider')->__('Responsive Sliders')
			);
		return $this;
	}

	/**
	 * New CMS Slide
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Edit CMS Slide
	 */
	public function editAction()
	{

		$this->_title(Mage::helper('cms')->__('CMS'))->_title(Mage::helper('responsiveslider')->__('Responsive Sliders'));

		// 1. Get ID and create model
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('responsiveslider/responsiveslider_item');

		// 2. Initial checking
        if ($id) {
            //edit mode
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('responsiveslider')->__('This slide no longer exists.')
                );
                $this->_redirect('*/*/');

                return;
            }

        } else {
            //create mode
            $responsiveslider_id = $this->getRequest()->getParam('responsiveslider_id');
            $sliderModel = Mage::getModel('responsiveslider/responsiveslider');
            if ($responsiveslider_id) {
                $sliderModel->load($responsiveslider_id);
                if (!$sliderModel->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('responsiveslider')->__('This slider no longer exists.')
                    );
                    $this->_redirect('*/*/');

                    return;
                }

                $model->setResponsivesliderId($sliderModel->getId());
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Slide'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('cms_slides', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('responsiveslider')->__('Edit Slide') : Mage::helper('responsiveslider')->__('New Slide'),
                $id ? Mage::helper('responsiveslider')->__('Edit Slide') : Mage::helper('responsiveslider')->__('New Slide')
            )
            ->renderLayout();


	}

	/**
	 * Save action
	 */
	public function saveAction()
	{
		// check if data sent
		if ($data = $this->getRequest()->getPost()) {

			$id = $this->getRequest()->getParam('responsiveslider_id');
			$model = Mage::getModel('responsiveslider/responsiveslider')->load($id);
			if (!$model->getId() && $id) {
				Mage::getSingleton('adminhtml/session')->addError(
					Mage::helper('responsiveslider')->__('This slider no longer exists.')
				);
				$this->_redirect('responsiveslider/edit/', array('id' => $id));

				return;
			}

			$id = $this->getRequest()->getParam('id');
			$model = Mage::getModel('responsiveslider/responsiveslider_item');

            $model->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('responsiveslider')->__('This slide no longer exists.')
                );
                $this->_redirect('*/*/');

                return;
            }

            // init model and set data
            //process date values
            $data = $this->_filterDates($data, array('from_date', 'to_date'));
            //process image
            $mediaPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA);
            $slidesPath = 'responsiveslider';
            foreach ($_FILES as $key => $file) {
                if (isset($file['name']) and (file_exists($file['tmp_name']))) {
                    try {
                        $uploader = new Varien_File_Uploader($key);
                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $uploader->save($mediaPath.DS.$slidesPath, $file['name']);
                        $data[$key] = $slidesPath.'/'.$file['name'];
                    } catch (Exception $e) {

                    }
                } else {
                    if (isset($data[$key]['delete']) && $data[$key]['delete'] == 1) {
                        $data[$key] = '';
                    } else {
                        unset($data[$key]);
                    }
                }
            }

            //TODO gérer le cas du produit

            //
            $dataLink = array();
            $dataLink['responsiveslider_id'] = $data['responsiveslider_id'];
            unset($data['responsiveslider_id']);

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $item = $model->save();

                //link slide to slider
                if($item->getId()) {
                    $modelLink = Mage::getModel('responsiveslider/responsiveslider_link');
                    if(!$modelLink->isLinkIsExist($item->getId(), $dataLink['responsiveslider_id']) )  {
                        $dataLink['item_id'] = $item->getId();
                        $modelLink->setData($dataLink);
                        $modelLink->save();
                    }
                }

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('responsiveslider')->__('The slide has been saved')
                );
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getItemId()));
                    return;
                }
                // go to slider edit
                $this->_redirect('*/responsiveslider/edit', array('responsiveslider_id'=>$this->getRequest()->getParam('responsiveslider_id')));

                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect(
                    '*/*/edit',
                    array('responsiveslider_id' => $this->getRequest()->getParam('responsiveslider_id'))
                );

                return;
            }
		}
		$this->_redirect('*/*/');
	}

    /**
     * Delete Slide item action
     */
	public function deleteAction()
	{

        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('responsiveslider/responsiveslider_item');
                $model->load($id);
                $model->delete();

                //delete link to slider
                $this->_resetItemToSlidersLink();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('responsiveslider')->__('The slide has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/');

                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));

                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('responsiveslider')->__('Unable to find a slide to delete.')
        );
        // go to grid
        $this->_redirect('*/*/');
	}


    /**
     * Index action
     */
	public function indexAction()
	{
		$this->_redirect('*/responsiveslider/');
	}

    protected  function _resetItemToSlidersLink()
    {
        $id = $this->getRequest()->getParam('id');
        $itemsLinkCollection = Mage::getModel('responsiveslider/responsiveslider_link')->getCollection();
        $itemsLinkCollection->addFieldToFilter('item_id', $id);

        foreach($itemsLinkCollection as $itemsLink) {
            $itemsLink->delete();
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/cms/responsiveslider/sliders');
    }

}