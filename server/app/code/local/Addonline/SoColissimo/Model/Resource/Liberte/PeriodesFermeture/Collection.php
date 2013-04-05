<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Resource_Liberte_PeriodesFermeture_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct() {
        $this->_init('socolissimo/liberte_periodesFermeture');
    }  
}