<?php
/**
 * Addonline_SoColissimoFlexibilite
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimoFlexibilite
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_SoColissimoFlexibilite_AjaxController extends Mage_Core_Controller_Front_Action
{
 
    
    /**
     * Load liste relais
     */
    public function listRelaisAction()
    {
		$response = new Varien_Object();
    	
   		$adresse    = $this->getRequest()->getParam('adresse', false);
   		$zipcode    = $this->getRequest()->getParam('zipcode', false);
   		$ville      = $this->getRequest()->getParam('ville', false);
   		/*$poste  	= $this->getRequest()->getParam('poste', false);	
   		$cityssimo  = $this->getRequest()->getParam('cityssimo', false);
   		$commercant = $this->getRequest()->getParam('commercant', false);

     	//le filtre du WS permet seulement d'exclure les commerçants
    	$filterRelay = 0;
     	if ($commercant == 'true' || $commercant === 'checked') {
    		$filterRelay = 1;
     	} 
		*/
   		$filterRelay = 1;
     	$listrelais = Mage::getModel('socolissimoflexibilite/service')->findRDVPointRetraitAcheminement($adresse, $zipcode, $ville, $filterRelay);
	    
	    if (is_array($listrelais->listePointRetraitAcheminement)) {
	        $result['items'] = $listrelais->listePointRetraitAcheminement;
	        $result['html'] = $this->_getListRelaisHtml($listrelais->listePointRetraitAcheminement);
	        $result['skinUrl'] = Mage::getDesign()->getSkinUrl("images/socolissimo/");
	    } else {
	        $result['error'] = $listrelais->errorMessage;
	    }
        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    protected function _getListRelaisHtml($list)
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('socolissimoflexibilite_ajax_listrelais');
        $layout->generateXml();
        $layout->generateBlocks();
        $layout->getBlock('root')->setListRelais($list);
        $output = $layout->getOutput();
        return $output;
    }
}
