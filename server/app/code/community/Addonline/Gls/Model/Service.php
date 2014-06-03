<?php
/**
 * Copyright (c) 2014 GLS
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @category    Addonline
 * @package     Addonline_Gls
 * @copyright   Copyright (c) 2014 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 * @license    http://www.opensource.org/licenses/MIT  The MIT License (MIT)
 **/


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

		if(file_exists(dirname(__FILE__).DS.'Webservice'.DS.'PointsRelaisWSService.php')) require_once dirname(__FILE__).DS.'Webservice'.DS.'PointsRelaisWSService.php';
		else require_once dirname(__FILE__).DS.'Addonline_Gls_Model_Webservice_PointsRelaisWSService.php';

		$pointsRelaisWSService = new PointsRelaisWSService(array('trace' => TRUE), $this->getUrlWsdl());
// 		$aParameters = array('UserName' =>$login,'Password' =>$mdp,'ZipCode' => $zipcode,'Country' => $country);

		$aParameters = array('Credentials' => array('UserName' =>$login,'Password' =>$mdp),'Address' => array(
				'Name1' => '',
				'Name2' => '',
				'Name3' => '',
				'Street1' => '',
				'BlockNo1' => '',
				'Street2' => '',
				'BlockNo2' => '',
				'ZipCode' => $zipcode,
				'City' => '',
				'Province' => '',
				'Country' => $country)
		);

		try {
			$result = $pointsRelaisWSService->findRelayPoints($aParameters);
			return $result;
		}catch (SoapFault $fault) {
			var_dump($fault);
			return false;
		}
	}

}
