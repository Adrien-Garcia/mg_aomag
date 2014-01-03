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

	public function selectorAction()
	{
		// Creation du block
   		$this->loadLayout();
   		$block = $this->getLayout()->createBlock(
   				'Addonline_Gls_Block_Selector',
   				'root',
   				array('template' => 'gls/selector.phtml')
   		);
   		$this->getLayout()->getBlock('content')->append($block);
   		$this->renderLayout();
	}

    /**
     * Load liste Point relais
     */
    public function listPointsRelaisAction()
    {
    	$aPointsRelais = array();
		$response = new Varien_Object();

   		$zipcode   = $this->getRequest()->getParam('zipcode', false);
   		$country   = $this->getRequest()->getParam('country', false);

   		$listrelais = Mage::getSingleton('gls/service')->getRelayPointsForZipCode($zipcode, $country);

   		foreach($listrelais->SearchResults as $key => $pointRelais){

   			$aRelay = array();
   			$aRelay['relayId'] = $pointRelais->Parcelshop->ParcelShopId;
   			$aRelay['relayName'] = $pointRelais->Parcelshop->Address->Name1.' '.$pointRelais->Parcelshop->Address->Name2.' '.$pointRelais->Parcelshop->Address->Name3;
			$aRelay['relayAddress'] = $pointRelais->Parcelshop->Address->Street1.' '.$pointRelais->Parcelshop->Address->BlockNo1.' '.$pointRelais->Parcelshop->Address->Street2.' '.$pointRelais->Parcelshop->Address->BlockNo2;
			$aRelay['relayZipCode'] = $pointRelais->Parcelshop->Address->ZipCode;
			$aRelay['relayCity'] = $pointRelais->Parcelshop->Address->City;
			$aRelay['relayCountry'] = $pointRelais->Parcelshop->Address->Country;
			$aRelay['relayLatitude'] = $pointRelais->Parcelshop->GLSCoordinates->Latitude;
			$aRelay['relayLongitude'] = $pointRelais->Parcelshop->GLSCoordinates->Longitude;

			$relayWorkingDays = array();
			for($i = 0; $i < 6; $i++) {
				if(isset($pointRelais->Parcelshop->GLSWorkingDay[$i])){
					$relayWorkingDays[$i]['hours']['from'] = $pointRelais->Parcelshop->GLSWorkingDay[$i]->OpeningHours->Hours->From;
					$relayWorkingDays[$i]['hours']['to'] = $pointRelais->Parcelshop->GLSWorkingDay[$i]->OpeningHours->Hours->To;
					$relayWorkingDays[$i]['breaks']['from'] = $pointRelais->Parcelshop->GLSWorkingDay[$i]->Breaks->Hours->From;
					$relayWorkingDays[$i]['breaks']['to'] = $pointRelais->Parcelshop->GLSWorkingDay[$i]->Breaks->Hours->To;
				}
 			}
 			$aRelay['relayWorkingDays'] = $relayWorkingDays;
 			$aPointsRelais[$pointRelais->Parcelshop->ParcelShopId] = $aRelay;
   		}

   		// Creation du block
   		$this->loadLayout();
   		$block = $this->getLayout()->createBlock(
   				'Addonline_Gls_Block_Listrelay',
   				'root',
   				array('template' => 'gls/listrelais.phtml')
   		);
   		$block->setListRelay($aPointsRelais);
   		$this->getLayout()->getBlock('content')->append($block);
   		$this->renderLayout();
    }

    public function saveInSessionRelayInformationsAction(){
    	if(count($_GET)){
    		Mage::getSingleton('checkout/session')->setData('gls_shipping_relay_data',$_GET);
    	}
    }
}
