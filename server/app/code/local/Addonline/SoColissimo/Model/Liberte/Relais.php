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
    
	public function getIdentifiant() {
		return $this->getData('identifiant');
	}
	
    public function getTypeRelais() {
    	return $this->getData('type_relais');
    }
    
    public function getDistance() {
    	return $this->getData('distance');
    }
    
    public function getLibelle() {
    	return $this->getData('libelle');
    }
    
    public function getAdresse() {
    	return $this->getData('adresse');
    }

    public function getAdresse1() {
    	return $this->getData('complement_adr');
    }
    
    public function getAdresse2() {
    	return $this->getData('lieu_dit');
    }
    
    public function getAdresse3() {
    	return $this->getData('indice_localisation');
    }
    
    public function getCodePostal() {
    	return $this->getData('code_postal');
    }
    
    public function getCommune() {
    	return $this->getData('commune');
    }
    
    public function getIndicateurAcces() {
    	return $this->getData('indicateur_acces');
    }
    
    public function getCongeTotal() {
    	return false;
    }
    
}