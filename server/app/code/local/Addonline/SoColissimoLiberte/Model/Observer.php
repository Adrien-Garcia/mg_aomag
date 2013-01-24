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

class Addonline_SoColissimoLiberte_Model_Observer extends Varien_Object
{
	
	public function _9cd4777ae76310fd6977a5c559c51820($store_id){						
		if (Mage::getStoreConfig('addonline/licence/aomagento')) { return true; }				
		
		$key = 'e983cfc54f88c7114e99da95f5757df6';
		$store_error = null;
		$stores = Mage::getModel('core/store')->getCollection();
		foreach($stores as $store) {
			$storeurl = Mage::app()->getStore($store['store_id'])->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			$storekey = Mage::getStoreConfig('socolissimoliberte/licence/serial',$store);			
			if(($storekey != 'DISABLED')){					
				if(md5($storeurl.$key.'SoColissimoLiberte')!=$storekey){
					$severity=Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;$title= "Vous devez renseigner une clé licence valide pour le module So colissimo Liberté, pour le magasin ".$store['code'].". Le module a été désactivé";$description= "Le module So colissimo Liberté n'a pas une clé licence valide";	$date = date('Y-m-d H:i:s'); Mage::getModel('adminnotification/inbox')->parse(array(array('severity' => $severity,'date_added'=> $date,'title'=> $title,'description'   => $description,'url'=> '','internal'      => true)));
					$store_error .= 1;
				}
			}
			
		}
					
		if(!is_object($store_id)){			
			$thisStore =  Mage::getModel('core/store')->load($store_id);
			$thisStorekey = Mage::getStoreConfig('socolissimoliberte/licence/serial',$thisStore);
			if($thisStorekey == 'DISABLED'){
				return false;
			}
		} 
		
		if(!$store_error){
			$_unreadNotices = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('is_read', 0); foreach($_unreadNotices as $notice): if(strpos($notice->getData('description'),'So colissimo Libert')): $notice->setIsRead(1)->save();	endif;endforeach;
			return true;
		}else{								
			return false;
		}
	}

    public function checkoutEventSocodata($observer)
    {

    	$quote = $observer->getEvent()->getQuote();
    	$request = Mage::app()->getRequest();
    	
    	$typeSocolissimo = $request->getParam('type_socolissimo');
    	
    	if ($typeSocolissimo) {
    	$adresseRelais = null;
    	$socoShippingData = Mage::getSingleton('checkout/session')->getData('socolissimoliberte_shipping_data');
    	//on positionne l'identitifant relais précédent si il existe
    	if (is_array($socoShippingData) && isset($socoShippingData['PRID'])) {
    		$adresseRelais = $socoShippingData['PRID'];
    	}
 		//on réinitilaise les données en session
    	$socoShippingData = array();
		Mage::getSingleton('checkout/session')->setData('socolissimoliberte_shipping_data', $socoShippingData);
	    if ($shippingAddress = $quote->getShippingAddress()) {
	    	
			if (strpos($request->getParam('shipping_method'),'socolissimoliberte_')===0) {
			
	        	$arrayData = array();
		        $street = array();
		        $customerNotesArray = array();
	
		        $socoShippingData['CEDELIVERYINFORMATION'] = '';
		        $socoShippingData['CEDOORCODE1'] = '';
		        $socoShippingData['CEDOORCODE2'] = '';
		        $socoShippingData['CEENTRYPHONE'] = '';
		        $socoShippingData['CECIVILITY'] = '';
		        $socoShippingData['CEEMAIL'] = $quote->getCustomer()->getData('email');
		        
		        $telephone = $request->getParam('tel_socolissimo');
		        if ($telephone) { 
		            $arrayData['telephone'] = $telephone;
		        	$socoShippingData['CEPHONENUMBER'] = $telephone;
	        	}   	

	        	$socoShippingData['DELIVERYMODE'] = $this->_getSocoProductCode($typeSocolissimo);
	        	 // marquer le mode RDV au niveau de l'adresse (calcul dépend du mode)
	        	$arrayData['soco_product_code'] = $this->_getSocoProductCode($typeSocolissimo);
	        	if ($typeSocolissimo == 'rdv') {
	        		$customerNotesArray['0']='Livraison sur rendez-vous : '.$telephone;
	            	$socoShippingData['CEDELIVERYINFORMATION'] = 'Prise de rendez-vous : '.$telephone;
	            }
	    		$idRelais = $request->getParam('relais_socolissimo');
	    		$relais = Mage::getModel('socolissimoliberte/relais')->load($idRelais);

	    		if ($relais->getId()) {
	
	        		$arrayData['customer_address_id'] = null;
		            
	        		$billingAddress = $quote->getBillingAddress();
		            $arrayData['lastname'] = '('.$billingAddress->getFirstname().' '.$billingAddress->getLastname().')';
		            $arrayData['firstname'] = $relais->getData('libelle');
		            $arrayData['city'] = $relais->getData('commune');
		            $arrayData['postcode'] =$relais->getData('code_postal');
		            $arrayData['telephone'] = $telephone;
		            	        		
		            $street['0'] = $relais->getData('adresse');
		            $street['1'] = $relais->getData('complement_adr');
		            $street['2'] = $relais->getData('lieu_dit');
		            $street['3'] = $relais->getData('indice_localisation');
	            	$shippingAddress->setStreet($street);
		            
		            $customerNotesArray['0']='Livraison relais colis : '.$relais->getIdentifiant();
			        
		            $socoShippingData['PRID'] = $relais->getIdentifiant();
			        
	            	$arrayData['save_in_address_book'] = 0;
	            
	        	} else {
		            $socoShippingData['PRID'] = '';
	        	}
	        	//on initialise les données socolissimo en session
	        	Mage::getSingleton('checkout/session')->setData('socolissimoliberte_shipping_data', $socoShippingData);
	        	
	        	if (! empty($customerNotesArray)) {
		        	$arrayData['customer_notes'] = implode("\n",$customerNotesArray);
		        }

				// sauvegarder l'adresse
	            $shippingAddress->addData($arrayData);
	        	
	            // relancer le calcul du prix (modification du shipping amount via socolissimo)
	            // voir getCalculatedPrice() de ShippingMethod
	            $shippingAddress->setCollectShippingRates(true);
	            $shippingAddress->collectShippingRates();
	            

	        }        
		}
		
		if ($adresseRelais && $adresseRelais != '') {
			if (!isset($socoShippingData['PRID']) || $socoShippingData['PRID'] == '') {
				//si l'adresse de livraison était un relais et que maintenant ça ne l'est plus il faut remettre l'adresse de facturation :
				$billingAddress = $quote->getBillingAddress();
				$shippingAdress = $quote->getShippingAddress();
				$shippingAdress->setData('customer_id', $billingAddress->getData('customer_id'));
               	$shippingAdress->setData('customer_address_id', $billingAddress->getData('customer_address_id'));
               	$shippingAdress->setData('email', $billingAddress->getData('email'));
               	$shippingAdress->setData('prefix', $billingAddress->getData('prefix'));
               	$shippingAdress->setData('firstname', $billingAddress->getData('firstname'));
               	$shippingAdress->setData('middlename', $billingAddress->getData('middlename'));
               	$shippingAdress->setData('lastname', $billingAddress->getData('lastname'));
               	$shippingAdress->setData('suffix', $billingAddress->getData('suffix'));
               	$shippingAdress->setData('company', $billingAddress->getData('company'));
               	$shippingAdress->setData('street', $billingAddress->getData('street'));
               	$shippingAdress->setData('city', $billingAddress->getData('city'));
               	$shippingAdress->setData('region', $billingAddress->getData('region'));
               	$shippingAdress->setData('region_id', $billingAddress->getData('region_id'));
               	$shippingAdress->setData('postcode', $billingAddress->getData('postcode'));
               	$shippingAdress->setData('country_id', $billingAddress->getData('country_id'));
               	if (is_array($socoShippingData) && isset($socoShippingData['CEPHONENUMBER'])) {
	               	$shippingAdress->setData('telephone', $socoShippingData['CEPHONENUMBER']);
               	} else {
	               	$shippingAdress->setData('telephone', $billingAddress->getData('telephone'));
               	}
                $shippingAdress->setData('save_in_address_book', 0);
			}
		}
    	}
        return $this;
    }

    /**
     * 
     * Sauvegarde les donnees de la commande propre a So Colissimo
     * @param $observer
     */
    
    public function addSocoAttributesToOrder($observer)
    {
    	try {
		    	$checkoutSession = Mage::getSingleton('checkout/session');
		    	$shippingData = $checkoutSession->getData('socolissimoliberte_shipping_data');
		    	
    			//on ne fait le traitement que si le mode d'expedition est socolissimo (et donc qu'on a recupere les donnees de socolissimo)
		    	if (isset($shippingData) && count($shippingData) > 0) {
			    	if (isset($shippingData['DELIVERYMODE'])) {
				    	$observer->getEvent()->getOrder()->setSocoProductCode($shippingData['DELIVERYMODE']);
			    	}
			
			    	if (isset($shippingData['CEDELIVERYINFORMATION'])) {
				    	$observer->getEvent()->getOrder()->setSocoShippingInstruction($shippingData['CEDELIVERYINFORMATION']);
			    	}
			
				    if (isset($shippingData['CEDOORCODE1'])) {	
				    	$observer->getEvent()->getOrder()->setSocoDoorCode1($shippingData['CEDOORCODE1']);
				    }
			    	
				    if (isset($shippingData['CEDOORCODE2'])) {
				    	$observer->getEvent()->getOrder()->setSocoDoorCode2($shippingData['CEDOORCODE2']);
				    }   	
			    	
				    if (isset($shippingData['CEENTRYPHONE'])) {
				    	$observer->getEvent()->getOrder()->setSocoInterphone($shippingData['CEENTRYPHONE']);
				    }
				    
				    if (isset($shippingData['PRID'])) {
			    		$observer->getEvent()->getOrder()->setSocoRelayPointCode($shippingData['PRID']);
				    }    	
				    
				    if (isset($shippingData['CECIVILITY'])) {
			    		$observer->getEvent()->getOrder()->setSocoCivility($shippingData['CECIVILITY']);
				    }
			
				    if (isset($shippingData['CEPHONENUMBER'])) {
			    		$observer->getEvent()->getOrder()->setSocoPhoneNumber($shippingData['CEPHONENUMBER']);
				    }
			
				    if (isset($shippingData['CEEMAIL'])) {
			    		$observer->getEvent()->getOrder()->setSocoEmail($shippingData['CEEMAIL']);
				    }
				    
		    	}
    	    } catch (Exception $e) {
    	    	Mage::Log('Failed to save so-colissimo data : '.print_r($shippingData, true));
    	}	    
    }
  
    public function resetSession($observer)
    {
		$checkoutSession = Mage::getSingleton('checkout/session');
    	$checkoutSession->setData('socolissimoliberte_shipping_data', array());
    }
    
    protected function _getSocoProductCode($type) {
    	if ($type=='poste') {
    		return 'BPR';
    	} elseif ($type=='cityssimo') {
    		return 'CIT';
    	} elseif ($type=='commercant') {
    		return 'A2P';
    	} elseif ($type=='rdv') {
    		return 'RDV';
    	} elseif ($type=='livraison') {
    		return Mage::getStoreConfig('carriers/socolissimoliberte/domicile_signature')?'DOS':'DOM';
    	} else {
    		return false;
    	}
    }
}
