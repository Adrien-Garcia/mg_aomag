<?php

class Addonline_Gls_Model_Service {

	protected $_urlWsdl;

	public function getUrlWsdl()
	{
		if (!$this->_urlWsdl) {
			$this->_urlWsdl = "http://www.gls-group.eu/276-I-PORTAL-WEBSERVICE/services/ParcelShopSearch/wsdl/2010_01_ParcelShopSearch.wsdl";
		}
		return $this->_urlWsdl;
	}

	public function getRelayPointsForZipCode($zipcode,$country){
		$login = Mage::getStoreConfig('carriers/gls/usernamews');
		$mdp   = Mage::getStoreConfig('carriers/gls/passws');

		require_once dirname(__FILE__).DS.'Webservice'.DS.'PointsRelaisWSService.php';

		$pointsRelaisWSService = new PointsRelaisWSService(array('trace' => TRUE), $this->getUrlWsdl());
		$aParameters = array('UserName' =>$login,'Password' =>$mdp,'ZipCode' => $zipcode,'Country' => $country);

		try {
			$result = $pointsRelaisWSService->findRelayPoints($aParameters);
			if ($result->return->errorCode != 0) {
				Mage::log($result->return);
			}
			return $result->return;
		}catch (SoapFault $fault) {
			echo '<pre>';
			var_dump($fault);
			echo '</pre>';
			return false;
		}
	}

}
