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

    protected $_rates_butStorePickup;
    protected $_rates_storePickup;

    /**
     * getShippingRates custom all but the store pickup set in admin
     * @return array
     * @throws Exception
     */
    public function getShippingRatesButStorePickup()
    {
        if (!isset($this->_rates_butStorePickup)) {

            $groups = array();
            $store_pickup = Mage::getStoreConfig("shipping/jetcheckout/select_store_pickup");

            $this->getAddress()->collectShippingRates()->save();

            $_carriers = $this->getAddress()->getGroupedAllShippingRates();
            if (!empty($_carriers)) {
                foreach ($_carriers as $n => $_carrier) {
                    $carrierCode = $_carrier[0]->getData('code');
                    if ($carrierCode != $store_pickup) {
                        $groups[$n] = array();
                        foreach ($_carrier as $r => $_rate) {
                            $groups[$n][$_rate->getCode()] = $this->getAddress()->getShippingRateByCode($_rate->getCode());
                        }
                    }
                }
            }

            $this->_rates_butStorePickup = $groups;
        }

        return $this->_rates_butStorePickup;
    }
    /**
     * getShippingRates custom only the store pickup set in admin
     * @return array
     * @throws Exception
     */
    public function getShippingRatesOnlyStorePickup()
    {

        if (!isset($this->_rates_storePickup)) {

            $groups = array();
            if ($store_pickup = Mage::getStoreConfig("shipping/jetcheckout/select_store_pickup")) {
                $this->getAddress()->collectShippingRates()->save();

                $groups[$store_pickup] = $this->getAddress()->getShippingRateByCode($store_pickup);

            }
            $this->_rates_storePickup = $groups;
        }

        return $this->_rates_storePickup;
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
