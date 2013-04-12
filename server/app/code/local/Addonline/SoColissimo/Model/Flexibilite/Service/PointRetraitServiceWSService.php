<?php

if (!class_exists("PointRetraitServiceWSService", false)) 
{
include_once('PointRetrait.php');
include_once('Conges.php');
include_once('pointRetraitAcheminementResult.php');
include_once('pointRetraitAcheminement.php');
include_once('rdvPointRetraitAcheminementResult.php');
include_once('pointRetraitAcheminementByIDResult.php');
include_once('rdvPointRetraitAcheminementByIDResult.php');
include_once('findRDVPointRetraitAcheminement.php');
include_once('findRDVPointRetraitAcheminementResponse.php');
include_once('findInternalPointRetraitAcheminementByID.php');
include_once('findInternalPointRetraitAcheminementByIDResponse.php');
include_once('findPointRetraitAcheminementByID.php');
include_once('findPointRetraitAcheminementByIDResponse.php');
include_once('findInternalRDVPointRetraitAcheminement.php');
include_once('findInternalRDVPointRetraitAcheminementResponse.php');
include_once('findInternalRDVPointRetraitAcheminementByID.php');
include_once('findInternalRDVPointRetraitAcheminementByIDResponse.php');


/**
 * SO Colissimo (mon Service mes Options) WEB Service Point Retrait [Version 2]
 * 
 */
class PointRetraitServiceWSService extends SoapClient
{

  /**
   * 
   * @var array $classmap The defined classes
   * @access private
   */
  private static $classmap = array(
    'PointRetrait' => 'PointRetrait',
    'PointRetrait' => 'PointRetrait',
    'Conges' => 'Conges',
    'pointRetraitAcheminementResult' => 'pointRetraitAcheminementResult',
    'pointRetraitAcheminement' => 'pointRetraitAcheminement',
    'rdvPointRetraitAcheminementResult' => 'rdvPointRetraitAcheminementResult',
    'pointRetraitAcheminementByIDResult' => 'pointRetraitAcheminementByIDResult',
    'rdvPointRetraitAcheminementByIDResult' => 'rdvPointRetraitAcheminementByIDResult',
    'findRDVPointRetraitAcheminement' => 'findRDVPointRetraitAcheminement',
    'findRDVPointRetraitAcheminementResponse' => 'findRDVPointRetraitAcheminementResponse',
    'findInternalPointRetraitAcheminementByID' => 'findInternalPointRetraitAcheminementByID',
    'findInternalPointRetraitAcheminementByIDResponse' => 'findInternalPointRetraitAcheminementByIDResponse',
    'findPointRetraitAcheminementByID' => 'findPointRetraitAcheminementByID',
    'findPointRetraitAcheminementByIDResponse' => 'findPointRetraitAcheminementByIDResponse',
    'findInternalRDVPointRetraitAcheminement' => 'findInternalRDVPointRetraitAcheminement',
    'findInternalRDVPointRetraitAcheminementResponse' => 'findInternalRDVPointRetraitAcheminementResponse',
    'findInternalRDVPointRetraitAcheminementByID' => 'findInternalRDVPointRetraitAcheminementByID',
    'findInternalRDVPointRetraitAcheminementByIDResponse' => 'findInternalRDVPointRetraitAcheminementByIDResponse');

  /**
   * 
   * @param array $config A array of config values
   * @param string $wsdl The wsdl file to use
   * @access public
   */
  public function __construct(array $options = array(), $wsdl = 'http://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl')
  {
    foreach(self::$classmap as $key => $value)
    {
      if(!isset($options['classmap'][$key]))
      {
        $options['classmap'][$key] = $value;
      }
    }
    
    parent::__construct($wsdl, $options);
  }

  /**
   * 
   * @param findInternalPointRetraitAcheminementByID $parameters
   * @access public
   */
  public function findInternalPointRetraitAcheminementByID(findInternalPointRetraitAcheminementByID $parameters)
  {
    return $this->__soapCall('findInternalPointRetraitAcheminementByID', array($parameters));
  }

  /**
   * 
   * @param findRDVPointRetraitAcheminement $parameters
   * @access public
   */
  public function findRDVPointRetraitAcheminement(findRDVPointRetraitAcheminement $parameters)
  {
    return $this->__soapCall('findRDVPointRetraitAcheminement', array($parameters));
  }

  /**
   * 
   * @param findInternalRDVPointRetraitAcheminement $parameters
   * @access public
   */
  public function findInternalRDVPointRetraitAcheminement(findInternalRDVPointRetraitAcheminement $parameters)
  {
    return $this->__soapCall('findInternalRDVPointRetraitAcheminement', array($parameters));
  }

  /**
   * 
   * @param findPointRetraitAcheminementByID $parameters
   * @access public
   */
  public function findPointRetraitAcheminementByID(findPointRetraitAcheminementByID $parameters)
  {
    return $this->__soapCall('findPointRetraitAcheminementByID', array($parameters));
  }

  /**
   * 
   * @param findInternalRDVPointRetraitAcheminementByID $parameters
   * @access public
   */
  public function findInternalRDVPointRetraitAcheminementByID(findInternalRDVPointRetraitAcheminementByID $parameters)
  {
    return $this->__soapCall('findInternalRDVPointRetraitAcheminementByID', array($parameters));
  }

}

}
