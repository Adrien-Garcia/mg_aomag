<?php
class Addonline_Socolissimo_Block_OneStepCheckout_Checkout extends Idev_OneStepCheckout_Block_Checkout
{


    public function differentShippingAvailable()
    {
    	
    	// dans le cas où on livre dans un relais colis SoColissimo, on se comporte comme si la livraison 
    	//dans une adresse différente de l'adresse de facturation n'était pas possible pour éviter d'écraser 
    	//l'addresse du relais colis qui a été enregistrée auparavent
    	$request = Mage::app()->getRequest();
    	$shippingMethod = $request->getParam('shipping_method');
    	if (strpos($shippingMethod,'socolissimo_')===0) {
	    	$idRelais = $request->getParam('relais_socolissimo');
	    	if ($idRelais) {
		    	return false;
	    	}
    	}
        return parent::differentShippingAvailable();
    }

}
