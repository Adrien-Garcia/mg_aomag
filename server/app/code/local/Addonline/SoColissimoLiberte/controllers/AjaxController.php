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
class Addonline_SoColissimoLiberte_AjaxController extends Mage_Core_Controller_Front_Action
{
 
    
    /**
     * Load liste relais
     */
    public function listRelaisAction()
    {
		$response = new Varien_Object();
    	
   		$latitude   = $this->getRequest()->getParam('latitude', false);
   		$longitude  = $this->getRequest()->getParam('longitude', false);
		$poste  	= $this->getRequest()->getParam('poste', false);	
   		$cityssimo  = $this->getRequest()->getParam('cityssimo', false);
   		$commercant = $this->getRequest()->getParam('commercant', false);
   		$typesRelais = array();
        if ($poste == 'true') {
    		$typesRelais[] = 'BPR';
    	} 
    	if ($cityssimo == 'true') {
    		$typesRelais[] = 'CIT';
    	} 
    	if ($commercant == 'true') {
    		$typesRelais[] = 'A2P';
    	} 
    	$dateLivraison = new Zend_Date();
    	if ($delai = Mage::getStoreConfig('carriers/socolissimoliberte/shipping_period')) {
	    	$dateLivraison->addDay($delai);
    	} else {
	    	$dateLivraison->addDay(1);
    	}
    	
   		$listrelais = Mage::getModel('socolissimoliberte/relais')->getCollection();
	    $listrelais->prepareNearestByType($latitude, $longitude, $typesRelais, $dateLivraison);
   		
        foreach ($listrelais as $relais) {
        	$relais->setData('urlPicto', Mage::getDesign()->getSkinUrl("images/commande/picto_".$relais->getType().".png"));
        	$relais->setData('type', $relais->getType());
        }
        $result = $listrelais->toArray();
        $result['html'] = $this->_getListRelaisHtml($listrelais);

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    protected function _getListRelaisHtml($list)
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('socolissimoliberte_ajax_listrelais');
        $layout->generateXml();
        $layout->generateBlocks();
        $layout->getBlock('root')->setListRelais($list);
        $output = $layout->getOutput();
        return $output;
    }
}
