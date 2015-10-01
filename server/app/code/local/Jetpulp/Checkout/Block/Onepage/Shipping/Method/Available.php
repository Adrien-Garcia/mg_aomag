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
        $store_pickup = Mage::getStoreConfig("shipping/jetcheckout/select_store_pickup");
        $methods = Mage::getSingleton('shipping/config')->getAllCarriers();
        $options = array();
        foreach($methods as $_code => $_method)
        {
            $_active = Mage::getStoreConfig("carriers/$_code/active");
            $_active = ($_active) ? 'active' : 'inactive';
            if($_code != $store_pickup && $_active) {
                $options[] = $_code;
            }
        }


        if (empty($this->_rates)) {
            $this->getAddress()->setLimitCarrier($options);
            $this->getAddress()->collectShippingRates()->save();

            $groups = $this->getAddress()->getGroupedAllShippingRates();
//            var_dump(array_keys($groups));

            /*
            if (!empty($groups)) {
                $ratesFilter = new Varien_Filter_Object_Grid();
                $ratesFilter->addFilter(Mage::app()->getStore()->getPriceFilter(), 'price');

                foreach ($groups as $code => $groupItems) {
                    $groups[$code] = $ratesFilter->filter($groupItems);
                }
            }
            */

            return $this->_rates = $groups;
        }

        return $this->_rates;
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

            if(is_array($store_pickup)) {
                foreach($store_pickup as $n => $_code) {
                    $groups[$_code] = $this->getAddress()->getShippingRateByCode($_code);
                }
            }else{
                $groups[$store_pickup] = $this->getAddress()->getShippingRateByCode($store_pickup);
            }

//            var_dump(array_keys($groups));
            /*
            if (!empty($groups)) {
                $ratesFilter = new Varien_Filter_Object_Grid();
                $ratesFilter->addFilter(Mage::app()->getStore()->getPriceFilter(), 'price');

                foreach ($groups as $code => $groupItems) {
                    $groups[$code] = $ratesFilter->filter($groupItems);
                }
            }
            */

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
