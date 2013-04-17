<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

class  Addonline_SoColissimo_Model_Liberte_Relais extends Addonline_SoColissimo_Model_Relais_Abstract
{
 
    /**
     * Constructeur par défaut
     * @return  Addonline_SoColissimo_Model_Liberte_Relais
     */
    public function _construct() {
        parent::_construct();
        $this->_init('socolissimo/liberte_relais');
    }
    
    public function loadByIdentifiantReseau($identifiant, $reseau) {
        $collection = $this->getCollection();
    	$collection->addFieldToFIlter('identifiant', $identifiant);
        $collection->addFieldToFIlter('code_reseau', $reseau);
        return $collection->getFirstItem();
    }     
    
    public function getLibelle() {
    	 if (Mage::app()->getStore()->getLanguageCode() == 'NL') {
    	 	return $this->getData('libelle_nl');
    	 } else {
    		return $this->getData('libelle');
    	 }
    }
    
    public function getAdresse() {
        if (Mage::app()->getStore()->getLanguageCode() == 'NL') {
    	 	return $this->getData('adresse_nl');
    	} else {
    		return $this->getData('adresse');
    	}
    }

    public function getCommune() {
            if (Mage::app()->getStore()->getLanguageCode() == 'NL') {
    	 	return $this->getData('commune_nl');
    	} else {
    		return $this->getData('commune');
    	}
    }
    
}