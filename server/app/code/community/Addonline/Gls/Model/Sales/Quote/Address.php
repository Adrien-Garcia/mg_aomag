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
//         	$aOrderRatesGLS['ordertoyou'] = $carrier->getConfigData('ordertoyou');
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
					$rates['gls'][$aOrderRatesGLS['orderrelay']] = $orderedRate;
				}
// 				if(strpos($sCode,'ls_toyou') > 0){
// 					$rates['gls'][$aOrderRatesGLS['ordertoyou']] = $orderedRate;
// 				}
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
