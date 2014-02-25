<?php
if ((string)Mage::getConfig()->getModuleConfig('Idev_OneStepCheckout')->active != 'true')
{
	class Idev_OneStepCheckout_Helper_Checkout extends Mage_Core_Helper_Abstract{}
}
class Addonline_SoColissimo_Helper_OneStepCheckout_Checkout extends Idev_OneStepCheckout_Helper_Checkout
{

    public function saveShipping($data, $customerAddressId)
    {
    	$shipping_data = Mage::getSingleton('checkout/session')->getData('socolissimo_livraison_relais');
    	if($shipping_data)return array();
    	else return parent::saveShipping($data, $customerAddressId);
    }
}
