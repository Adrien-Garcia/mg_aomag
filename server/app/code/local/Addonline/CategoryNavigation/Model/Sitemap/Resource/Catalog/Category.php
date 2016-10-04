<?php


/**
 * Sitemap resource catalog collection model
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Model_Sitemap_Resource_Catalog_Category extends Mage_Sitemap_Model_Resource_Catalog_Category
{

    /**
     * Get category collection array
     *
     * @param unknown_type $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        
        /* @var $store Mage_Core_Model_Store */
        $store = Mage::app()->getStore($storeId);
        if (!$store) {
            return false;
        }

        $this->_select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getIdFieldName() . '=?', $store->getRootCategoryId());

        $categoryRow = $this->_getWriteAdapter()->fetchRow($this->_select);
        if (!$categoryRow) {
            return false;
        }

        $this->_select = $this->_getWriteAdapter()->select()
            ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName()))
            ->where('main_table.path LIKE ?', $categoryRow['path'] . '/%');

        $storeId = (int)$store->getId();

        /** @var $urlRewrite Mage_Catalog_Helper_Category_Url_Rewrite_Interface */
        $urlRewrite = $this->_factory->getCategoryUrlRewriteHelper();
        $urlRewrite->joinTableToSelect($this->_select, $storeId);

        $this->_addFilter($storeId, 'is_active', 1);
        //ADDONLINE
        $this->_addFilter($storeId, 'navigation_type', 0);
        //ADDONLINE

        return $this->_loadEntities();
            
    }
}
