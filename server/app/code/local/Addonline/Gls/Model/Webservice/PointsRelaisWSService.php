<?php

if (!class_exists("PointsRelaisWSService", false))
{

	class PointsRelaisWSService extends SoapClient
	{

	  /**
	   *
	   * @param array $config A array of config values
	   * @param string $wsdl The wsdl file to use
	   * @access public
	   */
	  public function __construct(array $options = array(), $wsdl = 'http://www.gls-group.eu/276-I-PORTAL-WEBSERVICE/services/ParcelShopSearch/wsdl/2010_01_ParcelShopSearch.wsdl')
	  {
	    parent::__construct($wsdl, $options);
	  }

	  /**
	   *
	   * @param findInternalPointRetraitAcheminementByID $parameters
	   * @access public
	   */
	  public function findRelayPoints($parameters)
	  {
	    return $this->__soapCall('GetParcelShops', array($parameters));
	  }

	}
}