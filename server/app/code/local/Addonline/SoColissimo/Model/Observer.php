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
class Addonline_SoColissimo_Model_Observer extends Varien_Object
{
    
    // const CONTRAT_BOTH = 0;
    const CONTRAT_FLEXIBILITE = 1;

    const CONTRAT_LIBERTE = 2;

    const CONTRAT_FLEXIBILITE_MULTI = 3;

    const CONTRAT_LIBERTE_MULTI = 4;

    const CONTRAT_FLEXIBILITE_EAN = "SoColissimoFlexibilite";

    const CONTRAT_LIBERTE_EAN = "SoColissimoLiberte";

    const CONTRAT_FLEXIBILITE_MULTI_EAN = "SoColissimoFlexibiliteMultisite";

    const CONTRAT_LIBERTE_MULTI_EAN = "SoColissimoLiberteMultisite";

    const INBOX_ERREUR_TITLE = "Le module So Colissimo n'a pas une clé licence valide pour le magasin __storeCode__ .";

    /**
     * retourn le titre qu'on va mettre à la notification Magento
     * 
     * @param unknown $storeCode            
     */
    public function getNotificationTitle ($storeCode)
    {
        return str_replace("__storeCode__", $storeCode, self::INBOX_ERREUR_TITLE);
    }

    /**
     * retourne une url au format attendu pour faire le checksum de la clé ( = ww.abc.com )
     * 
     * @param unknown $url            
     */
    public function _prepareUrl ($url)
    {
        $url = strtolower($url);
        $domainname = preg_replace("/^[\w\:\/]*\/\/?([\w\d\.\-]+).*\/*$/", "$1", $url);
        return preg_replace("/^([\w\d\.\-]+).*\/*$/", "$1", $domainname);
    }

    /**
     * on marque les notifications qui concerne le store $storeCode comme lu
     * a noter qu'on trouve les notifications concernant notre store d'apres le titre de la notification
     * 
     * @param unknown $storeCode            
     */
    public function _removeNotificationsOfStore ($storeId)
    {
        $this->licenceLog("_removeNotificationsOfStore() pour $storeId");
        $store = Mage::getModel('core/store')->load($storeId);
        $_unreadNotices = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('is_read', 
            0);
        $title = $this->getNotificationTitle($store['code']);
        
        foreach ($_unreadNotices as $notice) {
            if ($notice->getData('title') == $title) {
                $notice->setIsRead(1)->save();
            }
        }
    }

    /**
     *
     * @param unknown $toCheckStoreId            
     */
    public function _addNotificationToStore ($toCheckStoreId)
    {
        $storeErreur = Mage::getModel('core/store')->load($toCheckStoreId);
        $storeErreurKey = trim(Mage::getStoreConfig('socolissimo/licence/serial', $storeErreur));
        // on a pas trouvé la clé
        if ($storeErreurKey != 'DISABLED') {
            // le soco du store a une clé, donc y a une vrai erreur
            $severity = Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;
            
            $description = "Vous devez renseigner une clé licence valide pour le module So Colissimo pour le magasin " .
                 $storeErreur['code'] . ". Le module a été désactivé.";
            $title = $this->getNotificationTitle($storeErreur['code']);
            $date = date('Y-m-d H:i:s');
            Mage::getModel('adminnotification/inbox')->parse(
                array(
                        array(
                                'severity' => $severity,
                                'date_added' => $date,
                                'title' => $title,
                                'description' => $description,
                                'url' => '',
                                'internal' => true
                        )
                ));
        }
    }

    /**
     * test pour un store (ou pas), un type de contrat
     * donc si on a le storeid, on prend sa licence et on la test pour le $contrat
     * si on a pas de storeid alors, on prend la licence de tous les stores et on la test pour le $contrat
     * 
     * @param unknown $toCheckStoreId            
     * @param unknown $contrat            
     * @return boolean
     */
    public function _9cd4777ae76310fd6977a5c559c51821 ($toCheckStoreId, $contrat)
    {
        if (Mage::getStoreConfig('addonline/licence/aomagento')) {
            // return true;
        }
        
        $contratPossibles = array();
        $contratPossibles[self::CONTRAT_FLEXIBILITE] = self::CONTRAT_FLEXIBILITE_EAN;
        $contratPossibles[self::CONTRAT_LIBERTE] = self::CONTRAT_LIBERTE_EAN;
        $contratPossibles[self::CONTRAT_FLEXIBILITE_MULTI] = self::CONTRAT_FLEXIBILITE_MULTI_EAN;
        $contratPossibles[self::CONTRAT_LIBERTE_MULTI] = self::CONTRAT_LIBERTE_MULTI_EAN;
        
        // si on a le module de licence AO alors on dit que la licence est toujours bonne
        
        $key = 'e983cfc54f88c7114e99da95f5757df6';
        $store_error = null;
        
        // si on a a précisé le store
        if (! is_object($toCheckStoreId)) {
            $store = Mage::getModel('core/store')->load($toCheckStoreId);
            $storeUrl = $this->_prepareUrl($store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
            $storeKey = trim(Mage::getStoreConfig('socolissimo/licence/serial', $store));
            
            // on ne trouve pas le ean " = le nom en francais du contrat", on met un texte bidon
            // au niveau code c'est pas top, mais pour la relecture du code obfuscated ca complique la lecture
            if (! isset($contratPossibles[$contrat])) {
                $contratPossibles[$contrat] = rand(10000000000000000000, 10000000000000000000000);
            }
            if (md5($storeUrl . $key . $contratPossibles[$contrat]) === $storeKey) {
                return true;
            }
        }         // pas de store précis, on test sur tous les stores
        else {
            $stores = Mage::getModel('core/store')->getCollection();
            foreach ($stores as $store) {
                $store = Mage::getModel('core/store')->load($toCheckStoreId);
                $storeUrl = $this->_prepareUrl($store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
                $storeKey = trim(Mage::getStoreConfig('socolissimo/licence/serial', $store));
                
                // on ne trouve pas le ean " = le nom en francais du contrat", on met un texte bidon
                // au niveau code c'est pas top, mais pour la relecture du code obfuscated ca complique la lecture
                if (! isset($contratPossibles[$contrat])) {
                    $contratPossibles[$contrat] = rand(10000000000000000000, 10000000000000000000000);
                }
                if (md5($storeUrl . $key . $contratPossibles[$contrat]) === $storeKey) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * test le numéro de licence pour le FO pour un store précis
     * 
     * @param integer $toCheckStoreId            
     * @return boolean
     */
    public function _9cd4777ae76310fd6977a5c559c51820 ($toCheckStoreId)
    {
        $this->licenceLog("_9cd4777ae76310fd6977a5c559c51820() start pour le storeId " . $toCheckStoreId);
        // si on a le module de licence AO alors on dit que la licence est toujours bonne
        if (Mage::getStoreConfig('addonline/licence/aomagento')) {
            // $this->licenceLog("return true licence/aomagento présent");
            // return true;
        }
        
        $key = 'e983cfc54f88c7114e99da95f5757df6';
        $store_error = null;
        
        $contratPossibles = array();
        $contratPossibles[self::CONTRAT_FLEXIBILITE] = self::CONTRAT_FLEXIBILITE_EAN;
        $contratPossibles[self::CONTRAT_LIBERTE] = self::CONTRAT_LIBERTE_EAN;
        
        $isKeyValide = false;
        $isKeyMulti = null;
        $valideStoreIds = array();
        $keyIsForEan = "";
        
        // on veut tous les stores
        $stores = Mage::getModel('core/store')->getCollection();
        
        $this->licenceLog("debut test pour mono site");
        
        // on va tester dans un premier temps les cles contrat mono site
        // on test pour tous les types de contrats
        foreach ($contratPossibles as $contratPossibleKey => $contratPossibleEan) {
            // on test tous les stores
            foreach ($stores as $store) {
                // pour chaque store on va donc tester la licence vs clé mono site et vs clé multisite
                $storeUrl = $this->_prepareUrl($store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
                $storeKey = trim(Mage::getStoreConfig('socolissimo/licence/serial', $store));
                if ($storeKey != 'DISABLED') {
                    if (md5($storeUrl . $key . $contratPossibleEan) === $storeKey) {
                        $isKeyValide = true;
                        $isKeyMulti = false;
                        $valideStoreIds[] = $store->getStoreId();
                        $keyIsForEan = $contratPossibleEan;
                        $this->licenceLog(
                            "licence valide trouvee pour le store " . $store->getStoreId() . " ( " . $store['code'] . ")");
                    }
                }
            }
        }
        
        $this->licenceLog("fin test pour mono site");
        
        // pas trouvé de clé valide mono site on recherche en multi
        if (! $isKeyValide) {
            
            $this->licenceLog("debut test pour multi sites");
            
            // on prend va tester que dans les contrats multi
            $contratPossibles = array();
            $contratPossibles[self::CONTRAT_FLEXIBILITE_MULTI] = self::CONTRAT_FLEXIBILITE_MULTI_EAN;
            $contratPossibles[self::CONTRAT_LIBERTE_MULTI] = self::CONTRAT_LIBERTE_MULTI_EAN;
            
            foreach ($contratPossibles as $contratPossibleKey => $contratPossibleEan) {
                // on test tous les stores
                foreach ($stores as $store) {
                    // pour chaque store on va donc tester la licence vs clé multi site et vs clé multisite
                    $storeUrl = $this->_prepareUrl($store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
                    $storeKey = trim(Mage::getStoreConfig('socolissimo/licence/serial', $store));
                    
                    if ($storeKey != 'DISABLED') {
                        if (md5($storeUrl . $key . $contratPossibleEan) === $storeKey) {
                            $isKeyValide = true;
                            $isKeyMulti = TRUE;
                            $valideStoreIds[] = $store->getStoreId();
                            $keyIsForEan = $contratPossibleEan;
                            $this->licenceLog(
                                "licence valide trouvee pour le store " . $store->getStoreId() . " ( " . $store['code'] .
                                     ")");
                        }
                    }
                }
            }
        }
        
        $this->licenceLog("fin test pour multi sites");
        
        if ($isKeyValide) {
            $t = "licence valide pour $keyIsForEan";
            if ($isKeyMulti) {
                $t .= ", c'est du multisite";
            } else {
                $t .= ", c'est du monosite";
            }
        } else {
            $t = "pas de licence";
        }
        $this->licenceLog($t);
        $this->licenceLog(" on vs avec $toCheckStoreId et y a " . count($valideStoreIds) . " store valide");
        
        // est ce qu'on a une clé valide ?
        if ($isKeyValide) {
            // si on est mono site alors on vérifie que la clé valide est bien celle du site sur lequel on est
            if (! $isKeyMulti) {
                if (in_array($toCheckStoreId, $valideStoreIds)) {
                    $retour = true;
                } else {
                    $retour = false;
                }
            } else {
                $retour = true;
            }
        }         // on n'a pas de clé
        else {
            $retour = false;
        }
        
        $storeErreur = Mage::getModel('core/store')->load($toCheckStoreId);
        $storeErreurKey = trim(Mage::getStoreConfig('socolissimo/licence/serial', $storeErreur));
        // on a pas trouvé la clé
        if (! $retour) {
            // le soco du store a une clé, donc y a une vrai erreur
            if ($storeErreurKey != 'DISABLED') {
                // on previent en mettant un msg dans le bo que la clé n'est pas bonne pour ce store
                $this->_addNotificationToStore($toCheckStoreId);
                return false;
            }             // donc la clé n'a pas été trouvé mais le soco est marqué disabled, on retourne false
            else {
                return false;
            }
        }         // pas d'erreur ? on retourne true alors
        else {
            return true;
        }
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

    private function licenceLog ($t)
    {
        Mage::log($t, null, 'socoLicence.log');
    }
}
