<?php

class Addonline_Gls_Model_Sales_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    public function getGroupedAllShippingRates()
    {
        $rates = array();
        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted() && $rate->getCarrierInstance()) {
                if (!isset($rates[$rate->getCarrier()])) {
                    $rates[$rate->getCarrier()] = array();
                }

                $rates[$rate->getCarrier()][] = $rate;
                $rates[$rate->getCarrier()][0]->carrier_sort_order = $rate->getCarrierInstance()->getSortOrder();
            }
        }
        uasort($rates, array($this, '_sortRates'));

        /*
         * On tri selon les paramètres du Back office
         */
        $aOrderRatesGLS = array();
        if(isset($rates['gls'])){
        	$carrier = $rates['gls'][0]->getCarrierInstance();
        	$aOrderRatesGLS['ordertohome'] = $carrier->getConfigData('ordertohome');
        	$aOrderRatesGLS['ordertoyou'] = $carrier->getConfigData('ordertoyou');
        	$aOrderRatesGLS['orderrelay'] = $carrier->getConfigData('orderrelay');
        }

        if(count($aOrderRatesGLS)){
        	$aOrderedRatesGLS = array();
        	foreach($rates['gls'] as $key => $orderedRate){
        		$aOrderedRatesGLS[$orderedRate->getData('code')] = $orderedRate;
        	}

        	foreach($rates['gls'] as $key => $orderedRate){
        		$sCode = $orderedRate->getData('code');
				if(strpos($sCode,'ls_relay') > 0){
					Mage::log($sCode, null, 'gls_import.log');
					$rates['gls'][$aOrderRatesGLS['orderrelay']] = $orderedRate;
				}
				if(strpos($sCode,'ls_toyou') > 0){
					$rates['gls'][$aOrderRatesGLS['ordertoyou']] = $orderedRate;
				}
				if(strpos($sCode,'ls_tohome') > 0){
					$rates['gls'][$aOrderRatesGLS['ordertohome']] = $orderedRate;
				}
        	}

        	unset($rates['gls'][0]);
        }
        /*
         * Fin du tri
         */

        return $rates;
    }
}
