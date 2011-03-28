<?php
require_once 'Mage/Catalog/controllers/ProductController.php';

class OrganicInternet_SimpleConfigurableProducts_AjaxController extends Mage_Catalog_ProductController
{
    public function coAction()
    {
       $product = $this->_initProduct();
       if (!empty($product)) {
           $this->_initProductLayout($product);
           $this->renderLayout();
       }
    }

    public function imageAction()
    {
       $product = $this->_initProduct();
       if (!empty($product)) {
           $this->_initProductLayout($product);
           $this->renderLayout();
       }
    }

    public function galleryAction()
    {
       $product = $this->_initProduct();
       if (!empty($product)) {
           #$this->_initProductLayout($product);
           $this->loadLayout();
           $this->renderLayout();
       }
    }

    //Copy of parent _initProduct but changes visibility checks.
    //Reproducing functionality like this is far from great for future compatibilty
    //but at the moment I don't see a better alternative.
    protected function _initProduct()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');
        $parentId   = (int) $this->getRequest()->getParam('pid');

        if (!$productId || !$parentId) {
            return false;
        }

        $parent = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($parentId);

        if (!Mage::helper('catalog/product')->canShow($parent)) {
            return false;
        }

        $childIds = $parent->getTypeInstance()->getUsedProductIds();
        if (!is_array($childIds) || !in_array($productId, $childIds)) {
            return false;
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
        // @var $product Mage_Catalog_Model_Product
        if (!$product->getId()) {
            return false;
        }
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            Mage::register('current_category', $category);
        }
        $product->setCpid($parentId);
        Mage::register('current_product', $product);
        Mage::register('product', $product);
        return $product;
    }
    
    /**
     * Initialize product view layout (méthode de Mage_Catalog_ProductController magento version 1.4.2)
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_ProductController
     */
    protected function _initProductLayout($product)
    {
        $design = Mage::getSingleton('catalog/design');
        $settings = $design->getDesignSettings($product);

        if ($settings->getCustomDesign()) {
            $design->applyCustomDesign($settings->getCustomDesign());
        }

        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();

        $update->addHandle('PRODUCT_TYPE_'.$product->getTypeId());
        $update->addHandle('PRODUCT_'.$product->getId());
        $this->loadLayoutUpdates();

        // apply custom layout update once layout is loaded
        if ($layoutUpdates = $settings->getLayoutUpdates()) {
            if (is_array($layoutUpdates)) {
                foreach($layoutUpdates as $layoutUpdate) {
                    $update->addUpdate($layoutUpdate);
                }
            }
        }

        $this->generateLayoutXml()->generateLayoutBlocks();
        // apply custom layout (page) template once the blocks are generated
        if ($settings->getPageLayout()) {
            $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
        }

        $currentCategory = Mage::registry('current_category');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('product-'.$product->getUrlKey());
            if ($currentCategory instanceof Mage_Catalog_Model_Category) {
                $root->addBodyClass('categorypath-'.$currentCategory->getUrlPath())
                    ->addBodyClass('category-'.$currentCategory->getUrlKey());
            }
        }
        return $this;
    }
}
