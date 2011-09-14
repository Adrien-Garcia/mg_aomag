<?php
/**
 * Addonline_SoColissimoLiberte
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimoLiberte
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_SoColissimoLiberte_Model_Mysql4_PeriodesFermeture_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct() {
        $this->_init('socolissimoliberte/periodesFermeture');
    }  

    public function deleteAll(){
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $db->query("TRUNCATE TABLE mage_socolissimoliberte_periode_fermeture");
    }    
}