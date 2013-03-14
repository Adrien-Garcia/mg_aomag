<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

class  Addonline_SoColissimo_Model_Liberte_Relais extends Mage_Core_Model_Abstract
{
 
    /**
     * Constructeur par défaut
     * @return  Addonline_SoColissimo_Model_Relais
     */
    public function _construct() {
        parent::_construct();
        $this->_init('socolissimo/relais');
    }
    
    public function getType() {
    	if ($this->getTypeRelais()=='BPR') {
    		return 'poste';
    	} elseif ($this->getTypeRelais()=='CIT') {
    		return 'cityssimo';
    	} elseif ($this->getTypeRelais()=='A2P') {
    		return 'commercant';
    	} else {
    		return false;
    	}
    }
}