<?php
/**
 * Catalog category flat collection
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_Catalog_Model_Resource_Category_Flat_Collection extends Mage_Catalog_Model_Resource_Category_Flat_Collection
{

    /**
     * Enter description here ...
     *
     * @param unknown_type $paths
     * @return Mage_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
 		//ADDONLINE : correction bug magento : les OR n'étaient pas mis entre parenthèses
        $read  = $this->getResource()->getReadConnection();
        $cond   = array();
        foreach ($paths as $path) {
        	$cond[] = $read->quoteInto('main_table.path LIKE ?', "$path%");
        }
        if ($cond) {
        	$this->getSelect()->where(join(' OR ', $cond));
        }
        //ADDONLINE : correction bug magento
        Mage::log($this->getSelect()->__toString());
        return $this;
    }

}
