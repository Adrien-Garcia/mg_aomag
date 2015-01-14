<?php


/**
 * Catalog Observer
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Model_Catalog_Observer extends Mage_Catalog_Model_Observer
{
 
	
	/**
	 * Recursively adds categories to top menu
	 *
	 * @param Varien_Data_Tree_Node_Collection|array $categories
	 * @param Varien_Data_Tree_Node $parentCategoryNode
	 */
	protected function _addCategoriesToMenu($categories, $parentCategoryNode, $menuBlock, $addTags = false)
	{
		foreach ($categories as $category) {
			if (!$category->getIsActive()) {
				continue;
			}
	
			$nodeId = 'category-node-' . $category->getId();
			
			//ADDONLINE : metre l'url de la page cms si elle est configurée
			$pageCms = null;
			if ($category->getNavigationType() == Addonline_CategoryNavigation_Model_Catalog_Category_Attribute_Source_Navigationtype::PAGE_CMS 
                  && $category->getPageCms()) {
				$pageCms = $category->getPageCms();
			}
			//FIN ADDONLINE
			$tree = $parentCategoryNode->getTree();
			$categoryData = array(
					'name' => $category->getName(),
					'id' => $nodeId,
					'image' => $category->getImageUrl(),//$category->getImageUrl(),
					'url' => $pageCms?$menuBlock->getUrl('/').$pageCms:Mage::helper('catalog/category')->getCategoryUrl($category),
					'is_active' => $this->_isActiveMenuCategory($category),
					'navigation_type' => $category->getNavigationType() //ADDONLINE : on ajoute navigation_type au noeud
			);
			$categoryNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $parentCategoryNode);
			$parentCategoryNode->addChild($categoryNode);
	
			if (Mage::helper('catalog/category_flat')->isEnabled()) {
				$subcategories = (array)$category->getChildrenNodes();
			} else {
				$subcategories = $category->getChildren();
			}
	
			$this->_addCategoriesToMenu($subcategories, $categoryNode,$menuBlock,$addTags);
		}
	}
	
}
