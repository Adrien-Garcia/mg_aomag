<?php

class Addonline_Socolissimo_Model_Shipping_Rate_Result extends Mage_Shipping_Model_Rate_Result
{
  
    /**
     * Sort rates by price from min to max
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function sortRatesByPrice()
    {
     	if (!is_array($this->_rates) || !count($this->_rates)) {
             return $this;
         }

         $rate = $this->_rates[0];
         if ($rate->carrier == 'socolissimo') {
         	
         	foreach ($this->_rates as $i => $rate) {
         		$method = $rate->getMethod();
         		$methodOrder = 9;
         		if (strpos($method, 'livraison')===0) {
         			$methodOrder = 1;
         		}
         	    if (strpos($method, 'rdv')===0) {
         			$methodOrder = 2;
         		}
         	    if (strpos($method, 'cityssimo')===0) {
         			$methodOrder = 3;
         		}
         	    if (strpos($method, 'poste')===0) {
         			$methodOrder = 4;
         		}
         	    if (strpos($method, 'commercant')===0) {
         			$methodOrder = 5;
         		}
         		$tmp[$i]=$methodOrder;
         	}
         	
         	natsort($tmp);
         	
         	foreach ($tmp as $i => $order) {
         		$result[] = $this->_rates[$i];
         	}
         	
         	$this->reset();
         	$this->_rates = $result;
         	
         	return $this;
         } else {
        	return parent::sortRatesByPrice();
        }
        
    }

}
