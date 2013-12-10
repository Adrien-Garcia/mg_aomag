<?php
/**
 * Addonline_Gls
 *
 * @category    Addonline
 * @package     Addonline_Gls
 * @copyright   Copyright (c) 2013 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 */

class Addonline_Gls_Model_Observer
{

	const CONTRAT_BOTH = 0;
	const CONTRAT_FLEXIBILITE = 1;
	const CONTRAT_LIBERTE = 2;

	public function checkoutEventGlsdata($observer)
	{

		$quote = $observer->getEvent()->getQuote();
		$request = Mage::app()->getRequest();

		//si on n'a pas le paramètre shipping_method c'est qu'on n'est pas sur la requête de mise à jour du mode de livraison
		//dans ce cas on ne change rien
		if (!$request->getParam('shipping_method')) {
			return $this;
		}

		$shippingAddress = $quote->getShippingAddress();
		$shippingMethod = $shippingAddress->getShippingMethod();

		return $this;
	}

	/**
	 *
	 * Sauvegarde les donnees de la commande propre a So Colissimo
	 * @param $observer
	 */

	/* public function addSocoAttributesToOrder($observer)
	{
		try {
			$checkoutSession = Mage::getSingleton('checkout/session');
			$shippingData = $checkoutSession->getData('socolissimo_shipping_data');

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
		} elseif ($type=='domicile') {
			return Mage::getStoreConfig('carriers/socolissimo/domicile_signature')?'DOS':'DOM';
		} else {
			return false;
		}
	}*/
}
