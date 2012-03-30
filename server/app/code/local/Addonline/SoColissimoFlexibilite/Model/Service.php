<?php

class Addonline_SoColissimoFlexibilite_Model_Service {
	
	function findRDVPointRetraitAcheminement($adresse, $zipcode, $ville, $filterRelay) {
		
		require_once dirname(__FILE__).'/PointRetraitServiceWSService.php';

		//$urlWsdl='https://217.108.161.163/pointretrait-ws-cxf/PointRetraitServiceWS?wsdl';
		$urlWsdl='http://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS?wsdl';
		
		$pointRetraitServiceWSService = new PointRetraitServiceWSService(array('trace' => TRUE), $urlWsdl);

		try {
				$findRDVPointRetraitAcheminement = new findRDVPointRetraitAcheminement();
				$findRDVPointRetraitAcheminement->accountNumber = Mage::getStoreConfig('carriers/socolissimoflexibilite/id_socolissimo');
				$findRDVPointRetraitAcheminement->password = Mage::getStoreConfig('carriers/socolissimoflexibilite/password_socolissimo');
				$findRDVPointRetraitAcheminement->address = $adresse;
				$findRDVPointRetraitAcheminement->zipCode = $zipcode;
				$findRDVPointRetraitAcheminement->city = $ville;
				$findRDVPointRetraitAcheminement->weight = $this->_getQuoteWeight();
				$findRDVPointRetraitAcheminement->shippingDate =  $this->_getShippingDate();
				$findRDVPointRetraitAcheminement->filterRelay = $filterRelay;
				$date = new Zend_Date();
				$quote = Mage::getSingleton('checkout/session')->getQuote();
				$findRDVPointRetraitAcheminement->requestId = Mage::getStoreConfig('carriers/socolissimoflexibilite/id_socolissimo').$quote->getCustomerId().$date->toString('yyyyMMddHHmmss');

				$result = $pointRetraitServiceWSService->findRDVPointRetraitAcheminement($findRDVPointRetraitAcheminement);
				
				Mage::log('Request '.$pointRetraitServiceWSService->__getLastRequest());
				Mage::log('Response '.$pointRetraitServiceWSService->__getLastResponse());
				
				if ($result->return->errorCode == 0) {			
					//foreach ($result->return->listePointRetraitAcheminement as $relais) {
						//Mage::log($relais	);
						//$this->findPointRetraitAcheminementByID($relais->identifiant);
					//} 
					return $result->return;
				} else {
					Mage::log($result->return);
					return $result->return;
				}				
				
		} catch (SoapFault $fault) {
				Mage::log('RequestHeaders '.$pointRetraitServiceWSService->__getLastRequestHeaders());
				Mage::log('Request '.$pointRetraitServiceWSService->__getLastRequest());
				Mage::log('ResponseHeaders '.$pointRetraitServiceWSService->__getLastResponseHeaders());
				Mage::log('Response '.$pointRetraitServiceWSService->__getLastResponse());
				Mage::log($fault);
				return false;
			}
			
	}
	
	function findPointRetraitAcheminementByID($id) {
		
		require_once dirname(__FILE__).'/PointRetraitServiceWSService.php';

		$urlWsdl='https://217.108.161.163/pointretrait-ws-cxf/PointRetraitServiceWS?wsdl';
		
		$pointRetraitServiceWSService = new PointRetraitServiceWSService(array('trace' => TRUE), $urlWsdl);

		try {
				$findPointRetraitAcheminementByID = new findPointRetraitAcheminementByID();
				$findPointRetraitAcheminementByID->accountNumber = Mage::getStoreConfig('carriers/socolissimoflexibilite/id_socolissimo');
				$findPointRetraitAcheminementByID->password = Mage::getStoreConfig('carriers/socolissimoflexibilite/password_socolissimo');
				$findPointRetraitAcheminementByID->id = $id;
				$findPointRetraitAcheminementByID->weight = $this->_getQuoteWeight();
				$findPointRetraitAcheminementByID->date = $this->_getShippingDate();
				$findPointRetraitAcheminementByID->filterRelay = 1;

				$result = $pointRetraitServiceWSService->findPointRetraitAcheminementByID($findPointRetraitAcheminementByID);
				
				if ($result->return->errorCode == 0) {			
					Mage::log($result->return->pointRetraitAcheminement);		
					return $result->return->pointRetraitAcheminement;
				} else {
					return $result->return->errorMessage;
				}
		} catch (SoapFault $fault) {
				Mage::log('RequestHeaders '.$pointRetraitServiceWSService->__getLastRequestHeaders());
				Mage::log('Request '.$pointRetraitServiceWSService->__getLastRequest());
				Mage::log('ResponseHeaders '.$pointRetraitServiceWSService->__getLastResponseHeaders());
				Mage::log('Response '.$pointRetraitServiceWSService->__getLastResponse());
				Mage::log($fault);
				return false;
			}
			
	}
	
	private function _getQuoteWeight() {
		//on récupère le poids du colis en gramme de type entier
     	$quote = Mage::getSingleton('checkout/session')->getQuote();
     	$weight = 0;
     	foreach ($quote->getAllItems() as $item) {
	     	$weight += $item->getRowWeight();
     	}
     	$weight = round($weight*1000);
     	if ($weight==0) {
     		$weight=1;
     	}
     	return $weight;
	}

	
	private function _getShippingDate() {
	
   		$shippingDate = new Zend_Date();
    	if ($delay = Mage::getStoreConfig('carriers/socolissimoflexibilite/shipping_period')) {
	    	$shippingDate->addDay($delay);
    	} else {
	    	$shippingDate->addDay(1);
    	}
     	return $shippingDate->toString('dd/MM/yyyy');
	}
/**
 * Codes erreurs WS
 * 
 * 0 	Code retour OK
 * 101 Numéro de compte absent
 * 102 Mot de passe absent
 * 104 Code postal absent
 * 105 Ville absente
 * 106 Date estimée de l’envoi absente
 * 107 Identifiant point de retrait absent
 * 120 Poids n’est pas un entier
 * 121 Poids n’est pas compris entre 1 et 99999
 * 122 Date n’est pas au format JJ/MM/AAAA
 * 123 Filtre relais n’est pas 0 ou 1
 * 124 Identifiant point de retrait incorrect
 * 125 Code postal incorrect (non compris entre 01XXX et 95XXX ou 980XX)
 * 201 Identifiant / mot de passe invalide
 * 202 Service non autorisé pour cet identifiant
 * 300 Pas de point de retrait suite à l’application des règles métier
 * 1000 Erreur système (erreur technique)
 **/
}