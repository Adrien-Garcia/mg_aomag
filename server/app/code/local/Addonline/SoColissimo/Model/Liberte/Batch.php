<?php
/**
 * Addonline_SoColissimo
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

class Addonline_SoColissimo_Model_Liberte_Batch {
	
	private $_tabRelais;
	
	public function run() { 
		
		if (Mage::helper('socolissimo')->isFlexibilite()) {
			return;
		}
		
		$_export_dir = Mage::getStoreConfig('carriers/socolissimo/rep_fichier_liberte', Mage::app()->getStore()->getId());
		
		$_export_path = BP . DS . $_export_dir;
		
	    $repertoire = opendir($_export_path) or die("Erreur le repertoire $_export_path existe pas");
	    $file = null; 
	    $timestamp = 0;
	    while($nom_fichier = @readdir($repertoire)){
	    	if( preg_match( "/^PR_CLP_/" , $nom_fichier ) ){
		        // enlever les traitements inutile
		       if ($nom_fichier == "." || $nom_fichier == "..") 
		       		continue;
		       		
		       // il faut prendre le fichier le plus récent dans le répértoire
		       if( $timestamp < filemtime( $_export_path.DS.$nom_fichier ) ){
		       		$timestamp = filemtime( $_export_path.DS.$nom_fichier );
		       		$file = $nom_fichier;
		       }
	    	}
	    }
	    closedir($repertoire);

	    if($file){
	    	$this->_importRelais($_export_path.DS.$file);
	    }else{
	    	echo "Aucun fichier SoColissimo à importer";
	    }
	}
	
	
	function _importRelais($nom_fichier){

		/*Ouverture du fichier en lecture seule*/
		$file = fopen($nom_fichier, 'r');
		/*Si on a réussi à ouvrir le fichier*/
		if ($file){
			// on vide les tables socolissimo_horaire_ouverture et socolissimo_periode_fermeture pour mettre à jour leur données
			$this->_viderTables ();	
			
			/*Tant que l'on est pas à la fin du fichier*/
			while ( !feof($file) )
			{
				// on lit la ligne courante
				$ligne = fgets($file);

				// Lire les lignes des points de retrait
				if( strpos($ligne, "PR")===0 ){
					$this->majRelais($ligne);
				}
				// Lire les lignes des horaires d'ouverture des points de retrait
				else if( strpos($ligne, "HO")===0 ){	
					$this->remplirHoraireOuverture($ligne);
				}		
				// Lire les lignes des horaires de fermeture des points de retrait
				else if( strpos($ligne, "FE")===0 ){
					$this->remplirPeriodeFermeture($ligne);
				}						
			}
			/*On ferme le fichier*/
			fclose($file);
		}		
	}
	
    public function loadByIdentifiant( $identifiant ){
    	$relais = Mage::getModel('socolissimo/liberte_relais');
  
        $item = Mage::getModel('socolissimo/liberte_relais')->getCollection()->loadByIdentifiant( $identifiant )->getFirstItem();
        if($item->getIdentifiant()!="") 
        	$relais = $item;
        return $relais;
    }	
	
	/**
	 * maj dans la table socolissimo_relais'
	 */
	function majRelais( $ligneRelais ){
		$donnes_relais = explode(";", $ligneRelais);
		$relais = $this->loadByIdentifiant( $donnes_relais[1] );
		$relais->setIdentifiant( $donnes_relais[1] );
		$relais->setLibelle( $donnes_relais[2] );
		$relais->setAdresse($donnes_relais[3]);
		$relais->setComplement_adr($donnes_relais[4]);
		$relais->setLieu_dit($donnes_relais[5]);
		$relais->setIndice_localisation($donnes_relais[6]);
		$relais->setCode_postal($donnes_relais[7]);
		$relais->setCommune( $donnes_relais[8] );
		$relais->setLatitude(str_replace(',','.',$donnes_relais[9]));
		$relais->setLongitude(str_replace(',','.',$donnes_relais[10]));
		$relais->setIndicateur_acces($donnes_relais[11]);
		$relais->setType_relais($donnes_relais[12]);
		$relais->setPoint_max($donnes_relais[13]);
		$relais->setLot_acheminement($donnes_relais[14]);
		$relais->setDistribution_sort($donnes_relais[15]);
		$relais->setVersion($donnes_relais[16]);
		$relais->save();
		// sauvegarde des clé primaire de chaque identifiants relais	
		$this->_tabRelais[$donnes_relais[1]] = $relais->getId();
	}
	
	/**
	 * maj dans la table socolissimo_horaire_ouverture'
	 */
	function remplirHoraireOuverture( $ligneHO ){	
		$donnes_horaire = explode(";", $ligneHO);
		$horaire_ouverture = Mage::getModel('socolissimo/liberte_horairesOuverture');
		
		if( isset($this->_tabRelais[$donnes_horaire[1]]) ){
			$horaire_ouverture->setId_relais_ho( $this->_tabRelais[$donnes_horaire[1]] );
			$horaire_ouverture->setDeb_periode_horaire( $donnes_horaire[2] );
			$horaire_ouverture->setFin_periode_horaire($donnes_horaire[3]);
			$horaire_ouverture->setHoraire_lundi($donnes_horaire[4]);
			$horaire_ouverture->setHoraire_mardi($donnes_horaire[5]);
			$horaire_ouverture->setHoraire_mercredi($donnes_horaire[6]);
			$horaire_ouverture->setHoraire_jeudi($donnes_horaire[7]);
			$horaire_ouverture->setHoraire_vendredi( $donnes_horaire[8] );
			$horaire_ouverture->setHoraire_samedi($donnes_horaire[9]);
			$horaire_ouverture->setHoraire_dimanche($donnes_horaire[10]);
			$horaire_ouverture->save();
		}
	}
	
	/**
	 * maj dans la table socolissimo_periode_fermeture'
	 */
	function remplirPeriodeFermeture( $ligneFE ){
		$donnes_fe = explode(";", $ligneFE);
		$periode_fermeture = Mage::getModel('socolissimo/liberte_periodesFermeture');

		if( isset($this->_tabRelais[$donnes_fe[1]]) ){
			$dd = new Zend_Date( $donnes_fe[2], "dd/MM/yyyy");
			$df = new Zend_Date( $donnes_fe[3], "dd/MM/yyyy" );	
			$periode_fermeture->setId_relais_fe( $this->_tabRelais[$donnes_fe[1]] );
			$periode_fermeture->setDeb_periode_fermeture( $dd->toString("yyyy-MM-dd") );
			$periode_fermeture->setFin_periode_fermeture( $df->toString("yyyy-MM-dd") );
			$periode_fermeture->save();
		}
	}	

	
	
	function _viderTables(){
		Mage::getResourceModel('socolissimo/liberte_horairesOuverture')->deleteAll();
		Mage::getResourceModel('socolissimo/liberte_periodesFermeture')->deleteAll();
	}
}