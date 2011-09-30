<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Sponsorship_Model_Mysql4_CatalogFidelityPoint extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the sponsorship_id refers to the key field in your database table.
        $this->_init('sponsorship/catalogfidelitypoint', 'rule_product_fidelity_point_id');
    }
}