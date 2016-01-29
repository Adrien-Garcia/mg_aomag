<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout status
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Jetpulp_Checkout_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{


    /**
     * getShippingRates custom all but the store pickup set in admin
     * @return array
     * @throws Exception
     */
    public function getShippingRatesButStorePickup()
    {
        $_rates = null;
        $groups = array();
        $store_pickup = Mage::getStoreConfig("shipping/jetcheckout/select_store_pickup");
        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();

            $catch = null;
            if (is_string($store_pickup) && preg_match("/(.+)_all/",$store_pickup, $catch)) {
                $_carriers = $this->getAddress()->getGroupedAllShippingRates();
                foreach($_carriers as $n => $_carrier) {
                    if( $catch[1] == $n ) {
                        unset($_carriers[$n]);
                    }else {
                        $groups[$n] = array();
                        foreach($_carrier as $r => $_rate){
                            $groups[$n][$_rate->getCode()] = $this->getAddress()->getShippingRateByCode($_rate->getCode());
                        }
                    }
                }
            } elseif (is_string($store_pickup) && !preg_match("/(.+)_all/",$store_pickup)) {
                $rate[$store_pickup] = $this->getAddress()->getShippingRateByCode($store_pickup);
                $groups[$rate[$store_pickup]->getCarrier()] = $rate;
            }

            return $_rates = $groups;
        }

        return $_rates;
    }
    /**
     * getShippingRates custom only the store pickup set in admin
     * @return array
     * @throws Exception
     */
    public function getShippingRatesOnlyStorePickup()
    {
        $_rates = null;
        $groups = array();
        $store_pickup = Mage::getStoreConfig("shipping/jetcheckout/select_store_pickup");
//        var_dump($store_pickup);
        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();

            $catch = null;
            if(is_string($store_pickup) && preg_match("/(.+)_all/",$store_pickup, $catch)) {
                $_carriers = $this->getAddress()->getGroupedAllShippingRates();
                $store_pickup = array();

                foreach($_carriers as $n => $_carrier) {
                    if ($catch[1] != $n) {
                        unset($_carriers[$n]);
                    } else {
                        $store_pickup[$n] = array();
                        foreach($_carrier as $r => $_rate){
                            $store_pickup[$n][] = $_rate->getCode();
                        }
                    }
                }

            }

            if(is_array($store_pickup)) {
                foreach($store_pickup as $n => $_rates) {
                    foreach($_rates as $r => $_code) {
                        $groups[$_code] = $this->getAddress()->getShippingRateByCode($_code);
                    }
                }
            }else{
                $groups[$store_pickup] = $this->getAddress()->getShippingRateByCode($store_pickup);
            }

            return $_rates = $groups;
        }

        return $_rates;
    }


    public function isUseBillingAddressForShipping()
    {
        if (($this->getQuote()->getIsVirtual())
            || !$this->getQuote()->getShippingAddress()->getSameAsBilling()) {
            return false;
        }
        return true;
    }

}
