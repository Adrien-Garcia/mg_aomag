<?php


/**
 * Catalog Category navigation_type Attribute Source Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Addonline_CategoryNavigation_Model_Catalog_Category_Attribute_Source_Navigationtype extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    
    const NORMAL = 0;
    const UNNAVIGABLE = 1;
    const PAGE_CMS = 2;
    
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(array(
                'label' => Mage::helper('categorynavigation')->__('Normal'),
                'value' => self::NORMAL
            ), array(
                'label' => Mage::helper('categorynavigation')->__('Unnavigable'),
                'value' => self::UNNAVIGABLE
            ), array(
                'label' => Mage::helper('categorynavigation')->__('CMS Page'),
                'value' => self::PAGE_CMS
            ));
        }
        return $this->_options;
    }
}
