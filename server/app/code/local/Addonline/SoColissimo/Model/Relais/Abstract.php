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
    	if (!$this->hasData('type')) {
	    	if ($this->getTypeRelais()=='BPR' || $this->getTypeRelais()=='CDI' || $this->getTypeRelais()=='ACP' || $this->getTypeRelais()=='BDP') {
	    		$this->setData('type',self::TYPE_POSTE);
	    	} elseif ($this->getTypeRelais()=='CIT') {
	    		$this->setData('type',self::TYPE_CITYSSIMO);
	    	} elseif ($this->getTypeRelais()=='A2P' || $this->getTypeRelais()=='CMT') {
	    		$this->setData('type',self::TYPE_COMMERCANT);
	    	} else {
	    		$this->setData('type','');
	    	}
    	}
    	return $this->getData('type');
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

    public function isParking() {
    	if (!$this->hasData('parking')) {
	    	$this->setParking($this->getTypeRelais()=='CDI' || $this->getTypeRelais()=='ACP');
    	}
    	return $this->getParking();
    }
    
    public function isManutention() {
    	if (!$this->hasData('manutention')) {
	    	$this->setManutention($this->getTypeRelais()=='CDI' || $this->getTypeRelais()=='ACP');
    	}
    	return $this->getManutention();
    }
}