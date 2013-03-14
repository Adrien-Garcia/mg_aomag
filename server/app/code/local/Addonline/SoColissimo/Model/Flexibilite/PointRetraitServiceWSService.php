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
    'pointRetraitV1Result' => 'pointRetraitV1Result',
    'pointRetraitV1' => 'pointRetraitV1',
    'pointRetraitAcheminementResult' => 'pointRetraitAcheminementResult',
    'pointRetraitAcheminement' => 'pointRetraitAcheminement',
    'rdvPointRetraitAcheminementResult' => 'rdvPointRetraitAcheminementResult',
    'bureauInstanceResult' => 'bureauInstanceResult',
    'bureauInstance' => 'bureauInstance',
    'pointRetraitResult' => 'pointRetraitResult',
    'pointRetraitAcheminementByIDResult' => 'pointRetraitAcheminementByIDResult',
    'rdvPointRetraitAcheminementByIDResult' => 'rdvPointRetraitAcheminementByIDResult',
    'findPointRetraitByID' => 'findPointRetraitByID',
    'findPointRetraitByIDResponse' => 'findPointRetraitByIDResponse',
    'findPointRetraitForMobile' => 'findPointRetraitForMobile',
    'findPointRetraitForMobileResponse' => 'findPointRetraitForMobileResponse',
    'findInternalRDVPointRetraitAcheminement' => 'findInternalRDVPointRetraitAcheminement',
    'findInternalRDVPointRetraitAcheminementResponse' => 'findInternalRDVPointRetraitAcheminementResponse',
    'findBureauIntance' => 'findBureauIntance',
    'findBureauIntanceResponse' => 'findBureauIntanceResponse',
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
    'findPointRetraitAcheminementByIDResponse' => 'findPointRetraitAcheminementByIDResponse',
    'findBureauIntanceByRegate' => 'findBureauIntanceByRegate',
    'findBureauIntanceByRegateResponse' => 'findBureauIntanceByRegateResponse');

  /**
   * 
   * @param array $config A array of config values
   * @param string $wsdl The wsdl file to use
   * @access public
   */
  public function __construct(array $options = array(), $wsdl = 'https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS?wsdl')
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
   * Méthode de recherche de point pour SO Mobile
   * 
   * @param findPointRetraitForMobile $parameters
   * @access public
   */
  public function findPointRetraitForMobile(findPointRetraitForMobile $parameters)
  {
    return $this->__soapCall('findPointRetraitForMobile', array($parameters));
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
   * Méthode pour récupérer la liste des bureaux d'instance
   * 
   * @param findBureauIntance $parameters
   * @access public
   */
  public function findBureauIntance(findBureauIntance $parameters)
  {
    return $this->__soapCall('findBureauIntance', array($parameters));
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

  /**
   * Méthode pour récupérer la liste des bureaux d'instance associé à un code regate
   * 
   * @param findBureauIntanceByRegate $parameters
   * @access public
   */
  public function findBureauIntanceByRegate(findBureauIntanceByRegate $parameters)
  {
    return $this->__soapCall('findBureauIntanceByRegate', array($parameters));
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
if (!class_exists("pointRetraitV1Result", false)) 
{
class pointRetraitV1Result
{

  /**
   * 
   * @var string $appointmentcostBE
   * @access public
   */
  public $appointmentcostBE;

  /**
   * 
   * @var string $appointmentcostRDV
   * @access public
   */
  public $appointmentcostRDV;

  /**
   * 
   * @var string $companyName
   * @access public
   */
  public $companyName;

  /**
   * 
   * @var boolean $displayCompanyName
   * @access public
   */
  public $displayCompanyName;

  /**
   * 
   * @var boolean $displayValidPage
   * @access public
   */
  public $displayValidPage;

  /**
   * 
   * @var string $encryptionKey
   * @access public
   */
  public $encryptionKey;

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
   * @var boolean $pointProximite
   * @access public
   */
  public $pointProximite;

  /**
   * 
   * @var pointRetraitV1 $pointRetraitV1
   * @access public
   */
  public $pointRetraitV1;

  /**
   * 
   * @var int $qualiteReponse
   * @access public
   */
  public $qualiteReponse;

  /**
   * 
   * @var boolean $rdv
   * @access public
   */
  public $rdv;

  /**
   * 
   * @var string $urlReturnKO
   * @access public
   */
  public $urlReturnKO;

  /**
   * 
   * @var string $urlReturnOK
   * @access public
   */
  public $urlReturnOK;

  /**
   * 
   * @var string $wsRequestId
   * @access public
   */
  public $wsRequestId;

}

}
if (!class_exists("pointRetraitV1", false)) 
{
class pointRetraitV1
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
   * @var string $distributionSort
   * @access public
   */
  public $distributionSort;

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
   * @var boolean $loanOfHandlingTool
   * @access public
   */
  public $loanOfHandlingTool;

  /**
   * 
   * @var string $localite
   * @access public
   */
  public $localite;

  /**
   * 
   * @var string $lotAcheminement
   * @access public
   */
  public $lotAcheminement;

  /**
   * 
   * @var string $nom
   * @access public
   */
  public $nom;

  /**
   * 
   * @var boolean $parking
   * @access public
   */
  public $parking;

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

  /**
   * 
   * @var string $typeDeReseau
   * @access public
   */
  public $typeDeReseau;

  /**
   * 
   * @var string $versionPlanTri
   * @access public
   */
  public $versionPlanTri;

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
if (!class_exists("bureauInstanceResult", false)) 
{
class bureauInstanceResult
{

  /**
   * 
   * @var bureauInstance $bureauInstance
   * @access public
   */
  public $bureauInstance;

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

}

}
if (!class_exists("bureauInstance", false)) 
{
class bureauInstance
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
   * @var string $adresse4
   * @access public
   */
  public $adresse4;

  /**
   * 
   * @var string $codePostal
   * @access public
   */
  public $codePostal;

  /**
   * 
   * @var string $codeRegate
   * @access public
   */
  public $codeRegate;

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
   * @var int $delaiDistribution
   * @access public
   */
  public $delaiDistribution;

  /**
   * 
   * @var int $delaiRelance
   * @access public
   */
  public $delaiRelance;

  /**
   * 
   * @var int $delaiReponse
   * @access public
   */
  public $delaiReponse;

  /**
   * 
   * @var string $echecInteractivite
   * @access public
   */
  public $echecInteractivite;

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
   * @var string $ville
   * @access public
   */
  public $ville;

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
if (!class_exists("findPointRetraitForMobile", false)) 
{
class findPointRetraitForMobile
{

  /**
   * 
   * @var string $identFO
   * @access public
   */
  public $identFO;

  /**
   * 
   * @var string $requestId
   * @access public
   */
  public $requestId;

  /**
   * 
   * @var string $firstCmd
   * @access public
   */
  public $firstCmd;

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
   * @var string $preparationTime
   * @access public
   */
  public $preparationTime;

  /**
   * 
   * @var string $filterRelay
   * @access public
   */
  public $filterRelay;

  /**
   * 
   * @var string $regateLastPoint
   * @access public
   */
  public $regateLastPoint;

  /**
   * 
   * @var string $typeMedia
   * @access public
   */
  public $typeMedia;

  /**
   * 
   * @var string $typeRequest
   * @access public
   */
  public $typeRequest;

  /**
   * 
   * @var string $countryCode
   * @access public
   */
  public $countryCode;

  /**
   * 
   * @var string $optionInter
   * @access public
   */
  public $optionInter;

  /**
   * 
   * @var string $language
   * @access public
   */
  public $language;

}

}
if (!class_exists("findPointRetraitForMobileResponse", false)) 
{
class findPointRetraitForMobileResponse
{

  /**
   * 
   * @var pointRetraitV1Result $return
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
if (!class_exists("findBureauIntance", false)) 
{
class findBureauIntance
{

  /**
   * 
   * @var string $zipCode
   * @access public
   */
  public $zipCode;

  /**
   * 
   * @var string $regateCode
   * @access public
   */
  public $regateCode;

  /**
   * 
   * @var string $date
   * @access public
   */
  public $date;

}

}
if (!class_exists("findBureauIntanceResponse", false)) 
{
class findBureauIntanceResponse
{

  /**
   * 
   * @var bureauInstanceResult $return
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
if (!class_exists("findBureauIntanceByRegate", false)) 
{
class findBureauIntanceByRegate
{

  /**
   * 
   * @var string $regateCode
   * @access public
   */
  public $regateCode;

  /**
   * 
   * @var string $date
   * @access public
   */
  public $date;

}

}
if (!class_exists("findBureauIntanceByRegateResponse", false)) 
{
class findBureauIntanceByRegateResponse
{

  /**
   * 
   * @var bureauInstanceResult $return
   * @access public
   */
  public $return;

}

}
