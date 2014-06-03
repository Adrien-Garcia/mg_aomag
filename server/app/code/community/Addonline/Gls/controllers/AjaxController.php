<?php
/**
 * Copyright (c) 2014 GLS
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @category    Addonline
 * @package     Addonline_Gls
 * @copyright   Copyright (c) 2014 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 * @license    http://www.opensource.org/licenses/MIT  The MIT License (MIT)
 **/

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
   		if(!isset($listrelais->SearchResults)){
   			echo $this->__('Your parameters for GSL Webservice might be wrong, or the webservice is down');
   			Mage::log($listrelais,null,'gls.log');
   		}else{

   			$productMaxWeight = 0;
   			$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
   			foreach($items as $item) {
   				$productMaxWeight = (($productMaxWeight>$item->getWeight())?$productMaxWeight:$item->getWeight());
   			}
   			
   			
   			$onlyxlrelay = Mage::getStoreConfig('carriers/gls/onlyxlrelay') || ($productMaxWeight > Mage::getStoreConfig('carriers/gls/maxrelayweight'));
	   		foreach($listrelais->SearchResults as $key => $pointRelais){

 	   			if($onlyxlrelay && substr($pointRelais->Parcelshop->Address->Name1,strlen($pointRelais->Parcelshop->Address->Name1)-2,strlen($pointRelais->Parcelshop->Address->Name1)) != 'XL'){
 	   				continue;
 	   			}
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
				for($i = 0; $i < 7; $i++) {
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
