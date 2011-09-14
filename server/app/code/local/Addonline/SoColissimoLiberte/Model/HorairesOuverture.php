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

class  Addonline_SoColissimoLiberte_Model_HorairesOuverture extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('socolissimoliberte/horairesOuverture');
    }
}