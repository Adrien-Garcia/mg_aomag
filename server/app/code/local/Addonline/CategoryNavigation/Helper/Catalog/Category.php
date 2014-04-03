<?php

/**
 * Catalog category helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Helper_Catalog_Category extends Mage_Catalog_Helper_Category
{

    /**
     * Check if a category can be shown
     *
     * @param  Mage_Catalog_Model_Category|int $category
     * @return boolean
     */
    public function canShow($category)
    {
        if (is_int($category)) {
            $category = Mage::getModel('catalog/category')->load($category);
        }

        if (!$category->getId()) {
            return false;
        }

        if (!$category->getIsActive()) {
            return false;
        }
        if (!$category->isInRootCategoryList()) {
            return false;
        }
		//ADDONLINE : on faitune 404 pour les catégories non navigables ou page_cms
        if ($category->getNavigationType() != Addonline_CategoryNavigation_Model_Catalog_Category_Attribute_Source_Navigationtype::NORMAL) {
        	return false;
        }
        //FIN ADDONLINE
        return true;
    }

}
