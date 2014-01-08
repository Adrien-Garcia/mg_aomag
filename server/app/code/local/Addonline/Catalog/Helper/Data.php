<?php

class Addonline_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     *  
     * @param $string
     * @param $nbWords
     * @return string
     */
	function getLimitedWordsFromString($string, $nbWords) {
		$words = explode(' ', strip_tags($string));
		return implode(' ', array_slice($words, 0, $nbWords));
	}
    
    /**
     *  
     * @param $string
     * @param $nbCars
     * @return string
     */
	function getHtmlSubString($string, $nbCars) {
		$text = html_entity_decode(strip_tags($string),ENT_QUOTES,'UTF-8');
		if (strlen($text)>$nbCars){
			$texteDecoupe = substr($text, 0, $nbCars);
			$positionDernierEspace = strrpos($texteDecoupe, ' ');
			$text = str_replace('&amp;rsquo;','\'',htmlentities(substr($texteDecoupe, 0, $positionDernierEspace),ENT_QUOTES,'UTF-8')).'...';
			return $text;
		}
		return $string;
	}
    
    
     /**
     * Retrieve pourcentage reduction
     *
     * @return string
     */
     public function getReductionPercent($_product)
    {
    	$_regularPrice = Mage::helper('tax')->getPrice($_product, $_product->getPrice(), Mage::helper('tax')->displayPriceIncludingTax() || Mage::helper('tax')->displayBothPrices());
    	$_finalPrice = Mage::helper('tax')->getPrice($_product, $_product->getFinalPrice()); 
		if ($_finalPrice<$_regularPrice) {	
    		return round(($_regularPrice - $_finalPrice)*100/$_regularPrice);
		} else {
			return 0;
		}
    }
    
    /**
     * Check if tje product is new
     * @param string $date
     * @return boolean
     */
    public function isNew($date=null, $_product=null)
    {
    	if($date==null) {
    		$date = $_product->getResource()->formatDate(time());
    	}
    
    	if(!$_product->getData('news_from_date')) {
    		return false;
    	}
    
    	$current_date = new DateTime($date); // compare date
    	$from_date = new DateTime($_product->getData('news_from_date')); // begin date
    	$to_date = new DateTime($_product->getData('news_to_date')); // end date
    
    	$return = ($current_date >= $from_date && $current_date <= $to_date);
    
    	return $return;
    }
    
}
