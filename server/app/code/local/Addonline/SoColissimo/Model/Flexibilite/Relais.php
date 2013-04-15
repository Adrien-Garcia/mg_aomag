<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

class  Addonline_SoColissimo_Model_Flexibilite_Relais extends Addonline_SoColissimo_Model_Relais_Abstract
{

	
	public function setPointRetraitAcheminement($pointRetraitAcheminement) {
		$this->setIdentifiant($pointRetraitAcheminement->identifiant);
		$this->setTypeRelais($pointRetraitAcheminement->typeDePoint);
		$this->setDistance(intval($pointRetraitAcheminement->distanceEnMetre)/1000);
		$this->setLibelle($pointRetraitAcheminement->nom);
		$this->setAdresse($pointRetraitAcheminement->adresse1);
		$this->setAdresse1($pointRetraitAcheminement->adresse2);
		$this->setAdresse2($pointRetraitAcheminement->adresse3);
		$this->setCodePostal($pointRetraitAcheminement->codePostal);
		$this->setCommune($pointRetraitAcheminement->localite);
		$this->setLatitude($pointRetraitAcheminement->coordGeolocalisationLatitude);
		$this->setLongitude($pointRetraitAcheminement->coordGeolocalisationLongitude);
		$this->setIndicateurAcces($pointRetraitAcheminement->accesPersonneMobiliteReduite);
		$this->setCongeTotal($pointRetraitAcheminement->congesTotal);
		//Ouvertures
		$this->setHoraireLundi($pointRetraitAcheminement->horairesOuvertureLundi);
		$this->setHoraireMardi($pointRetraitAcheminement->horairesOuvertureMardi);
		$this->setHoraireMercredi($pointRetraitAcheminement->horairesOuvertureMercredi);
		$this->setHoraireJeudi($pointRetraitAcheminement->horairesOuvertureJeudi);
		$this->setHoraireVendredi($pointRetraitAcheminement->horairesOuvertureVendredi);
		$this->setHoraireSamedi($pointRetraitAcheminement->horairesOuvertureSamedi);
		$this->setHoraireDimanche($pointRetraitAcheminement->horairesOuvertureDimanche);
		
		//Congés
		$listeConges = array();
		$listeConges['items']=array();
		if (isset($pointRetraitAcheminement->listeConges)) {
			if (is_array($pointRetraitAcheminement->listeConges)) {
				$listeConges['totalRecords']=count($pointRetraitAcheminement->listeConges);
				foreach ($pointRetraitAcheminement->listeConges as $conge) {
					$listeConges['items'][]=array('deb_periode_fermeture'=> $conge->calendarDeDebut, 'fin_periode_fermeture'=> $conge->calendarDeFin);
				}
			} else if (isset($pointRetraitAcheminement->listeConges)) {
				$conge =$pointRetraitAcheminement->listeConges;
				$listeConges['totalRecords']= 1;
				$listeConges['items'][]=array('deb_periode_fermeture'=> $conge->calendarDeDebut, 'fin_periode_fermeture'=> $conge->calendarDeFin);
			}
		} else {
			$listeConges['totalRecords']=0;
		}
		$this->setFermetures($listeConges);
	}
	
   public function toJson(array $arrAttributes = array()) {
   		Mage::log('relais toJson');
   	  return $this->getData();
   }
}