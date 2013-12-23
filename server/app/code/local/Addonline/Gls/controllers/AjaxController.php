<?php
/**
 * Addonline_GLS
 *
 * @category    Addonline
 * @package     Addonline_GLS
 * @copyright   Copyright (c) 2013 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Gls_AjaxController extends Mage_Core_Controller_Front_Action
{

    /**
     * Load liste Point relais
     */
    public function listPointsRelaisAction()
    {
		$response = new Varien_Object();

   		$zipcode   = $this->getRequest()->getParam('zipcode', false);
   		$country   = $this->getRequest()->getParam('country', false);

   		$listrelais = Mage::getSingleton('gls/service')->getRelayPointsForZipCode($zipcode, $country);

   		var_dump($listrelais);

   		/* $resource = Mage::getSingleton('core/resource');
   		$read = $resource->getConnection('core_read');
   		$qry = "SELECT *,(((acos(sin((".$latitude."*pi()/180)) * sin((pickupstore_latitude*pi()/180))+cos((".$latitude."*pi()/180)) * cos((pickupstore_latitude*pi()/180)) * cos(((".$longitude."- pickupstore_longitude) *pi()/180))))*180/pi())*60*1.1515*1.609344) as distance FROM storepickup_pickupstore WHERE (((acos(sin((".$latitude."*pi()/180)) * sin((pickupstore_latitude*pi()/180))+cos((".$latitude."*pi()/180)) * cos((pickupstore_latitude*pi()/180)) * cos(((".$longitude."- pickupstore_longitude) *pi()/180))))*180/pi())*60*1.1515*1.609344) <= ".$distance." order by distance limit ".$limit.";";
   		$rs = $read->query($qry);
   		$aReturn = array();
   		while($row = $rs->fetch() ) {
   			$aReturn[$row['pickupstore_id']] = $row;
   		}

   		// Creation du block
   		$this->loadLayout();
   		$block = $this->getLayout()->createBlock(
   				'Addonline_StorePickup_Block_Liststore',
   				'root',
   				array('template' => 'storepickup/liststore.phtml')
   		);
   		$block->setListStore($aReturn);
   		$this->getLayout()->getBlock('content')->append($block);
   		$this->renderLayout();
		*/
    }
}
