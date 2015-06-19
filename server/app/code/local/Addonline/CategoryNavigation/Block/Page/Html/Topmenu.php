<?php

/**
 * Top menu block
 *
 * @category    Mage
 * @package     Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Block_Page_Html_Topmenu extends Mage_Page_Block_Html_Topmenu
{

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Varien_Data_Tree_Node $menuTree
     * @param string $childrenWrapClass
     * @return string
     */
    protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $html = '';
        
        $isClient4 = Mage::getSingleton('core/design_package')->getPackageName() == "CLIENT-4";
        
        /*
         * la variable personnalisée 'include_category_thumb_in_navigation_submenu' à TRUE
         * indique que la miniature de la catégorie de 1er niveau
         * doit être affichée dans le sous megnou
         */ 
        $includeThumb = Mage::getModel('core/variable')->setStoreId(Mage::app()->getStore()->getId())->loadByCode('include_category_thumb_in_navigation_submenu')->getValue('text')=='true'?true:false;
        
        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';
        //if( $childLevel == 1 && $includeThumb) $html .="<div>";
        foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            //ADDONLINE
            if ($child->getNavigationType() == Addonline_CategoryNavigation_Model_Catalog_Category_Attribute_Source_Navigationtype::UNNAVIGABLE) {
	            $html .= '<a ' . $outermostClassCode . '><span>';
            } else {
            	$html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>';
            }
			//FIN ADDONLINE
            $html .= $this->escapeHtml($child->getName()) . '</span></a>';

            if ($child->hasChildren()) {
                if (!empty($childrenWrapClass)) {
                    $html .= '<div class="' . $childrenWrapClass . '">';
                }
                
                if( $includeThumb && $childLevel == 0 && $child->getThumbnail() ) {
                    $html .= '<ul class="level' . $childLevel . '  with-thumb">';
                } else {
                    $html .= '<ul class="level' . $childLevel . '">';
                }

                $html .= $this->_getHtml($child, $childrenWrapClass);

                // on ajoute si besoin un li pour la miniature
                if( $includeThumb && $childLevel == 0 && $child->getThumbnail() ) {
                    $html .= "<li class='img-category'><img class='visuel' src='". $child->getThumbnail() ."' /></li>";
                }

                $html .= '</ul>';

                if (!empty($childrenWrapClass)) {
                    $html .= '</div>';
                }

            }
             
            $html .= '</li>';

            $counter++;
        }
        //if( $childLevel == 1 && $includeThumb) $html .= "</div>";
        return $html;
    }


}
