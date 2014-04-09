<?php

/**
 * SEO Categories Sitemap block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Block_Catalog_Seo_Sitemap_Category extends Mage_Catalog_Block_Seo_Sitemap_Category
{

    /**
     * Initialize categories collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('catalog/category');
        /* @var $helper Mage_Catalog_Helper_Category */
        $collection = $helper->getStoreCategories('name', true, false);
        //ADDONLINE : on ajoute les infos de navigation_type et page_cms à la requete et on exclue les catégories non navigables
        $collection->addAttributeToSelect('navigation_type');
        $collection->addAttributeToSelect('page_cms');
        $collection->addAttributeToFilter('navigation_type', array('neq' => 1) );
        $this->setCollection($collection);
        return $this;
    }

    /**
     * Get item URL
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getItemUrl($category)
    {
			//ADDONLINE : metre l'url de la page cms si elle est configurée
			$pageCms = null;
			if ($category->getNavigationType() == Addonline_CategoryNavigation_Model_Catalog_Category_Attribute_Source_Navigationtype::PAGE_CMS 
                  && $category->getPageCms()) {
				return  Mage::getUrl('/').$category->getPageCms();
			}
			//FIN ADDONLINE
    	
    	$helper = Mage::helper('catalog/category');
        /* @var $helper Mage_Catalog_Helper_Category */
        return $helper->getCategoryUrl($category);
    }

}
