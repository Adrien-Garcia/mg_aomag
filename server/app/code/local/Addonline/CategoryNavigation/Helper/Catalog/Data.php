<?php


/**
 * Catalog data helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Helper_Catalog_Data extends Mage_Catalog_Helper_Data
{
    
    protected $_moduleName = 'Mage_Catalog';
    
  /**
     * Return current category path or get it from current category
     * and creating array of categories|product paths for breadcrumbs
     *
     * @return string
     */
    public function getBreadcrumbPath()
    {
        if (!$this->_categoryPath) {
            $path = array();
            if ($category = $this->getCategory()) {
                $pathInStore = $category->getPathInStore();
                $pathIds = array_reverse(explode(',', $pathInStore));

                $categories = $category->getParentCategories();

                // add category path breadcrumb
                foreach ($pathIds as $categoryId) {
                    if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                        //ADDONLINE : metre l'url de la page cms si elle est configurée
                        $pageCms = null;
                        if ($categories[$categoryId]->getNavigationType() == Addonline_CategoryNavigation_Model_Catalog_Category_Attribute_Source_Navigationtype::PAGE_CMS
                        &&  $categories[$categoryId]->getPageCms()) {
                            $pageCms =  $categories[$categoryId]->getPageCms();
                        }
                        //FIN ADDONLINE
                        $path['category'.$categoryId] = array(
                            'label' => $categories[$categoryId]->getName(),
                            'link' => $pageCms?Mage::getUrl('/').$pageCms:($this->_isCategoryLink($categoryId) ? $categories[$categoryId]->getUrl() : ''),
                            'navigation_type' => $categories[$categoryId]->getNavigationType()
                        );
                    }
                }
            }

            if ($this->getProduct()) {
                $path['product'] = array('label'=>$this->getProduct()->getName());
            }

            $this->_categoryPath = $path;
        }
        return $this->_categoryPath;
    }
}
