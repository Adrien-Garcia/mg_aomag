<?php

class Jetpulp_ResponsiveSlider_Adminhtml_ResponsivesliderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Jetpulp_ResponsiveSlider_Adminhtml_ResponsivesliderController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/responsiveslider/sliders')
            ->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
            ->_addBreadcrumb(
                Mage::helper('responsiveslider')->__('Responsive Sliders'),
                Mage::helper('responsiveslider')->__('Responsive Sliders')
            );

        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('cms')->__('CMS'))->_title($this->__('Responsive Sliders'));

        $this->_initAction();
        //$this->_addContent($this->getLayout()->createBlock('responsiveslider/adminhtml_grid'));
        $this->renderLayout();
    }

    /**
     * Index action
     */
    public function slidesAction()
    {
        $this->_title(Mage::helper('cms')->__('CMS'))->_title($this->__('Responsive Sliders'))->_title($this->__('Slides'));
        $this->loadLayout()
            ->_setActiveMenu('cms/responsiveslider/slides')
            ->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
            ->_addBreadcrumb(
                Mage::helper('responsiveslider')->__('Responsive Sliders'),
                Mage::helper('responsiveslider')->__('Responsive Sliders')
            )->_addBreadcrumb(
                Mage::helper('responsiveslider')->__('Slides'),
                Mage::helper('responsiveslider')->__('Slides')
            );
        $this->renderLayout();
    }

    /**
     * Create new CMS Slider
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit CMS Slider
     */
    public function editAction()
    {
        $this->_title(Mage::helper('cms')->__('CMS'))->_title($this->__('Responsive Sliders'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('responsiveslider_id');
        $model = Mage::getModel('responsiveslider/responsiveslider');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('responsiveslider')->__('This slider no longer exists.')
                );
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Slider'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('cms_responsiveslider', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('responsiveslider')->__('Edit Slider') : Mage::helper('responsiveslider')->__('New Slider'),
                $id ? Mage::helper('responsiveslider')->__('Edit Slider') : Mage::helper('responsiveslider')->__('New Slider')
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
                $this->_redirect('*/*/');

                return;
            }

            // init model and set data
            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();

                //save link
                if( isset($data['in_slider_items']) && !empty($data['in_slider_items'])) {
                    $position = $this->getRequest()->getParam('position');
                    $this->_resetSliderToItemsLink();
                    foreach($data['in_slider_items'] as $slide) {
                        if( is_string($slide) AND $slide === 'on' ){
                            continue;
                        }
                        $modelLink = Mage::getModel('responsiveslider/responsiveslider_link');
                        $modelLink->setItemId($slide);
                        $modelLink->setResponsivesliderId($id);
                        $modelLink->setSortOrder($position[$slide]);
                        $modelLink->save();
                    }
                }

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('responsiveslider')->__('The slider has been saved.')
                );
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('responsiveslider_id' => $model->getId()));

                    return;
                }
                // go to grid
                $this->_redirect('*/*/');

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
     * Slider items grid
     *
     */
    public function itemsAction() {

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('responsiveslider_id');
        $model = Mage::getModel('responsiveslider/responsiveslider');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('responsiveslider')->__('This slider no longer exists.')
                );
                $this->_redirect('*/*/');

                return;
            }
        }

        // 4. Register model to use later in blocks
        Mage::register('cms_responsiveslider', $model);

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('responsiveslider_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('responsiveslider/responsiveslider');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();

                //delete all link
                $this->_resetSliderToItemsLink();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('responsiveslider')->__('The slider has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/');

                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('responsiveslider_id' => $id));

                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('responsiveslider')->__('Unable to find a slider to delete.')
        );
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $slidersGrid = $this->getLayout()->createBlock('responsiveslider/widget_slider_chooser', '', array(
            'id' => $uniqId,
        ));

        $this->getResponse()->setBody($slidersGrid->toHtml());
    }

    protected function _resetSliderToItemsLink()
    {
        $id = $this->getRequest()->getParam('responsiveslider_id');
        $itemsLinkCollection = Mage::getModel('responsiveslider/responsiveslider_link')->getCollection();
        $itemsLinkCollection->addFieldToFilter('responsiveslider_id', $id);

        foreach($itemsLinkCollection as $itemsLink) {
            $itemsLink->delete();
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/cms/responsiveslider/sliders');
    }

}