<?php
class Addonline_GiftProduct_Model_Sales_Quote_Address_Total_Grand extends Mage_Sales_Model_Quote_Address_Total_Grand {
	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		$grandTotal     = $address->getGrandTotal();
		$baseGrandTotal = $address->getBaseGrandTotal();
	
        $store      = $address->getQuote()->getStore();
		
        $totals_array     = $address->getAllTotalAmounts();
		$totals_array["subtotal"] = $address->getSubtotal();
		$totals = array_sum($totals_array);
		$totals     = $store->roundPrice($totals);
		
		$baseTotals_array = $address->getAllBaseTotalAmounts();
		$baseTotals_array["subtotal"] = $address->getBaseSubtotal();
		$baseTotals = array_sum($baseTotals_array);
		$baseTotals = $store->roundPrice($baseTotals);
		
		$address->setGrandTotal($grandTotal+$totals);
		$address->setBaseGrandTotal($baseGrandTotal+$baseTotals);
		return $this;
	}
}