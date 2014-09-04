<?php

/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Objet Observer
 *
 * @category Addonline
 * @package Addonline_SoColissimo
 * @copyright Copyright (c) 2014 Addonline
 * @author Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Observer extends Varien_Object implements Addonline_Licence_Model_ModuleLicenceConfig
{

    const CONTRAT_FLEXIBILITE = 1;

    const CONTRAT_LIBERTE = 2;

    const CONTRAT_FLEXIBILITE_MULTI = 3;

    const CONTRAT_LIBERTE_MULTI = 4;

    const CONTRAT_FLEXIBILITE_EAN = "SoColissimoFlexibilite";

    const CONTRAT_LIBERTE_EAN = "SoColissimoLiberte";

    const CONTRAT_FLEXIBILITE_MULTI_EAN = "SoColissimoFlexibiliteMultisite";

    const CONTRAT_LIBERTE_MULTI_EAN = "SoColissimoLiberteMultisite";

    /**
     * rettourne des infos sur le module
     *
     * @see Addonline_Licence_Model_ModuleLicenceConfig::getLicenceInfoConfig()
     */
    public function getLicenceInfoConfig ($what, $store = null)
    {
        switch ($what) {
            case "licence/serial":
                return trim(Mage::getStoreConfig('socolissimo/licence/serial', $store));
                break;
            
            case "module/version":
                return Mage::getConfig()->getNode('modules/Addonline_SoColissimo/version');
                break;
            
            case "module/keymaster":
                return "e983cfc54f88c7114e99da95f5757df6";
                break;
            
            case "module/name":
                return "So Colissimo";
                break;
            
            case "notification/licence/error/title":
                return "Le module So Colissimo n'a pas une clé licence valide pour le magasin __storeCode__ .";
                break;
            
            default:
                return NULL;
                break;
        }
    }

    /**
     * retourne un tableau des licences de ce module sous a la forme [licence_id] = licence_txt
     * on peut récuperer soit toutes, les mono sites ou les multi sites
     *
     * @see Addonline_Licence_Model_LicenceConfig::getLicenceContrats()
     */
    public function getLicenceContrats ($which = self::GET_CONTRAT_ALL)
    {
        $contratPossibles = array();
        
        switch ($which) {
            case self::GET_CONTRAT_ALL:
            case self::GET_CONTRAT_MONO:
                $contratPossibles[self::CONTRAT_FLEXIBILITE] = self::CONTRAT_FLEXIBILITE_EAN;
                $contratPossibles[self::CONTRAT_LIBERTE] = self::CONTRAT_LIBERTE_EAN;
                
                // si on veut tous les contrats, on ne fait pas de break, on continue
                if ($which != self::GET_CONTRAT_ALL) {
                    break;
                }
            
            case self::GET_CONTRAT_ALL:
            case self::GET_CONTRAT_MULTI:
                $contratPossibles[self::CONTRAT_FLEXIBILITE_MULTI] = self::CONTRAT_FLEXIBILITE_MULTI_EAN;
                $contratPossibles[self::CONTRAT_LIBERTE_MULTI] = self::CONTRAT_LIBERTE_MULTI_EAN;
                
                // si on veut tous les contrats, on ne fait pas de break, on continue (sert à rien pour l'instant, car y
                // a rien dessous)
                if ($which != self::GET_CONTRAT_ALL) {
                    break;
                }
        }
        
        return $contratPossibles;
    }

    /**
     * Enter Description here
     *
     * @param unknown $observer            
     * @return Addonline_SoColissimo_Model_Observer
     */
    public function checkoutEventSocodata ($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $request = Mage::app()->getRequest();
        
        // si on n'a pas le paramètre shipping_method c'est qu'on n'est pas sur la requête de mise à jour du mode de
        // livraison
        // dans ce cas on ne change rien
        if (! $request->getParam('shipping_method')) {
            return $this;
        }
        
        $telephone = $request->getParam('tel_socolissimo');
        $idRelais = $request->getParam('relais_socolissimo');
        $reseau = $request->getParam('reseau_socolissimo');
        $shippingAddress = $quote->getShippingAddress();
        $shippingMethod = $shippingAddress->getShippingMethod();
        
        $socoShippingData = Mage::getSingleton('checkout/session')->getData('socolissimo_shipping_data');
        // on positionne l'identitifant relais précédent si il existe
        $relaisPrecedent = null;
        if (is_array($socoShippingData) && isset($socoShippingData['PRID'])) {
            $relaisPrecedent = $socoShippingData['PRID'];
        }
        
        if (strpos($shippingMethod, 'socolissimo_') === 0) {
            
            $typeSocolissimo = explode("_", $shippingMethod);
            $typeSocolissimo = $typeSocolissimo[1];
            
            // on réinitilaise les données Socolissimo en session
            $socoShippingData = array();
            Mage::getSingleton('checkout/session')->setData('socolissimo_shipping_data', $socoShippingData);
            
            $arrayAddressData = array();
            $street = array();
            $customerNotesArray = array();
            
            $socoShippingData['CEDELIVERYINFORMATION'] = '';
            $socoShippingData['CEDOORCODE1'] = '';
            $socoShippingData['CEDOORCODE2'] = '';
            $socoShippingData['CEENTRYPHONE'] = '';
            $socoShippingData['CECIVILITY'] = '';
            // $socoShippingData['CEEMAIL'] = $quote->getCustomer()->getData('email');
            $socoShippingData['CEEMAIL'] = $quote->getCustomerEmail();
            
            if ($telephone) {
                $arrayAddressData['telephone'] = $telephone;
                $socoShippingData['CEPHONENUMBER'] = $telephone;
            }
            
            $socoShippingData['DELIVERYMODE'] = $this->_getSocoProductCode($typeSocolissimo);
            if ($typeSocolissimo == 'rdv') {
                $customerNotesArray['0'] = 'Livraison sur rendez-vous : ' . $telephone;
                $socoShippingData['CEDELIVERYINFORMATION'] = 'Prise de rendez-vous : ' . $telephone;
            }
            
            $relaisFound = false;
            if (strpos($shippingMethod, 'socolissimo_poste') === 0 ||
                 strpos($shippingMethod, 'socolissimo_cityssimo') === 0 ||
                 strpos($shippingMethod, 'socolissimo_commercant') === 0) {
                if (Mage::helper('socolissimo')->isFlexibilite()) {
                    $relais = Mage::getSingleton('socolissimo/flexibilite_service')->findPointRetraitAcheminementByID(
                        $idRelais, $reseau);
                    $relaisFound = ($relais instanceof Addonline_SoColissimo_Model_Flexibilite_Relais);
                } else {
                    $relais = Mage::getModel('socolissimo/liberte_relais')->loadByIdentifiantReseau($idRelais, $reseau);
                    $relaisFound = $relais->getId();
                }
            }
            
            if ($relaisFound) {
                
                $socoShippingData['PRID'] = $relais->getIdentifiant();
                $socoShippingData['DELIVERYMODE'] = $relais->getTypeRelais(); // on écrase pour mettre les bons types
                                                                              // pour la belgique
                
                $arrayAddressData['customer_address_id'] = null;
                
                $billingAddress = $quote->getBillingAddress();
                $arrayAddressData['lastname'] = $billingAddress->getLastname();
                $arrayAddressData['firstname'] = $billingAddress->getFirstname();
                $arrayAddressData['company'] = $relais->getLibelle();
                $arrayAddressData['city'] = $relais->getCommune();
                $arrayAddressData['postcode'] = $relais->getCodePostal();
                $arrayAddressData['telephone'] = $telephone;
                
                $street['0'] = $relais->getAdresse();
                $street['1'] = $relais->getAdresse1();
                $street['2'] = $relais->getAdresse2();
                $street['3'] = $relais->getAdresse3();
                
                $shippingAddress->setStreet($street); // on appelle setStreet directement sur l'objet address au lieu de
                                                      // passer par addData, pour la gestion en plusieurs lignes
                
                $customerNotesArray['0'] = 'Livraison relais colis socolissimo : ' . $relais->getIdentifiant();
                
                $arrayAddressData['save_in_address_book'] = 0;
                Mage::getSingleton('checkout/session')->setData('socolissimo_livraison_relais', 
                    $relais->getIdentifiant());
            } else {
                $socoShippingData['PRID'] = '';
            }
            
            // on initialise les données socolissimo en session
            Mage::getSingleton('checkout/session')->setData('socolissimo_shipping_data', $socoShippingData);
            
            if (! empty($customerNotesArray)) {
                $arrayAddressData['customer_notes'] = implode("\n", $customerNotesArray);
            }
            
            // sauvegarder l'adresse
            $shippingAddress->addData($arrayAddressData);
        }
        
        if ($relaisPrecedent && $relaisPrecedent != '') {
            if (! isset($socoShippingData['PRID']) || $socoShippingData['PRID'] == '') {
                // si l'adresse de livraison était un relais et que maintenant ça ne l'est plus il faut remettre
                // l'adresse de facturation :
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
        
        return $this;
    }

    /**
     * Sauvegarde les donnees de la commande propre a So Colissimo
     *
     * @param unknown $observer            
     */
    public function addSocoAttributesToOrder ($observer)
    {
        try {
            Mage::getSingleton('checkout/session')->setData('socolissimo_livraison_relais', null);
            $checkoutSession = Mage::getSingleton('checkout/session');
            $shippingData = $checkoutSession->getData('socolissimo_shipping_data');
            
            // on ne fait le traitement que si le mode d'expedition est socolissimo (et donc qu'on a recupere les
            // donnees de socolissimo)
            if (isset($shippingData) && count($shippingData) > 0) {
                if (isset($shippingData['DELIVERYMODE'])) {
                    $observer->getEvent()
                        ->getOrder()
                        ->setSocoProductCode($shippingData['DELIVERYMODE']);
                }
                
                if (isset($shippingData['CEDELIVERYINFORMATION'])) {
                    $observer->getEvent()
                        ->getOrder()
                        ->setSocoShippingInstruction($shippingData['CEDELIVERYINFORMATION']);
                }
                
                if (isset($shippingData['CEDOORCODE1'])) {
                    $observer->getEvent()
                        ->getOrder()
                        ->setSocoDoorCode1($shippingData['CEDOORCODE1']);
                }
                
                if (isset($shippingData['CEDOORCODE2'])) {
                    $observer->getEvent()
                        ->getOrder()
                        ->setSocoDoorCode2($shippingData['CEDOORCODE2']);
                }
                
                if (isset($shippingData['CEENTRYPHONE'])) {
                    $observer->getEvent()
                        ->getOrder()
                        ->setSocoInterphone($shippingData['CEENTRYPHONE']);
                }
                
                if (isset($shippingData['PRID'])) {
                    $observer->getEvent()
                        ->getOrder()
                        ->setSocoRelayPointCode($shippingData['PRID']);
                }
                
                if (isset($shippingData['CECIVILITY'])) {
                    $observer->getEvent()
                        ->getOrder()
                        ->setSocoCivility($shippingData['CECIVILITY']);
                }
                
                if (isset($shippingData['CEPHONENUMBER'])) {
                    $observer->getEvent()
                        ->getOrder()
                        ->setSocoPhoneNumber($shippingData['CEPHONENUMBER']);
                }
                
                if (isset($shippingData['CEEMAIL'])) {
                    $observer->getEvent()
                        ->getOrder()
                        ->setSocoEmail($shippingData['CEEMAIL']);
                }
            }
        } catch (Exception $e) {
            Mage::Log('Failed to save so-colissimo data : ' . print_r($shippingData, true), null, 'socolissimo.log');
        }
    }

    /**
     * enleve les données soco de la session
     *
     * @param unknown $observer            
     */
    public function resetSession ($observer)
    {
        $checkoutSession = Mage::getSingleton('checkout/session');
        $checkoutSession->setData('socolissimoliberte_shipping_data', array());
    }

    /**
     * renvoie le productCode SoColissimo
     *
     * @param unknown $type            
     * @return string|boolean
     */
    protected function _getSocoProductCode ($type)
    {
        if ($type == 'poste') {
            return 'BPR';
        } elseif ($type == 'cityssimo') {
            return 'CIT';
        } elseif ($type == 'commercant') {
            return 'A2P';
        } elseif ($type == 'rdv') {
            return 'RDV';
        } elseif ($type == 'domicile') {
            return Mage::getStoreConfig('carriers/socolissimo/domicile_signature') ? 'DOS' : 'DOM';
        } else {
            return false;
        }
    }
}
