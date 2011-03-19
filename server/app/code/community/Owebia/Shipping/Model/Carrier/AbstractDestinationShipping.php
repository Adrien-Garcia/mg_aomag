<?php

/**
 * Magento Owebia Shipping Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Owebia
 * @package    Owebia_Shipping
 * @copyright  Copyright (c) 2008 Owebia (http://www.owebia.com)
 * @author     Antoine Lemoine
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class Owebia_Shipping_Model_Carrier_AbstractDestinationShipping
	extends Mage_Shipping_Model_Carrier_Abstract
{
	protected static $FLOAT_REGEX = '[-]?\d+(?:[.]\d+)?';
	protected static $POSITIVE_FLOAT_REGEX = '\d+(?:[.]\d+)?';
	protected static $ACCENTED_CHARS_ARRAY      = array('À','Á','Â','Ã','Ä','Å','à','á','â','ã','ä','å','Ò','Ó','Ô','Õ','Ö','Ø','ò','ó','ô','õ','ö','ø','È','É','Ê','Ë','è','é','ê','ë','Ç','ç','Ì','Í','Î','Ï','ì','í','î','ï','Ù','Ú','Û','Ü','ù','ú','û','ü','ÿ','Ñ','ñ');
	protected static $NONE_ACCENTED_CHARS_ARRAY = array('A','A','A','A','A','A','a','a','a','a','a','a','O','O','O','O','O','O','o','o','o','o','o','o','E','E','E','E','e','e','e','e','C','c','I','I','I','I','i','i','i','i','U','U','U','U','u','u','u','u','y','N','n');
	protected static $COUPLE_REGEX = '(?:[0-9.]+|\*)\:[0-9.]+(?:\:[0-9.]+%?)*';

	protected $_result;

	/**
	 * Collect rates for this shipping method based on information in $request
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		// skip if not enabled
		if (!$this->getConfigData('active')) {
			return false;
		}
 		if ($request->_data['package_weight']==0 && !$this->getConfigData('active_when_weight_null')) {
			return false;
		}

		// get necessary configuration values
		$handling_fee = str_replace(array('&gt;','&lt;','“','”','&laquo;','&raquo;'),array('>','<','"','"','"','"'),$this->getConfigData('handling_fees_table'));
		
		$countries_translation_list = Mage::getModel('core/locale')->getLocale()->getCountryTranslationList();
		$code = $request->_data['dest_country_id'];
		
		$process = array(
			'destination_found' => false,
			'prices_range_match' => false,
			'value_found' => false,
			'result' => Mage::getModel('shipping/rate_result'),
			'request' => $request,
			'cart_amount' => 0,
			'cart_vat_amount' => 0,
			'reference_cart_amount' => 0,
			'country_name' => isset($countries_translation_list[$code]) ? $countries_translation_list[$code] : $code,
			'stop_to_first_match' => $this->getConfigData('stop_to_first_match'),
		);

		$process['cart_amount'] = $request->_data['package_value_with_discount'];
		if ($this->getConfigData('use_amount_with_tax'))
		{
			$items_in_cart = $request->getData('all_items');
			if (count($items_in_cart)>0)
			{
				foreach ($items_in_cart as $key => $item_in_cart)
				{
					$calc = Mage::getSingleton('tax/calculation');
					$rates = $calc->getRatesForAllProductTaxClasses($calc->getRateRequest());
					$vat_rate = isset($rates[$item_in_cart->getProduct()->getTaxClassId()]) ? $rates[$item_in_cart->getProduct()->getTaxClassId()] : 0;

					if ($vat_rate > 0){
						$vat_to_add = $item_in_cart->getData('row_total_with_discount')*$vat_rate/100;
					} else {
						$vat_to_add = $item_in_cart->getData('tax_amount');
					}
					$process['cart_vat_amount'] += $vat_to_add;
				}
			}
		}
		else
		{
			$process['cart_vat_amount'] = 0;
		}
		$process['reference_cart_amount'] = ($this->getConfigData('use_amount_with_tax') ? $process['cart_vat_amount'] : 0)+$process['cart_amount'];

		$config = $this->jsonDecode($handling_fee);

		if (count($config)>0)
		{
			foreach ($config as $row)
			{
				if ($this->processRow($row,$process) && $process['stop_to_first_match']) { break; }
			}
		}
		else
		{
			$handling_fee = preg_replace("/(# *\[|\[|#)/","\n$1",str_replace(array("\r","\n"),array('',''),$handling_fee));
			$rows = explode("\n",$handling_fee);

			$destination_regex = '[A-Z]{2}(?: *\(-? *[A-Z0-9]+(?:, *[A-Z0-9]+)*\))?';
			$full_regex = '#^\[([^;]+); *('.$destination_regex.'(?:, *'.$destination_regex.')*);(?: *((?:[0-9.]+|\*)=>(?:[0-9.]+|\*));)?(?: *([0-9.]+);)? *('
				.self::$COUPLE_REGEX.'(?:, *'.self::$COUPLE_REGEX.')*)\]$#';

			foreach ($rows as $key => $row_string)
			{
				$row_string = trim($row_string);
				if (preg_match($full_regex,$row_string,$row_data))
				{
					$row = array(
						'label' => $row_data[1],
						'destination' => $row_data[2],
						'prices_range' => $row_data[3],
						'fixed_fees' => (float)$row_data[4],
						'fees_table' => $row_data[5],
					);
					
					if ($this->processRow($row,$process) && $process['stop_to_first_match']) { break; }
				}
				else if (preg_match('/^#/',$row_string) || $row_string=='')
				{
					// Do nothing
				}
				else
				{
					$this->appendError($process['result'],$this->__('Error in the configuration of the shipping method').$row_string);
				}
			}
		}

		if (!$process['destination_found'])
		{
			$this->appendError($process['result'],$this->__('The shipping method doesn\'t cover the zone').' ('.$process['country_name'].')');
		}
		else if (!$process['prices_range_match'])
		{
			$this->appendError($process['result'],$this->__('The shipping method isn\'t available with this cart amount'));
		}
		else if (!$process['value_found'])
		{
			$this->appendError($process['result'],$this->getNotFoundError($process['request']));
		}

		return $process['result'];
	}

	protected function processRow($row, &$process)
	{
		$prices_range = explode('=>',isset($row['prices_range']) && preg_match('/('.self::$POSITIVE_FLOAT_REGEX.'|\*)[[:space:]]*=>[[:space:]]*('.self::$POSITIVE_FLOAT_REGEX.'|\*)/',$row['prices_range']) ? $row['prices_range'] : '*=>*');
		$prices_range[0] = trim($prices_range[0]);
		$prices_range[1] = trim($prices_range[1]);
		$min_amount = $prices_range[0]=='*' ? -1 : (float)$prices_range[0];
		$max_amount = $prices_range[1]=='*' ? -1 : (float)$prices_range[1];
		
		if (!isset($row['destination']))
		{
			$row['destination'] = '*';
		}
		if (!isset($row['label']))
		{
			$row['label'] = '***';
		}

		$destination_match = $this->destinationMatch($process['request'],$row['destination']);
		$prices_range_match = ($min_amount==-1 || $min_amount<=$process['reference_cart_amount']) && ($max_amount==-1 || $max_amount>=$process['reference_cart_amount']);
		$process['prices_range_match'] = $process['prices_range_match'] || $prices_range_match;
		$process['destination_found'] = $process['destination_found'] || $destination_match;
		$free_shipping = $process['request']->getFreeShipping();
		
		if ($prices_range_match && $destination_match)
		{
			$fixed_fees_key = $free_shipping ? 'free_shipping__fixed_fees' : 'fixed_fees';
			$fixed_fees = isset($row[$fixed_fees_key]) && is_numeric($row[$fixed_fees_key]) ? $row[$fixed_fees_key] : 0;
	
			if (!$free_shipping && isset($row['fees_table']) || $free_shipping && isset($row['free_shipping__fees_table']))
			{
				$fees_table_string = trim($row[$free_shipping ? 'free_shipping__fees_table' : 'fees_table']);
				if (!preg_match('#^'.self::$COUPLE_REGEX.'(?:, *'.self::$COUPLE_REGEX.')*$#',$fees_table_string))
				{
					$fees_table_string = '*:0.00';
				}
				$fees_table = explode(',',$fees_table_string);
				
				foreach ($fees_table as $item)
				{
					$fee_data = explode(':',$item);
					$empty_package_weight = isset($fee_data[2]) ?
						($fee_data[2]{strlen($fee_data[2])-1}=='%' ?
							substr($fee_data[2],0,strlen($fee_data[2])-1)*$process['request']->_data['package_weight']/100.
							: $fee_data[2])
						: 0;
					$params = array(
						'name' => $row['label'],
						'country_name' => $process['country_name'],
						'max_value' => trim($fee_data[0]),
						'fee' => trim($fee_data[1]),
						'empty_package_weight' => $empty_package_weight,
						'cart_amount' => $process['cart_amount'],
						'cart_vat_amount' => $process['cart_vat_amount'],
						'reference_cart_amount' => $process['reference_cart_amount'],
					);
					
					if ($this->test($process['request'],$params))
					{
						$fees = $this->calculateFee($process['request'],$params)+$fixed_fees;
						$this->appendMethod($process,$params,$fees);
						return true;
					}
				}
			}
			else if (!$free_shipping && isset($row['fees_formula']) || $free_shipping && isset($row['free_shipping__fees_formula']))
			{
				$weight = $process['request']->_data['package_weight'];
				$price = $process['cart_amount'];
				$vat = $process['cart_vat_amount'];
				$qty = $process['request']->_data['package_qty'];
				$formula = str_replace(
					array('weight','poids','price','prix','vat','tva','quantity','quantite'),
					array($weight,$weight,$price,$price,$vat,$vat,$qty,$qty),
					$row[$free_shipping ? 'free_shipping__fees_formula' : 'fees_formula']
				);
				if (preg_match('/^(floor|ceil|round|max|min|rand|pow|pi|sqrt|log|exp|abs|int|float|[0-9\.\-\(\)\*\/\?\:\+\<\>\=\&\|% ])*$/',$formula))
				{
					$params = array(
						'name' => $row['label'],
						'country_name' => $process['country_name'],
						'cart_amount' => $process['cart_amount'],
						'cart_vat_amount' => $process['cart_vat_amount'],
					);
					eval('$fees = '.$formula.';');
					$this->appendMethod($process,$params,$fees+$fixed_fees);
					return true;
				}
				else
				{
					$this->appendError($process['result'],$this->__('Error in the configuration of the shipping method').' '.htmlentities($row['fees_formula']));
					return false;
				}
			}
			else if (!$free_shipping && isset($row['fixed_fees']) || $free_shipping && isset($row['free_shipping__fixed_fees']))
			{
				$params = array(
					'name' => $row['label'],
					'country_name' => $process['country_name'],
					'cart_amount' => $process['cart_amount'],
					'cart_vat_amount' => $process['cart_vat_amount'],
				);
				$this->appendMethod($process,$params,$fixed_fees);
			}
		}
		return false;
	}

	protected function appendMethod(&$process, $params, $fees)
	{
		$process['value_found'] = true;

		$method = Mage::getModel('shipping/rate_result_method');

		$method->setCarrier($this->_code);
		$method->setCarrierTitle($this->getConfigData('title'));

		// strtr ne marche pas avec des chaînes UTF-8 => on utilise str_replace
		$method_name = str_replace(self::$ACCENTED_CHARS_ARRAY,self::$NONE_ACCENTED_CHARS_ARRAY,$params['name']);
		//$method_name = $params['name'];
		$method->setMethod(preg_replace('#[^a-z0-9]+#','_',strtolower($method_name)));
		$method->setMethodTitle($this->getMethodTitle($process['request'],$params));
		$method->setPrice($fees);
		$method->setCost($fees);

		$process['result']->append($method);
	}

	protected function jsonDecode($config_string)
	{
		$keys_replacement = array(
			'nom' => 'label',
			'tranche_de_prix' => 'prices_range',
			'frais_fixes' => 'fixed_fees',
			'table_de_frais' => 'fees_table',
		);

		$row_regex = '([a-z0-9_]+|"[a-z0-9_]+")[[:space:]]*:[[:space:]]*("(?:(?:[^"}]|\\\\")*[^\\\\])?"|'.self::$FLOAT_REGEX.')';
		preg_match_all('/(^|[^#]){[[:space:]]*('.$row_regex.'(?:[[:space:]]*,[[:space:]]*'.$row_regex.')*,?)[[:space:]]*}/i',$config_string,$result,PREG_SET_ORDER);

		$config = array();
		foreach ($result as $row)
		{
			preg_match_all('/'.$row_regex.'/i',$row[0],$result2,PREG_SET_ORDER);

			$config_i = array();
			foreach ($result2 as $row)
			{
				$key = preg_replace('/^"(.*)"$/','$1',$row[1]);
				$config_i[isset($keys_replacement[$key]) ? $keys_replacement[$key] : $key] = str_replace('\"','"',preg_replace('/^"(.*)"$/','$1',$row[2]));
			}
			$config[] = $config_i;
		}
		
		return $config;
	}

	public function destinationMatch($request, $destination_string)
	{
		$tmp_destination_array = explode(',',str_replace(' ','',$destination_string));
		
		$concat = false;
		$concatened = '';
		$destination_array = array();
		$i = 0;
		foreach ($tmp_destination_array as $destination)
		{
			if ($concat)
			{
				$concatened .= ','.$destination;
				if (preg_match('#\)#',$destination))
				{
					$destination_array[] = $concatened;
					$concatened = '';
					$concat = false;
				}
			}
			else
			{
				if ($i<count($tmp_destination_array)-1 && preg_match('#\(#',$destination))
				{
					$concat = true;
					$concatened .= $destination;
				}
				else
				{
					$destination_array[] = $destination;
				}
			}
			$i++;
		}
		
		foreach ($destination_array as $destination)
		{
			if (preg_match('#([A-Z]{2})(?: *\((-)? *(.*)\))?#',$destination,$result))
			{
				$country_code = $result[1];
				if ($request->_data['dest_country_id']==$country_code)
				{
					if (!isset($result[3]) || $result[3]=='') { return true; }
					else
					{
						$region_codes = explode(',',$result[3]);
						// Vérification stricte
						$in_array = in_array($request->_data['dest_region_code'],$region_codes,true);
						if ($result[2]=='-' && !$in_array || $result[2]=='' && $in_array) { return true; }
					}
				}
			}
		}
		return false;
	}

	public function test($request, $params)
	{
		return false;
	}

	public function calculateFee($request, $params)
	{
		return $params['fee'];
	}

	public function getMethodTitle($request, $params)
	{
		return $params['name'].' ('.$params['country_name'].')';
	}

	public function getNotFoundError($request)
	{
		return $this->__('The parcel is too heavy for this shipping method')
			.' ('.$request->_data['package_weight'].' '.Mage::getStoreConfig('owebia/shipping/weight_unit').' )';
	}

	public function __($message)
	{
		return Mage::helper('shipping')->__($message);
	}

	public function appendError($result, $message)
	{
		if ($this->getConfigData('display_when_unavailable') || Mage::getStoreConfig('owebia/shipping/display_when_unavailable'))
		{
			$error = Mage::getModel('shipping/rate_result_error');
			$error->setCarrier($this->_code);
			$error->setCarrierTitle($this->getConfigData('title'));
			$error->setErrorMessage($message);
			$result->append($error);
		}
	}

	public function isTrackingAvailable()
	{
		return true;
	}

	public function getTrackingInfo($tracking_number)
	{
		$tracking_result = $this->getTracking($tracking_number);

		if ($tracking_result instanceof Mage_Shipping_Model_Tracking_Result)
		{
			if ($trackings = $tracking_result->getAllTrackings())
			{
				return $trackings[0];
			}
		}
		elseif (is_string($tracking_result) && !empty($tracking_result))
		{
			return $tracking_result;
		}
		
		return false;
	}

	public function getTracking($tracking_number)
	{
		$tracking_result = Mage::getModel('shipping/tracking_result');

		$tracking_status = Mage::getModel('shipping/tracking_result_status');
		$tracking_status->setCarrier($this->_code);
		$tracking_status->setCarrierTitle($this->getConfigData('title'));
		$tracking_status->setTracking($tracking_number);
		$tracking_status->addData(
			array(
				'status'=>'<a target="_blank" href="'.str_replace('$1',$tracking_number,$this->getConfigData('tracking_view_url')).'">suivre le colis</a>'
			)
		);
		$tracking_result->append($tracking_status);

		return $tracking_result;
	}

}

?>