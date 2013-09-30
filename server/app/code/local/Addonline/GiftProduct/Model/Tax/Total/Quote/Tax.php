<?php
class Addonline_GiftProduct_Model_Tax_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax {
	protected function _calcUnitTaxAmount(Mage_Sales_Model_Quote_Item_Abstract $item, $rate)
	{
		$extra      = $item->getExtraTaxableAmount();
		$baseExtra  = $item->getBaseExtraTaxableAmount();
		$qty        = $item->getTotalQty();
		$inclTax    = $item->getIsPriceInclTax();
		$price      = $item->getTaxableAmount();
		$basePrice  = $item->getBaseTaxableAmount();
		$rateKey    = (string)$rate;
		
		if($item->getAdditionalData() == "produit_cadeau") {
			$item->setTaxPercent(0);
		} else {
			$item->setTaxPercent($rate);
		}
		$hiddenTax      = null;
		$baseHiddenTax  = null;
		switch ($this->_config->getCalculationSequence($this->_store)) {
			case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
			case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
				$unitTax        = $this->_calculator->calcTaxAmount($price, $rate, $inclTax);
				$baseUnitTax    = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax);
				break;
			case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
			case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
				$discountAmount     = $item->getDiscountAmount() / $qty;
				$baseDiscountAmount = $item->getBaseDiscountAmount() / $qty;
				$unitTax            = $this->_calculator->calcTaxAmount(max($price-$discountAmount, 0), $rate, $inclTax);
				$baseUnitTax        = $this->_calculator->calcTaxAmount(max($basePrice-$baseDiscountAmount, 0), $rate, $inclTax);
				if ($inclTax && $discountAmount > 0) {
					$hiddenTax      = $this->_calculator->calcTaxAmount($discountAmount, $rate, $inclTax, false);
					$baseHiddenTax  = $this->_calculator->calcTaxAmount($baseDiscountAmount, $rate, $inclTax, false);
					$this->_hiddenTaxes[] = array(
							'rate_key'   => $rateKey,
							'qty'        => $qty,
							'item'       => $item,
							'value'      => $hiddenTax,
							'base_value' => $baseHiddenTax,
							'incl_tax'   => $inclTax,
					);
				} elseif ($discountAmount > $price) { // case with 100% discount on price incl. tax
					$hiddenTax      = $discountAmount - $price;
					$baseHiddenTax  = $baseDiscountAmount - $basePrice;
					$this->_hiddenTaxes[] = array(
							'rate_key'   => $rateKey,
							'qty'        => $qty,
							'item'       => $item,
							'value'      => $hiddenTax,
							'base_value' => $baseHiddenTax,
							'incl_tax'   => $inclTax,
					);
				}
				break;
		}
		if($item->getAdditionalData() == "produit_cadeau") {
			$item->setTaxAmount(0);
			$item->setBaseTaxAmount(0);
		} else {
			$item->setTaxAmount($this->_store->roundPrice(max(0, $qty*$unitTax)));
			$item->setBaseTaxAmount($this->_store->roundPrice(max(0, $qty*$baseUnitTax)));
		}
	
		return $this;
	}
}