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

class  Addonline_SoColissimoLiberte_Model_Relais extends Mage_Core_Model_Abstract
{
 
    /**
     * Constructeur par défaut
     * @return  Addonline_SoColissimoLiberte_Model_Relais
     */
    public function _construct() {
        parent::_construct();
        $this->_init('socolissimoliberte/relais');
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