<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Mysql4_Liberte_PeriodesFermeture extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct() {
        $this->_init('socolissimo/liberte_periodesFermeture','id_periode_fermeture');
    }  
}