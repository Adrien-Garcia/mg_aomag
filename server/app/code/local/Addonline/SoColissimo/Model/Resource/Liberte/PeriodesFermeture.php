<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Resource_Liberte_PeriodesFermeture extends Mage_Core_Model_Resource_Abstract
{
    public function _construct() {
        $this->_init('socolissimo/liberte_periodesFermeture','socolissimo_periode_fermeture');
    }
    public function deleteAll(){
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $db->query("TRUNCATE TABLE ".$this->getMainTable());
    }    
}