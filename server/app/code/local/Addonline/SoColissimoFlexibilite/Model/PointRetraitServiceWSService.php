<?php

if (!class_exists("PointRetraitServiceWSService", false)) 
{

/**
 * SO Colissimo (mon Service mes Options) WEB Service Point Retrait
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
    'pointRetraitByIDResult' => 'pointRetraitByIDResult',
    'pointRetrait' => 'pointRetrait',
    'conges' => 'conges',
    'pointRetraitAcheminementResult' => 'pointRetraitAcheminementResult',
    'pointRetraitAcheminement' => 'pointRetraitAcheminement',
    'rdvPointRetraitAcheminementResult' => 'rdvPointRetraitAcheminementResult',
    'pointRetraitResult' => 'pointRetraitResult',
    'pointRetraitAcheminementByIDResult' => 'pointRetraitAcheminementByIDResult',
    'rdvPointRetraitAcheminementByIDResult' => 'rdvPointRetraitAcheminementByIDResult',
    'findPointRetraitByID' => 'findPointRetraitByID',
    'findPointRetraitByIDResponse' => 'findPointRetraitByIDResponse',
    'findInternalRDVPointRetraitAcheminement' => 'findInternalRDVPointRetraitAcheminement',
    'findInternalRDVPointRetraitAcheminementResponse' => 'findInternalRDVPointRetraitAcheminementResponse',
    'findPointRetrait' => 'findPointRetrait',
    'findPointRetraitResponse' => 'findPointRetraitResponse',
    'findInternalPointRetraitAcheminementByID' => 'findInternalPointRetraitAcheminementByID',
    'findInternalPointRetraitAcheminementByIDResponse' => 'findInternalPointRetraitAcheminementByIDResponse',
    'findPointRetraitAcheminement' => 'findPointRetraitAcheminement',
    'findPointRetraitAcheminementResponse' => 'findPointRetraitAcheminementResponse',
    'findInternalRDVPointRetraitAcheminementByID' => 'findInternalRDVPointRetraitAcheminementByID',
    'findInternalRDVPointRetraitAcheminementByIDResponse' => 'findInternalRDVPointRetraitAcheminementByIDResponse',
    'findRDVPointRetraitAcheminement' => 'findRDVPointRetraitAcheminement',
    'findRDVPointRetraitAcheminementResponse' => 'findRDVPointRetraitAcheminementResponse',
    'findPointRetraitAcheminementByID' => 'findPointRetraitAcheminementByID',
    'findPointRetraitAcheminementByIDResponse' => 'findPointRetraitAcheminementByIDResponse');

  /**
   * 
   * @param array $config A array of config values
   * @param string $wsdl The wsdl file to use
   * @access public
   */
  public function __construct(array $options = array(), $wsdl = 'http://217.108.161.163/pointretrait-ws-cxf/PointRetraitServiceWS?wsdl')
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
   * @param findPointRetraitByID $parameters
   * @access public
   */
  public function findPointRetraitByID(findPointRetraitByID $parameters)
  {
    return $this->__soapCall('findPointRetraitByID', array($parameters));
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
   * @param findInternalPointRetraitAcheminementByID $parameters
   * @access public
   */
  public function findInternalPointRetraitAcheminementByID(findInternalPointRetraitAcheminementByID $parameters)
  {
    return $this->__soapCall('findInternalPointRetraitAcheminementByID', array($parameters));
  }

  /**
   * 
   * @param findPointRetrait $parameters
   * @access public
   */
  public function findPointRetrait(findPointRetrait $parameters)
  {
    return $this->__soapCall('findPointRetrait', array($parameters));
  }

  /**
   * 
   * @param findPointRetraitAcheminement $parameters
   * @access public
   */
  public function findPointRetraitAcheminement(findPointRetraitAcheminement $parameters)
  {
    return $this->__soapCall('findPointRetraitAcheminement', array($parameters));
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
   * @param findPointRetraitAcheminementByID $parameters
   * @access public
   */
  public function findPointRetraitAcheminementByID(findPointRetraitAcheminementByID $parameters)
  {
    return $this->__soapCall('findPointRetraitAcheminementByID', array($parameters));
  }

}

}
if (!class_exists("pointRetraitByIDResult", false)) 
{
class pointRetraitByIDResult
{

  /**
   * 
   * @var int $errorCode
   * @access public
   */
  public $errorCode;

  /**
   * 
   * @var string $errorMessage
   * @access public
   */
  public $errorMessage;

  /**
   * 
   * @var pointRetrait $pointRetrait
   * @access public
   */
  public $pointRetrait;

}

}
if (!class_exists("pointRetrait", false)) 
{
class pointRetrait
{

  /**
   * 
   * @var boolean $accesPersonneMobiliteReduite
   * @access public
   */
  public $accesPersonneMobiliteReduite;

  /**
   * 
   * @var string $adresse1
   * @access public
   */
  public $adresse1;

  /**
   * 
   * @var string $adresse2
   * @access public
   */
  public $adresse2;

  /**
   * 
   * @var string $adresse3
   * @access public
   */
  public $adresse3;

  /**
   * 
   * @var string $codePostal
   * @access public
   */
  public $codePostal;

  /**
   * 
   * @var boolean $congesPartiel
   * @access public
   */
  public $congesPartiel;

  /**
   * 
   * @var boolean $congesTotal
   * @access public
   */
  public $congesTotal;

  /**
   * 
   * @var string $coordGeolocalisationLatitude
   * @access public
   */
  public $coordGeolocalisationLatitude;

  /**
   * 
   * @var string $coordGeolocalisationLongitude
   * @access public
   */
  public $coordGeolocalisationLongitude;

  /**
   * 
   * @var int $distanceEnMetre
   * @access public
   */
  public $distanceEnMetre;

  /**
   * 
   * @var string $horairesOuvertureDimanche
   * @access public
   */
  public $horairesOuvertureDimanche;

  /**
   * 
   * @var string $horairesOuvertureJeudi
   * @access public
   */
  public $horairesOuvertureJeudi;

  /**
   * 
   * @var string $horairesOuvertureLundi
   * @access public
   */
  public $horairesOuvertureLundi;

  /**
   * 
   * @var string $horairesOuvertureMardi
   * @access public
   */
  public $horairesOuvertureMardi;

  /**
   * 
   * @var string $horairesOuvertureMercredi
   * @access public
   */
  public $horairesOuvertureMercredi;

  /**
   * 
   * @var string $horairesOuvertureSamedi
   * @access public
   */
  public $horairesOuvertureSamedi;

  /**
   * 
   * @var string $horairesOuvertureVendredi
   * @access public
   */
  public $horairesOuvertureVendredi;

  /**
   * 
   * @var string $identifiant
   * @access public
   */
  public $identifiant;

  /**
   * 
   * @var string $indiceDeLocalisation
   * @access public
   */
  public $indiceDeLocalisation;

  /**
   * 
   * @var conges $listeConges
   * @access public
   */
  public $listeConges;

  /**
   * 
   * @var string $localite
   * @access public
   */
  public $localite;

  /**
   * 
   * @var string $nom
   * @access public
   */
  public $nom;

  /**
   * 
   * @var string $periodeActiviteHoraireDeb
   * @access public
   */
  public $periodeActiviteHoraireDeb;

  /**
   * 
   * @var string $periodeActiviteHoraireFin
   * @access public
   */
  public $periodeActiviteHoraireFin;

  /**
   * 
   * @var int $poidsMaxi
   * @access public
   */
  public $poidsMaxi;

  /**
   * 
   * @var string $typeDePoint
   * @access public
   */
  public $typeDePoint;

}

}
if (!class_exists("conges", false)) 
{
class conges
{

  /**
   * 
   * @var dateTime $calendarDeDebut
   * @access public
   */
  public $calendarDeDebut;

  /**
   * 
   * @var dateTime $calendarDeFin
   * @access public
   */
  public $calendarDeFin;

  /**
   * 
   * @var int $numero
   * @access public
   */
  public $numero;

}

}
if (!class_exists("pointRetraitAcheminementResult", false)) 
{
class pointRetraitAcheminementResult
{

  /**
   * 
   * @var int $errorCode
   * @access public
   */
  public $errorCode;

  /**
   * 
   * @var string $errorMessage
   * @access public
   */
  public $errorMessage;

  /**
   * 
   * @var pointRetraitAcheminement $listePointRetraitAcheminement
   * @access public
   */
  public $listePointRetraitAcheminement;

  /**
   * 
   * @var int $qualiteReponse
   * @access public
   */
  public $qualiteReponse;

  /**
   * 
   * @var string $wsRequestId
   * @access public
   */
  public $wsRequestId;

}

}
if (!class_exists("pointRetraitAcheminement", false)) 
{
class pointRetraitAcheminement
{

  /**
   * 
   * @var string $distributionSort
   * @access public
   */
  public $distributionSort;

  /**
   * 
   * @var string $lotAcheminement
   * @access public
   */
  public $lotAcheminement;

  /**
   * 
   * @var string $versionPlanTri
   * @access public
   */
  public $versionPlanTri;

}

}
if (!class_exists("rdvPointRetraitAcheminementResult", false)) 
{
class rdvPointRetraitAcheminementResult
{

  /**
   * 
   * @var boolean $rdv
   * @access public
   */
  public $rdv;

}

}
if (!class_exists("pointRetraitResult", false)) 
{
class pointRetraitResult
{

  /**
   * 
   * @var int $errorCode
   * @access public
   */
  public $errorCode;

  /**
   * 
   * @var string $errorMessage
   * @access public
   */
  public $errorMessage;

  /**
   * 
   * @var pointRetrait $listePointRetrait
   * @access public
   */
  public $listePointRetrait;

  /**
   * 
   * @var int $qualiteReponse
   * @access public
   */
  public $qualiteReponse;

  /**
   * 
   * @var string $wsRequestId
   * @access public
   */
  public $wsRequestId;

}

}
if (!class_exists("pointRetraitAcheminementByIDResult", false)) 
{
class pointRetraitAcheminementByIDResult
{

  /**
   * 
   * @var int $errorCode
   * @access public
   */
  public $errorCode;

  /**
   * 
   * @var string $errorMessage
   * @access public
   */
  public $errorMessage;

  /**
   * 
   * @var pointRetraitAcheminement $pointRetraitAcheminement
   * @access public
   */
  public $pointRetraitAcheminement;

}

}
if (!class_exists("rdvPointRetraitAcheminementByIDResult", false)) 
{
class rdvPointRetraitAcheminementByIDResult
{

  /**
   * 
   * @var boolean $rdv
   * @access public
   */
  public $rdv;

}

}
if (!class_exists("findPointRetraitByID", false)) 
{
class findPointRetraitByID
{

  /**
   * 
   * @var string $accountNumber
   * @access public
   */
  public $accountNumber;

  /**
   * 
   * @var string $password
   * @access public
   */
  public $password;

  /**
   * 
   * @var string $id
   * @access public
   */
  public $id;

  /**
   * 
   * @var string $date
   * @access public
   */
  public $date;

  /**
   * 
   * @var string $weight
   * @access public
   */
  public $weight;

  /**
   * 
   * @var string $filterRelay
   * @access public
   */
  public $filterRelay;

}

}
if (!class_exists("findPointRetraitByIDResponse", false)) 
{
class findPointRetraitByIDResponse
{

  /**
   * 
   * @var pointRetraitByIDResult $return
   * @access public
   */
  public $return;

}

}
if (!class_exists("findInternalRDVPointRetraitAcheminement", false)) 
{
class findInternalRDVPointRetraitAcheminement
{

  /**
   * 
   * @var string $accountNumber
   * @access public
   */
  public $accountNumber;

  /**
   * 
   * @var string $password
   * @access public
   */
  public $password;

  /**
   * 
   * @var string $address
   * @access public
   */
  public $address;

  /**
   * 
   * @var string $zipCode
   * @access public
   */
  public $zipCode;

  /**
   * 
   * @var string $city
   * @access public
   */
  public $city;

  /**
   * 
   * @var string $weight
   * @access public
   */
  public $weight;

  /**
   * 
   * @var string $shippingDate
   * @access public
   */
  public $shippingDate;

  /**
   * 
   * @var string $filterRelay
   * @access public
   */
  public $filterRelay;

  /**
   * 
   * @var string $requestId
   * @access public
   */
  public $requestId;

}

}
if (!class_exists("findInternalRDVPointRetraitAcheminementResponse", false)) 
{
class findInternalRDVPointRetraitAcheminementResponse
{

  /**
   * 
   * @var rdvPointRetraitAcheminementResult $return
   * @access public
   */
  public $return;

}

}
if (!class_exists("findPointRetrait", false)) 
{
class findPointRetrait
{

  /**
   * 
   * @var string $accountNumber
   * @access public
   */
  public $accountNumber;

  /**
   * 
   * @var string $password
   * @access public
   */
  public $password;

  /**
   * 
   * @var string $address
   * @access public
   */
  public $address;

  /**
   * 
   * @var string $zipCode
   * @access public
   */
  public $zipCode;

  /**
   * 
   * @var string $city
   * @access public
   */
  public $city;

  /**
   * 
   * @var string $weight
   * @access public
   */
  public $weight;

  /**
   * 
   * @var string $shippingDate
   * @access public
   */
  public $shippingDate;

  /**
   * 
   * @var string $filterRelay
   * @access public
   */
  public $filterRelay;

  /**
   * 
   * @var string $requestId
   * @access public
   */
  public $requestId;

}

}
if (!class_exists("findPointRetraitResponse", false)) 
{
class findPointRetraitResponse
{

  /**
   * 
   * @var pointRetraitResult $return
   * @access public
   */
  public $return;

}

}
if (!class_exists("findInternalPointRetraitAcheminementByID", false)) 
{
class findInternalPointRetraitAcheminementByID
{

  /**
   * 
   * @var string $id
   * @access public
   */
  public $id;

}

}
if (!class_exists("findInternalPointRetraitAcheminementByIDResponse", false)) 
{
class findInternalPointRetraitAcheminementByIDResponse
{

  /**
   * 
   * @var pointRetraitAcheminementByIDResult $return
   * @access public
   */
  public $return;

}

}
if (!class_exists("findPointRetraitAcheminement", false)) 
{
class findPointRetraitAcheminement
{

  /**
   * 
   * @var string $accountNumber
   * @access public
   */
  public $accountNumber;

  /**
   * 
   * @var string $password
   * @access public
   */
  public $password;

  /**
   * 
   * @var string $address
   * @access public
   */
  public $address;

  /**
   * 
   * @var string $zipCode
   * @access public
   */
  public $zipCode;

  /**
   * 
   * @var string $city
   * @access public
   */
  public $city;

  /**
   * 
   * @var string $weight
   * @access public
   */
  public $weight;

  /**
   * 
   * @var string $shippingDate
   * @access public
   */
  public $shippingDate;

  /**
   * 
   * @var string $filterRelay
   * @access public
   */
  public $filterRelay;

  /**
   * 
   * @var string $requestId
   * @access public
   */
  public $requestId;

}

}
if (!class_exists("findPointRetraitAcheminementResponse", false)) 
{
class findPointRetraitAcheminementResponse
{

  /**
   * 
   * @var pointRetraitAcheminementResult $return
   * @access public
   */
  public $return;

}

}
if (!class_exists("findInternalRDVPointRetraitAcheminementByID", false)) 
{
class findInternalRDVPointRetraitAcheminementByID
{

  /**
   * 
   * @var string $accountNumber
   * @access public
   */
  public $accountNumber;

  /**
   * 
   * @var string $password
   * @access public
   */
  public $password;

  /**
   * 
   * @var string $id
   * @access public
   */
  public $id;

  /**
   * 
   * @var string $date
   * @access public
   */
  public $date;

  /**
   * 
   * @var string $weight
   * @access public
   */
  public $weight;

  /**
   * 
   * @var string $filterRelay
   * @access public
   */
  public $filterRelay;

}

}
if (!class_exists("findInternalRDVPointRetraitAcheminementByIDResponse", false)) 
{
class findInternalRDVPointRetraitAcheminementByIDResponse
{

  /**
   * 
   * @var rdvPointRetraitAcheminementByIDResult $return
   * @access public
   */
  public $return;

}

}
if (!class_exists("findRDVPointRetraitAcheminement", false)) 
{
class findRDVPointRetraitAcheminement
{

  /**
   * 
   * @var string $accountNumber
   * @access public
   */
  public $accountNumber;

  /**
   * 
   * @var string $password
   * @access public
   */
  public $password;

  /**
   * 
   * @var string $address
   * @access public
   */
  public $address;

  /**
   * 
   * @var string $zipCode
   * @access public
   */
  public $zipCode;

  /**
   * 
   * @var string $city
   * @access public
   */
  public $city;

  /**
   * 
   * @var string $weight
   * @access public
   */
  public $weight;

  /**
   * 
   * @var string $shippingDate
   * @access public
   */
  public $shippingDate;

  /**
   * 
   * @var string $filterRelay
   * @access public
   */
  public $filterRelay;

  /**
   * 
   * @var string $requestId
   * @access public
   */
  public $requestId;

}

}
if (!class_exists("findRDVPointRetraitAcheminementResponse", false)) 
{
class findRDVPointRetraitAcheminementResponse
{

  /**
   * 
   * @var rdvPointRetraitAcheminementResult $return
   * @access public
   */
  public $return;

}

}
if (!class_exists("findPointRetraitAcheminementByID", false)) 
{
class findPointRetraitAcheminementByID
{

  /**
   * 
   * @var string $accountNumber
   * @access public
   */
  public $accountNumber;

  /**
   * 
   * @var string $password
   * @access public
   */
  public $password;

  /**
   * 
   * @var string $id
   * @access public
   */
  public $id;

  /**
   * 
   * @var string $date
   * @access public
   */
  public $date;

  /**
   * 
   * @var string $weight
   * @access public
   */
  public $weight;

  /**
   * 
   * @var string $filterRelay
   * @access public
   */
  public $filterRelay;

}

}
if (!class_exists("findPointRetraitAcheminementByIDResponse", false)) 
{
class findPointRetraitAcheminementByIDResponse
{

  /**
   * 
   * @var pointRetraitAcheminementByIDResult $return
   * @access public
   */
  public $return;

}

}
