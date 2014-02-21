<?php
class Addonline_GiftProduct_Model_Tax_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax {
	
	/**
	 * Calculate unit tax anount based on unit price
	 *
	 * @param   Mage_Sales_Model_Quote_Item_Abstract $item
	 * @param   float $rate
	 * @return  Mage_Tax_Model_Sales_Total_Quote
	 */
	protected function _calcUnitTaxAmount(Mage_Sales_Model_Quote_Item_Abstract $item, $rate)
	{
		$qty        = $item->getTotalQty();
		$inclTax    = $item->getIsPriceInclTax();
		$price      = $item->getTaxableAmount();
		$basePrice  = $item->getBaseTaxableAmount();
		$rateKey    = (string)$rate;
		//ADDONLINE
		if($item->getAdditionalData() == "produit_cadeau") {
			$item->setTaxPercent(0);
		} else {
		//FIN ADDONLINE
		$item->setTaxPercent($rate);
		//ADDONLINE
		}
		//FIN ADDONLINE
		
		$isWeeeEnabled = $this->_weeeHelper->isEnabled();
		$isWeeeTaxable = $this->_weeeHelper->isTaxable();
	
		$hiddenTax      = null;
		$baseHiddenTax  = null;
		switch ($this->_config->getCalculationSequence($this->_store)) {
			case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
			case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
				$unitTax        = $this->_calculator->calcTaxAmount($price, $rate, $inclTax, false);
				$baseUnitTax    = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax, false);
	
				if ($isWeeeEnabled && $isWeeeTaxable) {
					$unitTax += $item->getWeeeTaxAppliedAmount() * $rate /100;
					$baseUnitTax += $item->getBaseWeeeTaxAppliedAmount() * $rate /100;
				}
				$unitTax = $this->_calculator->round($unitTax);
				$baseUnitTax = $this->_calculator->round($baseUnitTax);
				break;
			case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
			case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
				$discountAmount     = $item->getDiscountAmount() / $qty;
				$baseDiscountAmount = $item->getBaseDiscountAmount() / $qty;
	
				if ($isWeeeEnabled) {
					$discountAmount = $discountAmount - $item->getWeeeDiscount() / $qty;
					$baseDiscountAmount = $baseDiscountAmount - $item->getBaseWeeeDiscount() / $qty;
				}
	
				$unitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($price, $rate, $inclTax, false);
				$unitTaxDiscount = $this->_calculator->calcTaxAmount($discountAmount, $rate, $inclTax, false);
				$unitTax = $this->_calculator->round(max($unitTaxBeforeDiscount - $unitTaxDiscount, 0));
	
				$baseUnitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax, false);
				$baseUnitTaxDiscount = $this->_calculator->calcTaxAmount($baseDiscountAmount, $rate, $inclTax, false);
				$baseUnitTax = $this->_calculator->round(max($baseUnitTaxBeforeDiscount - $baseUnitTaxDiscount, 0));
	
				if ($isWeeeEnabled && $this->_weeeHelper->isTaxable()) {
					$weeeTax = ($item->getWeeeTaxAppliedRowAmount() - $item->getWeeeDiscount()) * $rate / 100;
					$weeeTax = $weeeTax / $qty;
					$unitTax += $weeeTax;
					$baseWeeeTax =
					($item->getBaseWeeeTaxAppliedRowAmount() - $item->getBaseWeeeDiscount()) * $rate / 100;
					$baseWeeeTax = $baseWeeeTax / $qty;
					$baseUnitTax += $baseWeeeTax;
				}
				$unitTax = $this->_calculator->round($unitTax);
				$baseUnitTax = $this->_calculator->round($baseUnitTax);
				if ($inclTax && $discountAmount > 0) {
					$hiddenTax      = $this->_calculator->round($unitTaxBeforeDiscount) - $unitTax;
					$baseHiddenTax  = $this->_calculator->round($baseUnitTaxBeforeDiscount) - $baseUnitTax;
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
				// calculate discount compensation
				if (!$item->getNoDiscount() && $item->getWeeeTaxApplied()) {
					$unitTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
							$price,
							$rate,
							$inclTax,
							false
					);
					$baseUnitTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
							$price,
							$rate,
							$inclTax,
							false
					);
					if ($isWeeeTaxable) {
						$unitTaxBeforeDiscount += $item->getWeeeTaxAppliedAmount() * $rate / 100;
						$baseUnitTaxBeforeDiscount += $item->getBaseWeeeTaxAppliedAmount() * $rate / 100;
					}
					$unitTaxBeforeDiscount = max(0, $this->_calculator->round($unitTaxBeforeDiscount));
					$baseUnitTaxBeforeDiscount = max(0, $this->_calculator->round($baseUnitTaxBeforeDiscount));
					$item->setDiscountTaxCompensation($unitTaxBeforeDiscount * $qty - max(0, $unitTax) * $qty);
					$item->setBaseDiscountTaxCompensation(
							$baseUnitTaxBeforeDiscount * $qty - max(0, $baseUnitTax) * $qty
					);
				}
				break;
		}
	
		//ADDONLINE
		if($item->getAdditionalData() == "produit_cadeau") {
			$item->setTaxAmount(0);
			$item->setBaseTaxAmount(0);
			$item->setRowTotalInclTax(0);
			$item->setBaseRowTotalInclTax(0);
		} else {
		//FIN ADDONLINE
		$item->setTaxAmount($this->_store->roundPrice(max(0, $qty*$unitTax)));
		$item->setBaseTaxAmount($this->_store->roundPrice(max(0, $qty*$baseUnitTax)));
		
		$rowTotalInclTax = $item->getRowTotalInclTax();
		if (!isset($rowTotalInclTax)) {
			if ($this->_config->priceIncludesTax($this->_store)) {
				$weeeTaxBeforeDiscount = 0;
				$baseWeeeTaxBeforeDiscount = 0;
				if ($isWeeeTaxable) {
					$weeeTaxBeforeDiscount = $item->getWeeeTaxAppliedRowAmount() * $rate/100;
					$baseWeeeTaxBeforeDiscount = $item->getBaseWeeeTaxAppliedRowAmount() * $rate/100;
				}
				$item->setRowTotalInclTax($price * $qty + $weeeTaxBeforeDiscount);
				$item->setBaseRowTotalInclTax($basePrice * $qty + $baseWeeeTaxBeforeDiscount);
			} else {
				$taxCompensation = $item->getDiscountTaxCompensation() ? $item->getDiscountTaxCompensation() : 0;
				$item->setRowTotalInclTax($price * $qty + $unitTax * $qty + $taxCompensation);
				$item->setBaseRowTotalInclTax(
						$basePrice * $qty + $baseUnitTax * $qty + $item->getBaseDiscountTaxCompensation()
				);
			}
		}
		//ADDONLINE
		}
		//FIN ADDONLINE
		
		return $this;
	}
}