<?php
/**
 * Addonline_SoColissimoLiberte
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimoLiberte
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_SoColissimoLiberte_Model_Mysql4_Relais_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct() {
        $this->_init('socolissimoliberte/relais');
    }
    
    public function loadByIdentifiant($identifiant){
        $this->getSelect()->where('main_table.identifiant = ?', $identifiant);
        return $this->load();
    }     
    
    public function prepareNearestByType($latitude, $longitude, $typesRelais, $dateLivraison) {

   		//calcul de la distance d'un arc de cercle à la surface de la terre entre deux points coordonnées : http://fr.wikipedia.org/wiki/Orthodromie
   		$formuleDistance = '(6371 * ACOS(COS(RADIANS(main_table.latitude)) * COS(RADIANS('.$latitude.')) * COS(RADIANS('.$longitude.'-main_table.longitude)) + SIN(RADIANS(main_table.latitude)) * SIN(RADIANS('.$latitude.'))))';
    	
    	$this->getSelect()->distinct()
    						->columns(array('distance'=>$formuleDistance))
							->where('type_relais IN (?)', $typesRelais);

		$anneeLivraison = $dateLivraison->get(Zend_Date::YEAR);
		$dateLivraisonDB =  $dateLivraison->toString('yyyy-MM-dd');
		//jointure sur le table des horaires : on selectionne tous ses champs et on filtre sur la date de livraison
		$this->getSelect()->join(array('h' => $this->getTable('socolissimoliberte/horairesOuverture')),
                    										'main_table.id_relais = h.id_relais_ho', '*')
							->where("STR_TO_DATE(concat(h.deb_periode_horaire , '/$anneeLivraison'), '%d/%m/%Y') <= ?" , $dateLivraisonDB)
							->where("STR_TO_DATE(concat(h.fin_periode_horaire , '/$anneeLivraison'), '%d/%m/%Y') >= ?" , $dateLivraisonDB);

		//jointure sur le table des periodes de fermeture (pour exclure les relais fermés à la date de livraison)
		$this->getSelect ()->joinLeft(array('f' => $this->getTable('socolissimoliberte/periodesFermeture')),
                    										'main_table.id_relais = f.id_relais_fe', array())
							->where("f.deb_periode_fermeture IS NULL OR f.deb_periode_fermeture > $dateLivraisonDB OR f.fin_periode_fermeture < $dateLivraisonDB");
		
		$this->getSelect()->order('distance')
							->limit(10);
   		//Mage::log($this->getSelect()->__toString());
   		
    }
}