<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

class  Addonline_SoColissimo_Model_Flexibilite_Relais extends Addonline_SoColissimo_Model_Relais_Abstract
{
 
	private $_pointRetraitAcheminement;
	
	public function setPointRetraitAcheminement($pointRetraitAcheminement) {
		$this->_pointRetraitAcheminement = $pointRetraitAcheminement;		
	}
	
	public function getPointRetraitAcheminement() {
		return $this->_pointRetraitAcheminement;
	}

	public function getIdentifiant() {
		return $this->_pointRetraitAcheminement->identifiant;
	}
	
    public function getTypeRelais() {
    	return $this->_pointRetraitAcheminement->typeDePoint;
    }
    
    public function getDistance() {
    	return intval($this->_pointRetraitAcheminement->distanceEnMetre)/1000; 
    }
    
    public function getLibelle() {
    	return $this->_pointRetraitAcheminement->nom;
    }
    
    public function getAdresse() {
    	return $this->_pointRetraitAcheminement->adresse1;
    }
    
    public function getAdresse1() {
    	return $this->_pointRetraitAcheminement->adresse2;
    }
    
    public function getAdresse2() {
    	return $this->_pointRetraitAcheminement->adresse3;
    }
    
    public function getAdresse3() {
    	return $this->_pointRetraitAcheminement->indiceDeLocalisation;
    }
    
    public function getCodePostal() {
    	return $this->_pointRetraitAcheminement->codePostal;
    }
    
    public function getCommune() {
    	return $this->_pointRetraitAcheminement->localite;
    }
    
    public function getIndicateurAcces() {
    	return $this->_pointRetraitAcheminement->accesPersonneMobiliteReduite;
    }
    
    public function getCongeTotal() {
    	return $this->_pointRetraitAcheminement->congesTotal;
    }
    
}