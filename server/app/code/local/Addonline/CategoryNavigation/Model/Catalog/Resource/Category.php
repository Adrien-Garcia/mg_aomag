<?php

/**
 * Catalog category model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Model_Catalog_Resource_Category extends Mage_Catalog_Model_Resource_Category
{

    /**
     * Return parent categories of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getParentCategories($category)
    {
        $pathIds = array_reverse(explode(',', $category->getPathInStore()));
        $categories = Mage::getResourceModel('catalog/category_collection')
            ->setStore(Mage::app()->getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')//ajout image category
            ->addAttributeToSelect('thumbnail')//ajout image category
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('navigation_type') //ADDONLINE ajout attribut navigation_type (utilisé pour le breadcrum)
            ->addAttributeToSelect('page_cms') //ADDONLINE ajout attribut navigation_type (utilisé pour le breadcrum)
            ->addFieldToFilter('entity_id', array('in' => $pathIds))
            ->addFieldToFilter('is_active', 1)
            ->load()
            ->getItems();
        
        return $categories;
    }
}
