<?php

class Addonline_SoColissimo_Model_Flexibilite_Service {
	
	protected $_urlWsdl;

	protected $_available;
	
	public function getUrlWsdl()
	{
		if (!$this->_urlWsdl) {
			if (Mage::getStoreConfig('carriers/socolissimo/testws_socolissimo_flexibilite')) {
				$this->_urlWsdl = "https://pfi.telintrans.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl";
			} else {
				$this->_urlWsdl = "http://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl";
			}
		}
		return $this->_urlWsdl;
	}
	
	public function isAvailable() {

		if (!$this->_available) {
			try {
				$supervisionUrl = "http://ws.colissimo.fr/supervision-wspudo/supervision.jsp";
				if (Mage::getStoreConfig('carriers/socolissimo/testws_socolissimo_flexibilite')) {
					$supervisionUrl = "http://pfi.telintrans.fr/supervision-wspudo/supervision.jsp";
				}
				$ctx=stream_context_create(array('http'=> array( 'timeout' => 0.6 )));//Si on n'a pas de réponse en moins d'une demi seconde
				$this->_available = file_get_contents ($supervisionUrl,false,$ctx);
			} catch(Exception $e){
				$this->_available = "[KO]";
			}
		}
		return 	trim($this->_available) === "[OK]";
	}
	
	function findRDVPointRetraitAcheminement($adresse, $zipcode, $ville, $country, $filterRelay) {
		
		/* 
		 * On inclu la class Stub générée avec wsdl2phpgenrator : https://github.com/walle/wsdl2phpgenerator/wiki/ExampleUsage 
		 * ./wsdl2php -et -i http://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl
		 */
		require_once dirname(__FILE__).'/Service/PointRetraitServiceWSService.php';

		$pointRetraitServiceWSService = new PointRetraitServiceWSService(array('trace' => TRUE), $this->getUrlWsdl());

		try {
				$findRDVPointRetraitAcheminement = new findRDVPointRetraitAcheminement();
				$findRDVPointRetraitAcheminement->accountNumber = Mage::getStoreConfig('carriers/socolissimo/id_socolissimo_flexibilite');
				$findRDVPointRetraitAcheminement->password = Mage::getStoreConfig('carriers/socolissimo/password_socolissimo_flexibilite');
				$findRDVPointRetraitAcheminement->address = $adresse;
				$findRDVPointRetraitAcheminement->zipCode = $zipcode;
				$findRDVPointRetraitAcheminement->city = $ville;
				$findRDVPointRetraitAcheminement->countryCode = $country;
				$findRDVPointRetraitAcheminement->weight = Mage::helper('socolissimo')->getQuoteWeight();
				$findRDVPointRetraitAcheminement->shippingDate =  Mage::helper('socolissimo')->getShippingDate();
				$findRDVPointRetraitAcheminement->filterRelay = $filterRelay;
				$date = new Zend_Date();
				$quote = Mage::getSingleton('checkout/session')->getQuote();
				$findRDVPointRetraitAcheminement->requestId = Mage::getStoreConfig('carriers/socolissimo/id_socolissimo_flexibilite').$quote->getCustomerId().$date->toString('yyyyMMddHHmmss');
				$findRDVPointRetraitAcheminement->lang = (Mage::app()->getStore()->getLanguageCode() == 'NL')?'NL':'FR';
				$findRDVPointRetraitAcheminement->optionInter = Mage::getStoreConfig('carriers/socolissimo/international');
				
				$result = $pointRetraitServiceWSService->findRDVPointRetraitAcheminement($findRDVPointRetraitAcheminement);

				//Mage::log('Request '.$pointRetraitServiceWSService->__getLastRequest());
				//Mage::log('Response '.$pointRetraitServiceWSService->__getLastResponse());
				//Mage::log($result);
				
				if ($result->return->errorCode != 0) {			
					Mage::log($result->return);
				}				
				return $result->return;
				
		} catch (SoapFault $fault) {
			Mage::log('RequestHeaders '.$pointRetraitServiceWSService->__getLastRequestHeaders());
			Mage::log('Request '.$pointRetraitServiceWSService->__getLastRequest());
			Mage::log('ResponseHeaders '.$pointRetraitServiceWSService->__getLastResponseHeaders());
			Mage::log('Response '.$pointRetraitServiceWSService->__getLastResponse());
			Mage::log($fault);
			return false;
		}
			
	}
	
	function findPointRetraitAcheminementByID($id, $reseau) {
		
		require_once dirname(__FILE__).'/Service/PointRetraitServiceWSService.php';
		
		$pointRetraitServiceWSService = new PointRetraitServiceWSService(array('trace' => TRUE), $this->getUrlWsdl());

		try {
				$findPointRetraitAcheminementByID = new findPointRetraitAcheminementByID();
				$findPointRetraitAcheminementByID->accountNumber = Mage::getStoreConfig('carriers/socolissimo/id_socolissimo_flexibilite');
				$findPointRetraitAcheminementByID->password = Mage::getStoreConfig('carriers/socolissimo/password_socolissimo_flexibilite');
				$findPointRetraitAcheminementByID->id = $id;
				$findPointRetraitAcheminementByID->weight = Mage::helper('socolissimo')->getQuoteWeight();
				$findPointRetraitAcheminementByID->date = Mage::helper('socolissimo')->getShippingDate();
				$findPointRetraitAcheminementByID->filterRelay = 1; //pout tous les avoir, même les commerçants
				$findPointRetraitAcheminementByID->reseau = $reseau;
				$findPointRetraitAcheminementByID->langue = (Mage::app()->getStore()->getLanguageCode() == 'NL')?'NL':'FR';
				
				$result = $pointRetraitServiceWSService->findPointRetraitAcheminementByID($findPointRetraitAcheminementByID);
				
				if ($result->return->errorCode == 0) {			
					Mage::log($result->return->pointRetraitAcheminement);	
					$relais = Mage::getModel('socolissimo/flexibilite_relais');
					$relais->setPointRetraitAcheminement($result->return->pointRetraitAcheminement);
					return $relais;
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
