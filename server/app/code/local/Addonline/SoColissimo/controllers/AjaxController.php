<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_AjaxController extends Mage_Core_Controller_Front_Action
{
 
	/**
	 * Load liste relais
	 */
	public function selectorAction()
	{
		$layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('socolissimo_ajax_selector');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
	}
	
    /**
     * Load liste relais
     */
    public function listRelaisAction()
    {
    	
   		$poste  	= $this->getRequest()->getParam('poste', false);	
   		$cityssimo  = $this->getRequest()->getParam('cityssimo', false);
   		$commercant = $this->getRequest()->getParam('commercant', false);

   		$typesRelais = array();
   		if ($poste == 'true' || $poste === 'checked') {
   			$typesRelais[] = 'BPR';
   			$typesRelais[] = 'CDI';
   			$typesRelais[] = 'ACP';
   		}
   		if ($cityssimo == 'true' || $cityssimo === 'checked') {
   			$typesRelais[] = 'CIT';
   		}
   		if ($commercant == 'true' || $commercant === 'checked') {
   			$typesRelais[] = 'A2P';
   		}
   		 
   		if (Mage::helper('socolissimo')->isFlexibilite()) {
   		
	   		$adresse    = $this->getRequest()->getParam('adresse', false);
	   		$zipcode    = $this->getRequest()->getParam('zipcode', false);
	   		$ville      = $this->getRequest()->getParam('ville', false);
   			
   			//le filtre du WS permet seulement d'exclure les commerçants : on filtre les résultats après l'appel au WS */
   			$filterRelay = 0;
   			if ($commercant == 'true' || $commercant === 'checked') {
   				$filterRelay = 1;
   			}
   			
	     	$listrelais = Mage::getModel('socolissimo/flexibilite_service')->findRDVPointRetraitAcheminement($adresse, $zipcode, $ville, 1, $typesRelais);
	     	
	     	if (isset($listrelais->listePointRetraitAcheminement) && is_array($listrelais->listePointRetraitAcheminement)) {
	     		$items = array();
	     		foreach ($listrelais->listePointRetraitAcheminement as $pointRetraitAcheminement) {
	     			$relais = Mage::getModel('socolissimo/flexibilite/relais');
	     			$relais->setPointRetraitAcheminement($pointRetraitAcheminement); 
	     			$items[] = $relais;
	     		}
	     		$result['items'] = $items;
		        $result['html'] = $this->_getListRelaisHtml($items);
		        $result['skinUrl'] = Mage::getDesign()->getSkinUrl("images/socolissimo/");
		    } else {
		        $result['error'] = $listrelais->errorMessage;
		    }
	        
   		} else {

	   		$latitude   = $this->getRequest()->getParam('latitude', false);
   			$longitude  = $this->getRequest()->getParam('longitude', false);

   			$dateLivraison = new Zend_Date();
   			if ($delai = Mage::getStoreConfig('carriers/socolissimo/shipping_period')) {
   				$dateLivraison->addDay($delai);
   			} else {
   				$dateLivraison->addDay(1);
   			}
   			 
   			$listrelais = Mage::getModel('socolissimo/liberte_relais')->getCollection();
   			$listrelais->prepareNearestByType($latitude, $longitude, $typesRelais, $dateLivraison);
   			 
   			foreach ($listrelais as $relais) {
   				$relais->setData('urlPicto', Mage::getDesign()->getSkinUrl("images/socolissimo/picto_".$relais->getType().".png"));
   				$relais->setData('type', $relais->getType());
   				$listFermetures = Mage::getModel('socolissimo/liberte_periodesFermeture')->getCollection();
   				$listFermetures->addFieldToFilter('id_relais_fe',$relais->getId());
   				$relais->setData('fermetures', $listFermetures->toArray());
   			}
   			$result = $listrelais->toArray(); //Pas besoin de spécifier le clé 'items', l'objet array porte déjà ses élélments sur 'items'
   			$result['html'] = $this->_getListRelaisHtml($listrelais);
   			$result['skinUrl'] = Mage::getDesign()->getSkinUrl("images/socolissimo/");
   			
   		}
	    
        $this->getResponse()->setBody(Zend_Json::encode($result));

    }
    
    protected function _getListRelaisHtml($list)
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('socolissimo_ajax_listrelais');
        $layout->generateXml();
        $layout->generateBlocks();
        $layout->getBlock('root')->setListRelais($list);
        $output = $layout->getOutput();
        return $output;
    }
}
