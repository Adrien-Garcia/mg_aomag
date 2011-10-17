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
class Addonline_SoColissimoLiberte_Model_Mysql4_HorairesOuverture extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct() {
        $this->_init('socolissimoliberte/horairesOuverture','id_horaire_ouverture');
    }
    
    public function deleteAll(){
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $db->query("TRUNCATE TABLE ".$this->getMainTable());
    }
}