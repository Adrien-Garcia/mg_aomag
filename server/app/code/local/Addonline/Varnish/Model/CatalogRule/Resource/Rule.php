<?php

/**
 * Catalog rules resource model
 *
 * @category    Addonline
 * @package     Addonline_Varnish
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addonline_Varnish_Model_CatalogRule_Resource_Rule extends Mage_CatalogRule_Model_Resource_Rule
{

    /**
     * In Static mode, the customer Group is not used
     *
     * @param int|string $date
     * @param int $wId
     * @param int $gId
     * @param int $pId
     * @return float | false
     */
    public function getRulePrice($date, $wId, $gId, $pId)
    {
    	if(Mage::registry('varnish_static')) {
    		$gId = 0;
    	}
    	return parent::getRulePrice($date, $wId, $gId, $pId);
    }

}
