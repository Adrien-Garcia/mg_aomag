<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

abstract class  Addonline_SoColissimo_Model_Relais_Abstract extends Mage_Core_Model_Abstract
{
 
    const TYPE_POSTE = 'poste';
    const TYPE_CITYSSIMO = 'cityssimo';
    const TYPE_COMMERCANT = 'commercant';
    
    public function getType() {
    	if ($this->getTypeRelais()=='BPR' || $this->getTypeRelais()=='CDI' || $this->getTypeRelais()=='ACP') {
    		return self::TYPE_POSTE;
    	} elseif ($this->getTypeRelais()=='CIT') {
    		return self::TYPE_CITYSSIMO;
    	} elseif ($this->getTypeRelais()=='A2P') {
    		return self::TYPE_COMMERCANT;
    	} else {
    		return false;
    	}
    }

    public function isBureauPoste() {
    	return $this->getType() == self::TYPE_POSTE;
    }

    public function isCityssimo() {
    	return $this->getType() == self::TYPE_CITYSSIMO;
    }
    
    public function isCommercant() {
    	return $this->getType() == self::TYPE_COMMERCANT;
    }
    
    abstract public function getIdentifiant();
    
    abstract public function getTypeRelais();
    
    abstract public function getDistance();
    
    abstract public function getLibelle();
    
    abstract public function getAdresse();
    
    abstract public function getAdresse1();
    
    abstract public function getAdresse2();
    
    abstract public function getAdresse3();
    
    abstract public function getCodePostal();
    
    abstract public function getCommune();
    
    abstract public function getIndicateurAcces();
    
    abstract public function getCongeTotal();
    
}