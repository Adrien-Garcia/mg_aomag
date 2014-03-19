<?php

/**
 * 1997-2013 Quadra Informatique
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is available
 * through the world-wide-web at this URL: http://www.opensource.org/licenses/OSL-3.0
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to modules@quadra-informatique.fr so we can send you a copy immediately.
 *
 * @author Quadra Informatique <modules@quadra-informatique.fr>
 * @copyright 1997-2013 Quadra Informatique
 * @license http://www.opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class Quadra_Paybox_Model_Observer
{

    /**
     *  Can redirect to Paybox payment
     */
    public function initRedirect(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('checkout/session')->setCanRedirect(true);
    }

    /**
     *  Return Orders Redirect URL
     *
     *  @return	  string Orders Redirect URL
     */
    public function multishippingRedirectUrl(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('checkout/session')->getCanRedirect()) {
            $orderIds = Mage::getSingleton('core/session')->getOrderIds();
            $orderIdsTmp = $orderIds;
            $key = array_pop($orderIdsTmp);
            $order = Mage::getModel('sales/order')->loadByIncrementId($key);

            if ($order->getPayment()->getMethod() == 'paybox_system') {
                Mage::getSingleton('checkout/session')->setRealOrderIds(implode(',', $orderIds));
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('paybox/system/redirect'));
            }
        } else {
            Mage::getSingleton('checkout/session')->unsRealOrderIds();
        }

        return $this;
    }

    /**
     *  Disables sending email after the order creation
     *
     *  @return	  updated order
     */
    public function disableEmailForMultishipping(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();

        if ($order->getPayment()->getMethod() == 'paybox_system') {
            $order->setCanSendNewEmailFlag(false)->save();
        }

        return $this;
    }

}
