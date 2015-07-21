<?php

/**
 * SEO tree Categories Sitemap block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Block_Catalog_Seo_Sitemap_Tree_Category extends Mage_Catalog_Block_Seo_Sitemap_Tree_Category
{
  
	
	/**
	 * Return collection of categories
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
	 */
	public function getTreeCollection()
	{

		$collection = parent::getTreeCollection();
		$collection->addAttributeToSelect('navigation_type');
		$collection->addAttributeToSelect('page_cms');
		return $collection;
	}

	public function getTemplate()
	{
		if ($this->_template == 'catalog/seo/tree.phtml') {
	    	$this->_template = 'jetpulp/category_navigation/tree.phtml';
	    }
	    return parent::getTemplate();
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
