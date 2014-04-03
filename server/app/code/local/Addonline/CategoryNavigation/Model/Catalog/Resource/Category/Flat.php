<?php

/**
 * Category flat model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Model_Catalog_Resource_Category_Flat extends Mage_Catalog_Model_Resource_Category_Flat
{


    /**
     * Return parent categories of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @param unknown_type $isActive
     * @return array
     */
    public function getParentCategories($category, $isActive = true)
    {
        $categories = array();
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(
                array('main_table' => $this->getMainStoreTable($category->getStoreId())),
                array('main_table.entity_id', 'main_table.name', 'main_table.navigation_type', 'main_table.page_cms') //ADDONLINE, on ajoute navigation_type, page_cms
            )
            ->joinLeft(
                array('url_rewrite'=>$this->getTable('core/url_rewrite')),
                'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 AND '.
                $read->quoteInto('url_rewrite.product_id IS NULL AND url_rewrite.store_id=? AND ',
                $category->getStoreId() ).
                $read->prepareSqlCondition('url_rewrite.id_path', array('like' => 'category/%')),
                array('request_path' => 'url_rewrite.request_path'))
            ->where('main_table.entity_id IN (?)', array_reverse(explode(',', $category->getPathInStore())));
        if ($isActive) {
            $select->where('main_table.is_active = ?', '1');
        }
        $select->order('main_table.path ASC');
        mage::log($select->__toString());
        $result = $this->_getReadAdapter()->fetchAll($select);
        foreach ($result as $row) {
            $row['id'] = $row['entity_id'];
            $categories[$row['entity_id']] = Mage::getModel('catalog/category')->setData($row);
        }
        return $categories;
    }

}
