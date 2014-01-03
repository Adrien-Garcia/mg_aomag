<?php

/**
 * Copyright (c) 2008-13 Owebia
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @website    http://www.owebia.com/
 * @project    Magento Owebia Shipping 2 module
 * @author     Antoine Lemoine
 * @license    http://www.opensource.org/licenses/MIT  The MIT License (MIT)
 **/

class GlsShippingHelper
{
	const FLOAT_REGEX = '[-]?\d+(?:[.]\d+)?';
	const COUPLE_REGEX = '(?:[0-9.]+|\*) *(?:\[|\])? *\: *[0-9.]+';

	public static $DEBUG_INDEX_COUNTER = 0;
	public static $UNCOMPRESSED_STRINGS = array(
		' product.attribute.',
		' item.option.',
		'{product.attribute.',
		'{item.option.',
		'{product.',
		'{cart.',
		'{selection.',
	);
	public static $COMPRESSED_STRINGS = array(
		' p.a.',
		' item.o.',
		'{p.a.',
		'{item.o.',
		'{p.',
		'{c.',
		'{s.',
	);

	protected $_input;
	protected $_config;
	protected $_messages;
	protected $_formula_cache;
	protected $_expression_cache;
	public $debug_code = null;
	public $debug_output = '';
	public $debug_header = null;

	public static function esc($input)
	{
		$input = htmlspecialchars($input, ENT_NOQUOTES, 'UTF-8');
		return preg_replace('/&lt;(\/?)span([^&]*)&gt;/', '<\1span\2>', $input);
	}

	public static function toString($value)
	{
		if (!isset($value)) return 'null';
		else if (is_bool($value)) return $value ? 'true' : 'false';
		else if (is_float($value)) return str_replace(',', '.', (string)$value); // To avoid locale problems
		else if (is_array($value)) return 'array(size:'.count($value).')';
		else if (is_object($value)) return get_class($value).'';
		else return $value;
	}

	public static function parseSize($size)
	{
		$size = trim($size);
		$last = strtolower($size[strlen($size)-1]);
		switch ($last) {
			case 'g': $size *= 1024;
			case 'm': $size *= 1024;
			case 'k': $size *= 1024;
		}
		return (float)$size;
	}

	public static function formatSize($size)
	{
		$unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		return self::toString(@round($size/pow(1024, ($i=floor(log($size, 1024)))), 2)).' '.$unit[$i];
	}

	public static function getInfos()
	{
		$properties = array(
				'server_os' => PHP_OS,
				'server_software' => $_SERVER['SERVER_SOFTWARE'],
				'php_version' => PHP_VERSION,
				'memory_limit' => self::formatSize(self::parseSize(ini_get('memory_limit'))),
				'memory_usage' => self::formatSize(memory_get_usage(true)),
		);
		return $properties;
	}

	public static function getDefaultProcessData()
	{
		return array(
			'info'				=> new OS2_Data_GLS(self::getInfos()),
			'cart'				=> new OS2_Data_GLS(),
			'quote'				=> new OS2_Data_GLS(),
			'selection'			=> new OS2_Data_GLS(),
			'customer'			=> new OS2_Data_GLS(),
			'customer_group'	=> new OS2_Data_GLS(),
			'customvar'			=> new OS2_Data_GLS(),
			'date'				=> new OS2_Data_GLS(),
			'origin'			=> new OS2_Data_GLS(),
			'shipto'			=> new OS2_Data_GLS(),
			'billto'			=> new OS2_Data_GLS(),
			'store'				=> new OS2_Data_GLS(),
			'request'			=> new OS2_Data_GLS(),
			'address_filter'	=> new OS2_Data_GLS(),
		);
	}

	public static function jsonEncode($data, $beautify = false, $html = false, $level = 0, $current_indent = '')
	{
		//$html = true;
		$indent = "\t";//$html ? '&nbsp;&nbsp;&nbsp;&nbsp;' : "\t";//
		$line_break = $html ? '<br/>' : "\n";
		$new_indent = $current_indent.$indent;
		switch ($type = gettype($data)) {
			case 'NULL':
				return ($html ? '<span class=json-reserved>' : '').'null'.($html ? '</span>' : '');
			case 'boolean':
				return ($html ? '<span class=json-reserved>' : '').($data ? 'true' : 'false').($html ? '</span>' : '');
			case 'integer':
			case 'double':
			case 'float':
				return ($html ? '<span class=json-numeric>' : '').$data.($html ? '</span>' : '');
			case 'string':
				return ($html ? '<span class=json-string>' : '').'"'.str_replace(array("\\", '"', "\n", "\r"), array("\\\\", '\"', "\\n", "\\r"), $html ? htmlspecialchars($data, ENT_COMPAT, 'UTF-8') : $data).'"'.($html ? '</span>' : '');
			case 'object':
				$data = (array)$data;
			case 'array':
				$output_index_count = 0;
				$output = array();
				foreach ($data as $key => $value) {
					if ($output_index_count!==null && $output_index_count++!==$key) {
						$output_index_count = null;
					}
				}
				$is_associative = $output_index_count===null;
				foreach ($data as $key => $value) {
					if ($is_associative) {
						$classes = array();
						if ($key=='about') $classes[] = 'json-about';
						if ($key=='conditions' || $key=='fees') $classes[] = 'json-formula';
						$property_classes = array('json-property');
						if ($level==0) $property_classes[] = 'json-id';
						$output[] = ($html && $classes ? '<span class="'.implode(' ', $classes).'">' : '')
						.($html ? '<span class="'.implode(' ', $property_classes).'">' : '')
						.self::jsonEncode((string)$key)
						.($html ? '</span>' : '').':'
								.($beautify ? ' ' : '')
								.self::jsonEncode($value, $beautify, $html, $level+1, $new_indent)
								.($html && $classes ? '</span>' : '');
					} else {
						$output[] = self::jsonEncode($value, $beautify, $html, $level+1, $current_indent);
					}
				}
				if ($is_associative) {
					$classes = array();
					if (isset($data['type']) && $data['type']=='meta') $classes[] = 'json-meta';
					$output = ($html && $classes ? '<span class="'.implode(' ', $classes).'">' : '')
					.'{'
							.($beautify ? "{$line_break}{$new_indent}" : '')
							.implode(','.($beautify ? "{$line_break}{$new_indent}" : ''), $output)
							.($beautify ? "{$line_break}{$current_indent}" : '')
							.'}'
									.($html && $classes ? '</span>' : '');
					//echo $output;
					return $output;
				} else {
					return '['.implode(','.($beautify ? ' ' : ''), $output).']';
				}
			default:
				return ''; // Not supported
		}
	}

	public function GlsShippingHelper($input,$autocorrection = true) {
		$this->_formula_cache = array();
		$this->_messages = array();
		$this->_input = $input;
		$this->_config = array();
		$this->_parseInput($autocorrection);
	}

	public function debug($text) {
		$this->debug_output .= "<p>".$text."</p>";
	}

	public function getDebug() {
		$index = $this->debug_code.'-'.self::$DEBUG_INDEX_COUNTER++;
		$output = "<style rel=\"stylesheet\" type=\"text/css\">"
		.".osh-debug{background:#000;color:#bbb;-webkit-opacity:0.9;-moz-opacity:0.9;opacity:0.9;text-align:left;white-space:pre-wrap;}"
		.".osh-debug p{margin:2px 0;}"
		.".osh-debug .osh-formula{color:#f90;} .osh-key{color:#0099f7;}"
		.".osh-debug .osh-error{color:#f00;} .osh-warning{color:#ff0;} .osh-info{color:#7bf700;}"
		.".osh-debug .osh-debug-content{padding:10px;}"
		.".osh-debug .osh-replacement{color:#ff3000;}"
		."</style>"
		."<div id=\"osh-debug-".$index."\" class=\"osh-debug\"><pre class=\"osh-debug-content\"><span style=\"float:right;cursor:pointer;\" onclick=\"document.getElementById('osh-debug-".$index."').style.display = 'none';\">[<span style=\"padding:0 5px;color:#f00;\">X</span>]</span>"
		."<p>".$this->debug_header."</p>".$this->debug_output."</pre></div>";
		return $output;
	}

	public function initDebug($code, $process)
	{
		$header = 'DEBUG OwebiaShippingHelper_GLS.php<br/>';
		foreach ($process as $index => $process_option) {
			if (in_array($index, array('data', 'options'))) {
				$header .= '   <span class=osh-key>'.self::esc(str_replace('.', '</span>.<span class=osh-key>', $index)).'</span> &gt;&gt;<br/>';
				foreach ($process_option as $object_name => $data) {
					if (is_object($data) || is_array($data)) {
						$header .= '      <span class=osh-key>'.self::esc(str_replace('.', '</span>.<span class=osh-key>', $object_name)).'</span> &gt;&gt;<br/>';
						$children = array();
						if (is_object($data)) $children = $data->__sleep();
						else if (is_array($data)) $children = array_keys($data);
						foreach ($children as $name) {
							$key = $name;
							if ($key=='*') {
								$header .= '         .<span class=osh-key>'.self::esc(str_replace('.', '</span>.<span class=osh-key>', $key)).'</span> = …<br/>';
							} else {
								if (is_object($data)) $value = $data->{$name};
								else if (is_array($data)) $children = $data[$name];
								$header .= '         .<span class=osh-key>'.self::esc(str_replace('.', '</span>.<span class=osh-key>', $key)).'</span> = <span class=osh-formula>'.self::esc(self::toString($value)).'</span> ('.gettype($value).')<br/>';
							}
						}
					} else {
						$header .= '      .<span class=osh-key>'.self::esc(str_replace('.', '</span>.<span class=osh-key>', $object_name)).'</span> = <span class=osh-formula>'.self::esc(self::toString($data)).'</span> ('.gettype($data).')<br/>';
					}
				}
			} else {
				$header .= '   <span class=osh-key>'.self::esc(str_replace('.', '</span>.<span class=osh-key>', $index)).'</span> = <span class=osh-formula>'.self::esc(self::toString($process_option)).'</span> ('.gettype($process_option).')<br/>';
			}
		}
		$this->debug_code = $code;
		$this->debug_header = $header;
	}

	public function getConfig() {
		return $this->_config;
	}

	public function getMessages() {
		$messages = $this->_messages;
		$this->_messages = array();
		return $messages;
	}

	public function formatConfig($compress,$keys_to_remove=array()) {
		$output = '';
		foreach ($this->_config as $code => $row) {
			if (!isset($row['lines'])) {
				if (isset($row['*comment']['value'])) {
					$output .= trim($row['*comment']['value'])."\n";
				}
				$output .= '{'.($compress ? '' : "\n");
				foreach ($row as $key => $property) {
					if (substr($key,0,1)!='*' && !in_array($key,$keys_to_remove)) {
						$value = $property['value'];
						if (isset($property['comment'])) $output .= ($compress ? '' : "\t").'/* '.$property['comment'].' */'.($compress ? '' : "\n");
						$output .= ($compress ? '' : "\t").$key.':'.($compress ? '' : ' ');
						if (is_bool($value)) $output .= $value ? 'true' : 'false';
						else if ((string)((int)$value)==$value) $output .= $value;
						else if ((string)((float)$value)==$value) $output .= ($compress ? (float)$value : $value);
						else $output .= '"'.str_replace('"','\\"',$value).'"';
						$output .= ','.($compress ? '' : "\n");
					}
				}
				if ($compress) $output = preg_replace('/,$/','',$output);
				$output .= "}\n".($compress ? '' : "\n");
			} else {
				$output .= $row['lines']."\n";
			}
		}
		return $compress ? $this->compress($output) : $this->uncompress($output);
	}

	public function checkConfig()
	{
		$timestamp = time();
		$process = array(
			'config' => $this->_config,
			'data' => self::getDefaultProcessData(),
			'result' => null,
		);
		foreach ($this->_config as $code => &$row) {
			$this->processRow($process, $row, $check_all_conditions=true);
			foreach ($row as $property_name => $property_value) {
				if (substr($property_name, 0, 1)!='*') {
					$this->debug('   check '.$property_name);
					$this->getRowProperty($row, $property_name);
				}
			}
		}
	}

	public function processRow($process, &$row, $is_checking=false) {
		if (!isset($row['*id'])) return;

		self::debug($process);
		self::debug('process row <span class="osh-key">'.$row['*id'].'</span>');
		if (!isset($row['label']['value'])) $row['label']['value'] = '***';

		$enabled = $this->getRowProperty($row,'enabled');
		if (isset($enabled)) {
			if (!$is_checking && !$enabled) {
				$this->addMessage('info',$row,'enabled','Configuration disabled');
				return new GLS_Result(false);
			}
		}

		//ADDONLINE : on exclu la livraison en relay si un article dépasse le point max XL
		$code = $row['*id'];
		if(strpos($code, 'relay_') === 0 ) {
			$okrelay = true;
			foreach ($process['cart.products'] as $product) {
				// Si un produit fait plus que le poids maximum des relayXL
				if($product->getAttribute('weight') > Mage::getStoreConfig('carriers/gls/maxxlrelayweight')){
					return new GLS_Result(false);
				}
			}
		}

		$conditions = $this->getRowProperty($row,'conditions');
		if (isset($conditions)) {
			$result = $this->_processFormula($process,$row,'conditions',$conditions,$is_checking);
			if (!$is_checking) {
				if (!$result->success) return $result;
				if (!$result->result) {
					$this->addMessage('info',$row,'conditions',"The cart doesn't match conditions");
					return new GLS_Result(false);
				}
			}
		}

		$destination = $this->getRowProperty($row,'destination');
		if (isset($destination)) {
			$destination_match = $this->_addressMatch($destination,array(
				'country_code' => $process['data']['destination.country.code'],
				'region_code' => $process['data']['destination.region.code'],
				'postcode' => $process['data']['destination.postcode']
			));
			if (!$is_checking && !$destination_match) {
				$this->addMessage('info',$row,'destination',"The shipping method doesn't cover the zone");
				return new GLS_Result(false);
			}
		}

		$origin = $this->getRowProperty($row,'origin');
		if (isset($origin)) {
			$origin_match = $this->_addressMatch($origin,array(
				'country_code' => $process['data']['origin.country.code'],
				'region_code' => $process['data']['origin.region.code'],
				'postcode' => $process['data']['origin.postcode']
			));
			if (!$is_checking && !$origin_match) {
				$this->addMessage('info',$row,'origin',"The shipping method doesn't match to shipping origin");
				return new GLS_Result(false);
			}
		}

		$customer_groups = $this->getRowProperty($row,'customer_groups');
		if (isset($customer_groups)) {
			$groups = explode(',',$customer_groups);
			$group_match = false;
			//self::debug('code:'.$process['data']['customer.group.code'].', id:'.$process['data']['customer.group.id']);
			foreach ($groups as $group) {
				$group = trim($group);
				if ($group=='*' || $group==$process['data']['customer.group.code'] || ctype_digit($group) && $group==$process['data']['customer.group.id']) {
					self::debug('      group <span class="osh-replacement">'.$process['data']['customer.group.code'].'</span>'
						.' (id:<span class="osh-replacement">'.$process['data']['customer.group.id'].'</span>) matches');
					$group_match = true;
					break;
				}
			}
			if (!$is_checking && !$group_match) {
				$this->addMessage('info',$row,'customer_groups',"The shipping method doesn't match to customer group (%s)",$process['data']['customer.group.code']);
				return new GLS_Result(false);
			}
		}

		$fees_code = 'fees';
		$fees = $this->getRowProperty($row,$fees_code);
		if (isset($fees)) {
			$result = $this->_processFormula($process,$row,$fees_code,$fees,$is_checking);
			if (!$result->success) return $result;
			self::debug('   => <span class="osh-info">result = <span class="osh-formula">'.$this->_toString($result->result).'</span>');
			return new GLS_Result(true,(float)$result->result);
		}

		return new GLS_Result(false);
	}

	public function getRowProperty(&$row, $key, $original_row=null, $original_key=null) {
		$property = null;
		$output = null;
		if (isset($original_row) && isset($original_key) && $original_row['*id']==$row['*id'] && $original_key==$key) {
			$this->addMessage('error',$row,$key,'Infinite loop %s',"<span class=\"code\">{".$row['*id'].'.'.$key."}</span>");
			return array('error' => 'Infinite loop');
		}
		if (isset($row[$key]['value'])) {
			$property = $row[$key]['value'];
			$output = $property;
			self::debug('   get <span class="osh-key">'.$row['*id'].'</span>.<span class="osh-key">'.$key.'</span> = <span class="osh-formula">'.$this->_toString($property).'</span>');
			preg_match_all('/{([a-z0-9_]+)\.([a-z0-9_]+)}/i',$output,$result_set,PREG_SET_ORDER);
			foreach ($result_set as $result) {
				list($original,$ref_code,$ref_key) = $result;
				if (!in_array($ref_code,array('module','date','store','cart','product','selection','customvar'))) {
					if ($ref_code==$row['code']['value'] && $ref_key==$key) {
						$this->addMessage('error',$row,$key,'Infinite loop %s',"<span class=\"code\">".$original."</span>");
						return null;
					}
					if (isset($this->_config[$ref_code][$ref_key]['value'])) {
						$replacement = $this->getRowProperty($this->_config[$ref_code],$ref_key,
							isset($original_row) ? $original_row : $row,isset($original_key) ? $original_key : $key);
						if (is_array($replacement) && isset($replacement['error'])) {
							return isset($original_row) ? $replacement : 'false';
						}
					} else {
						//$this->addMessage('error',$row,$key,'Non-existent property %s',"<span class=\"code\">".$original."</span>");
						$replacement = $original;//'null';
					}
					$output = $this->replace($original,$replacement,$output);
				}
			}
		} else {
			self::debug('   get <span class="osh-key">'.$row['*id'].'</span>.<span class="osh-key">'.$key.'</span> = <span class="osh-formula">null</span>');
		}
		return $output;
	}

	protected function _toString($value) {
		if (!isset($value)) return 'null';
		else if (is_bool($value)) return $value ? 'true' : 'false';
		else return $value;
	}

	protected function replace($from, $to, $input) {
		if ($from===$to) return $input;
		if (strpos($input,$from)===false) return $input;
		$to = $this->_toString($to);
		self::debug('      replace <span class="osh-replacement">'.$this->_toString($from).'</span> by <span class="osh-replacement">'.$to.'</span> =&gt; <span class="osh-formula">'.str_replace($from,'<span class="osh-replacement">'.$to.'</span>',$input).'</span>');
		return str_replace($from,$to,$input);
	}

	protected function _min() {
		$args = func_get_args();
		$min = null;
		foreach ($args as $arg) {
			if (isset($arg) && (!isset($min) || $min>$arg)) $min = $arg;
		}
		return $min;
	}

	protected function _max() {
		$args = func_get_args();
		$max = null;
		foreach ($args as $arg) {
			if (isset($arg) && (!isset($max) || $max<$arg)) $max = $arg;
		}
		return $max;
	}

	protected function _processFormula($process, &$row, $property_key, $formula_string, $is_checking, $use_cache=true)
	{
		$result = $this->_prepareFormula($process,$row,$property_key,$formula_string,$is_checking,$use_cache);
		if (!$result->success) return $result;

		$eval_result = $this->_evalFormula($result->result);
		if (!isset($eval_result)) {
			$this->addMessage('error',$row,$property_key,'Invalid formula');
			$result = new GLS_Result(false);
			if ($use_cache) $this->setCache($formula_string,$result);
			return $result;
		}
		self::debug('      formula evaluation = <span class="osh-formula">'.$this->_toString($eval_result).'</span>');
		$result = new GLS_Result(true,$eval_result);
		if ($use_cache) $this->setCache($formula_string,$result);
		return $result;
	}

	public function evalInput($process, $row, $property_key, $input) {
		$result = $this->_prepareFormula($process,$row,$property_key,$input,$is_checking=false,$use_cache=true);
		return $result->success ? $result->result : $input;
	}

	protected function setCache($expression, $value) {
		if ($value instanceof GLS_Result) {
			$this->_formula_cache[$expression] = $value;
			self::debug('      cache <span class="osh-replacement">'.$expression.'</span> = <span class="osh-formula">'.$this->_toString($this->_formula_cache[$expression]).'</span>');
		} else {
			$value = $this->_toString($value);
			$this->_expression_cache[$expression] = $value;
			self::debug('      cache <span class="osh-replacement">'.$expression.'</span> = <span class="osh-formula">'.$value.'</span>');
		}
	}

	protected function _prepareFormula($process, $row, $property_key, $formula_string, $is_checking, $use_cache=true)
	{
		if ($use_cache && isset($this->_formula_cache[$formula_string])) {
			$result = $this->_formula_cache[$formula_string];
			self::debug('      get cached formula <span class="osh-replacement">'.$formula_string.'</span> = <span class="osh-formula">'.$this->_toString($result->result).'</span>');
			return $result;
		}

		$formula = $formula_string;
		//self::debug('      formula = <span class="osh-formula">'.$formula.'</span>');

		while (preg_match("#{foreach product\.((?:attribute|option)\.(?:[a-z0-9_]+))}(.*){/foreach}#i",$formula,$result)) {
			$original = $result[0];
			if ($use_cache && isset($this->_expression_cache[$original])) {
				$replacement = $this->_expression_cache[$original];
				self::debug('      get cached expression <span class="osh-replacement">'.$original.'</span> = <span class="osh-formula">'.$replacement.'</span>');
			}
			else {
				$replacement = 0;
				list($filter_property_type,$filter_property_name) = explode('.',$result[1]);
				$selections = array();
				self::debug('      :: foreach <span class="osh-key">'.$filter_property_type.'</span>.<span class="osh-key">'.$filter_property_name.'</span>');
				foreach ($process['cart.products'] as $product) {
					$tmp_value = $this->_getProductProperty($product,$filter_property_type,$filter_property_name,$get_by_id=false);
					self::debug('         products[<span class="osh-formula">'.$product->getName().'</span>].<span class="osh-key">'.$filter_property_type.'</span>.<span class="osh-key">'.$filter_property_name.'</span> = <span class="osh-formula">'.$this->_toString($tmp_value).'</span>');
					$key = 'val_'.$tmp_value;
					$sel = isset($selections[$key]) ? $selections[$key] : null;
					$selections[$key]['products'][] = $product;
					$selections[$key]['weight'] = (isset($sel['weight']) ? $sel['weight'] : 0)+$product->getAttribute('weight')*$product->getQuantity();
					$selections[$key]['quantity'] = (isset($sel['quantity']) ? $sel['quantity'] : 0)+$product->getQuantity();
				}
				self::debug('      :: start foreach');
				foreach ($selections as $selection) {
					$process2 = $process;
					$process2['cart.products'] = $selection['products'];
					$process2['data']['selection.quantity'] = $selection['quantity'];
					$process2['data']['selection.weight'] = $selection['weight'];
					$process_result = $this->_processFormula($process2,$row,$property_key,$result[2],$is_checking,$tmp_use_cache=false);
					$replacement += $process_result->result;
				}
				self::debug('      :: end foreach <span class="osh-key">'.$filter_property_type.'</span>.<span class="osh-key">'.$filter_property_name.'</span>');
				if ($use_cache) $this->setCache($original,$replacement);
			}
			$formula = $this->replace($original,$replacement,$formula);
		}

		$formula = str_replace(array("\n","\t"),array('',''),$formula);

		while (preg_match("#{customvar\.([a-z0-9_]+)}#i",$formula,$result)) {
			$original = $result[0];
			$replacement = Mage::getModel('core/variable')->loadByCode($result[1])->getValue('plain');
			$formula = $this->replace($original,$replacement,$formula);
		}

		$first_product = isset($process['cart.products'][0]) ? $process['cart.products'][0] : null;
		if (!isset($process['data']['selection.weight'])) $process['data']['selection.weight'] = $process['data']['cart.weight'];
		if (!isset($process['data']['selection.quantity'])) $process['data']['selection.quantity'] = $process['data']['cart.quantity'];
		$process['data']['product.weight'] = isset($first_product) ? $first_product->getAttribute('weight') : 0;
		$process['data']['product.quantity'] = isset($first_product) ? $first_product->getQuantity() : 0;

		foreach ($process['data'] as $original => $replacement) {
			$formula = $this->replace('{'.$original.'}',$replacement,$formula);
		}

		if (isset($first_product)) {
			while (preg_match("#{product\.(attribute|option|stock)\.([a-z0-9_+-]+)}#i",$formula,$result)) {
				$original = $result[0];
				switch ($result[1]) {
					case 'attribute': $replacement = $first_product->getAttribute($result[2]); break;
					case 'option': $replacement = $first_product->getOption($result[2]); break;
					case 'stock': $replacement = $first_product->getStockData($result[2]); break;
				}
				$formula = $this->replace($original,$replacement,$formula);
			}
		}

		while (preg_match("/{(count) products(?: where ([^}]+))?}/i",$formula,$result)
			//		|| preg_match("/{(sum|min|max|count distinct) product\.(?(attribute|option|stock)\.([a-z0-9_+-]+)|(quantity))(?: where ([^}]+))?}/i",$formula,$result)) {
					|| preg_match("/{(sum|min|max|count distinct) product\.(attribute|option|stock)\.([a-z0-9_+-]+)(?: where ([^}]+))?}/i",$formula,$result)
					|| preg_match("/{(sum|min|max|count distinct) product\.(quantity)()(?: where ([^}]+))?}/i",$formula,$result)
				) {
			$original = $result[0];
			if ($use_cache && isset($this->_expression_cache[$original])) {
				$replacement = $this->_expression_cache[$original];
				self::debug('      get cached expression <span class="osh-replacement">'.$original.'</span> = <span class="osh-formula">'.$replacement.'</span>');
			} else {
				$replacement = $this->_processProductProperty($process['cart.products'],$result);
				if ($use_cache) $this->setCache($result[0],$replacement);
			}
			$formula = $this->replace($original,$replacement,$formula);
		}

		//while (preg_match("/{table '([^']+)' ([^}]+)}/",$formula,$result))
		while (preg_match("/{table ([^}]+) in ([0-9\.:,\*\[\] ]+)}/i",$formula,$result)) {
			$original = $result[0];
			if ($use_cache && isset($this->_expression_cache[$original])) {
				$replacement = $this->_expression_cache[$original];
				self::debug('      get cached expression <span class="osh-replacement">'.$original.'</span> = <span class="osh-formula">'.$replacement.'</span>');
			} else {
				$reference_value = $this->_evalFormula($result[1]);
				if (isset($reference_value)) {
					$fees_table_string = $result[2];

					if (!preg_match('#^'.self::COUPLE_REGEX.'(?:, *'.self::COUPLE_REGEX.')*$#',$fees_table_string)) {
						$this->addMessage('error',$row,$property_key,'Error in table %s','<span class="osh-formula">'.htmlentities($result[0]).'</span>');
						$result = new GLS_Result(false);
						if ($use_cache) $this->setCache($formula_string,$result);
						return $result;
					}
					$fees_table = explode(',',$fees_table_string);

					$replacement = null;
					foreach ($fees_table as $item) {
						$fee_data = explode(':',$item);

						$fee = trim($fee_data[1]);
						$max_value = trim($fee_data[0]);

						$last_char = $max_value{strlen($max_value)-1};
						if ($last_char=='[') $including_max_value = false;
						else if ($last_char==']') $including_max_value = true;
						else $including_max_value = true;

						$max_value = str_replace(array('[',']'),'',$max_value);

						if ($max_value=='*' || $including_max_value && $reference_value<=$max_value || !$including_max_value && $reference_value<$max_value) {
							$replacement = $fee;//$this->_calculateFee($process,$fee,$var);
							break;
						}
					}
				}
				$replacement = $this->_toString($replacement);
				if ($use_cache) $this->setCache($original,$replacement);
			}
			$formula = $this->replace($original,$replacement,$formula);
		}
		$result = new GLS_Result(true,$formula);
		return $result;
	}

	protected function _evalFormula($formula) {
		if (is_bool($formula)) return $formula;
		if (!preg_match('/^(?:floor|ceil|round|max|min|rand|pow|pi|sqrt|log|exp|abs|int|float|true|false|null|and|or|in|substr|strtolower'
				.'|in_array\(\'(?:[^\']*)\', *array\( *(?:\'(?:[^\']+)\') *(?: *, *\'(?:[^\']+)\')* *\) *\)'
				.'|\'[^\']*\'|[0-9,\'\.\-\(\)\*\/\?\:\+\<\>\=\&\|%! ])*$/',$formula)) {
			$errors = array(
				PREG_NO_ERROR => 'PREG_NO_ERROR',
				PREG_INTERNAL_ERROR => 'PREG_INTERNAL_ERROR',
				PREG_BACKTRACK_LIMIT_ERROR => 'PREG_BACKTRACK_LIMIT_ERROR',
				PREG_RECURSION_LIMIT_ERROR => 'PREG_RECURSION_LIMIT_ERROR',
				PREG_BAD_UTF8_ERROR => 'PREG_BAD_UTF8_ERROR',
				defined('PREG_BAD_UTF8_OFFSET_ERROR') ? PREG_BAD_UTF8_OFFSET_ERROR : 'PREG_BAD_UTF8_OFFSET_ERROR' => 'PREG_BAD_UTF8_OFFSET_ERROR',
			);
			$error = preg_last_error();
			if (isset($errors[$error])) $error = $errors[$error];
			self::debug('      doesn\'t match ('.$error.')');
			return null;
		}
		$formula = str_replace(
			array('min','max'),
			array('$this->_min','$this->_max'),
			$formula
		);
		$eval_result = null;
		@eval('$eval_result = ('.$formula.');');
		return $eval_result;
	}

	protected function _getOptionsAndData($string) {
		if (preg_match('/^(\\s*\(\\s*([^\] ]*)\\s*\)\\s*)/',$string,$result)) {
			$options = $result[2];
			$data = str_replace($result[1],'',$string);
		} else {
			$options = '';
			$data = $string;
		}
		return array(
			'options' => $options,
			'data' => $data,
		);
	}

	public function compress($input) {
		/*if (preg_match_all("/{table (.*) in (".self::COUPLE_REGEX."(?:, *".self::COUPLE_REGEX.")*)}/imsU",$input,$result,PREG_SET_ORDER)) {
			foreach ($result as $result_i) {
				$fees_table = explode(',',$result_i[2]);
				$value = null;
				foreach ($fees_table as $index => $item) {
					list($max_value,$fee) = explode(':',$item);
					$last_char = $max_value{strlen($max_value)-1};
					if (in_array($last_char,array('[',']'))) {
						$including_char = $last_char;
						$max_value = str_replace(array('[',']'),'',$max_value);
					} else $including_char = '';
					$fees_table[$index] = ((float)$max_value).$including_char.':'.((float)$fee);
				}
				$input = str_replace($result_i[2],implode(',',$fees_table),$input);
				$input = str_replace($result_i[1],trim($result_i[1]),$input);
			}
		}
		if (preg_match_all("#{foreach ([^}]*)}(.*){/foreach}#imsU",$input,$result,PREG_SET_ORDER)) {
			foreach ($result as $result_i) {
				$input = str_replace($result_i[1],trim($result_i[1]),$input);
				$input = str_replace($result_i[2],trim($result_i[2]),$input);
			}
		}
		*/
		$input = str_replace(
			self::$UNCOMPRESSED_STRINGS,
			self::$COMPRESSED_STRINGS,
			$input
		);

		if (function_exists('gzcompress') && function_exists('base64_encode')) {
			$input = 'gz64'.base64_encode(gzcompress($input));
		}
		return '$$'.$input;
	}

	public function uncompress($input) {
		if (substr($input,0,4)=='gz64' && function_exists('gzuncompress') && function_exists('base64_decode')) {
			$input = gzuncompress(base64_decode(substr($input,4,strlen($input))));
		}

		/*if (preg_match_all("/{table (.*) in (".self::COUPLE_REGEX."(?:, *".self::COUPLE_REGEX.")*)}/iU",$input,$result,PREG_SET_ORDER)) {
			foreach ($result as $result_i) {
				$fees_table = explode(',',$result_i[2]);
				$value = null;
				foreach ($fees_table as $index => $item) {
					list($max_value,$fee) = explode(':',$item);
					$last_char = $max_value{strlen($max_value)-1};
					if (in_array($last_char,array('[',']'))) {
						$including_char = $last_char;
						$max_value = str_replace(array('[',']'),'',$max_value);
					} else $including_char = '';
					$max_value = (float)$max_value;
					$fee = (float)$fee;
					$new_max_value = number_format($max_value,2,'.','');
					$new_fee = number_format($fee,2,'.','');
					$fees_table[$index] = (((float)$new_max_value)==$max_value ? $new_max_value : $max_value).$including_char.':'
						.(((float)$new_fee)==$fee ? $new_fee : $fee);
				}
				$input = str_replace($result_i[2],implode(', ',$fees_table),$input);
				$input = str_replace($result_i[1],trim($result_i[1]),$input);
			}
		}
		if (preg_match_all("#{foreach ([^}]*)}(.*){/foreach}#iU",$input,$result,PREG_SET_ORDER)) {
			foreach ($result as $result_i) {
				$input = str_replace($result_i[1],trim($result_i[1]),$input);
				$input = str_replace($result_i[2],trim($result_i[2]),$input);
			}
		}*/
		return str_replace(
			self::$COMPRESSED_STRINGS,
			self::$UNCOMPRESSED_STRINGS,
			$input
		);
	}

	public function parseProperty($input) {
		$value = $input==='false' || $input==='true' ? $input=='true' : str_replace('\"','"',preg_replace('/^(?:"|\')(.*)(?:"|\')$/s','$1',$input));
		return $value==='' ? null : $value;
	}

	public function cleanProperty(&$row, $key) {
		$input = $row[$key]['value'];
		if (is_string($input)) {
			$input = str_replace(array("\n"),array(''),$input);
			while (preg_match('/({TABLE |{SUM |{COUNT | DISTINCT | IN )/',$input,$resi)) {
				$input = str_replace($resi[0],strtolower($resi[0]),$input);
			}

			while (preg_match('/{{customVar code=([a-zA-Z0-9_-]+)}}/',$input,$resi)) {
				$input = str_replace($resi[0],'{customvar.'.$resi[1].'}',$input);
			}

			$regex = "{(weight|products_quantity|price_including_tax|price_excluding_tax|country)}";
			if (preg_match('/'.$regex.'/',$input,$resi)) {
				$this->addMessage('warning',$row,$key,'Usage of deprecated syntax %s','<span class="osh-formula">'.$resi[0].'</span>');
				while (preg_match('/'.$regex.'/',$input,$resi)) {
					switch ($resi[1]) {
						case 'price_including_tax':
						case 'price_excluding_tax':
						case 'weight':
							$input = str_replace($resi[0],"{cart.".$resi[1]."}",$input);
							break;
						case 'products_quantity': $input = str_replace($resi[0],"{cart.quantity}",$input); break;
						case 'country': $input = str_replace($resi[0],"{destination.country.name}",$input); break;
					}
				}
			}

			$regex1 = "{copy '([a-zA-Z0-9_]+)'\.'([a-zA-Z0-9_]+)'}";
			if (preg_match('/'.$regex1.'/',$input,$resi)) {
				$this->addMessage('warning',$row,$key,'Usage of deprecated syntax %s','<span class="osh-formula">'.$resi[0].'</span>');
				while (preg_match('/'.$regex1.'/',$input,$resi)) $input = str_replace($resi[0],'{'.$resi[1].'.'.$resi[2].'}',$input);
			}

			$regex1 = "{(count|all|any) (attribute|option) '([^'\)]+)' ?((?:==|<=|>=|<|>|!=) ?(?:".self::FLOAT_REGEX."|true|false|'[^'\)]*'))}";
			$regex2 = "{(sum) (attribute|option) '([^'\)]+)'}";
			if (preg_match('/'.$regex1.'/',$input,$resi) || preg_match('/'.$regex2.'/',$input,$resi)) {
				$this->addMessage('warning',$row,$key,'Usage of deprecated syntax %s','<span class="osh-formula">'.$resi[0].'</span>');
				while (preg_match('/'.$regex1.'/',$input,$resi) || preg_match('/'.$regex2.'/',$input,$resi)) {
					switch ($resi[1]) {
						case 'count':	$input = str_replace($resi[0],"{count products where product.".$resi[2]."s.".$resi[3].$resi[4]."}",$input); break;
						case 'all':		$input = str_replace($resi[0],"{count products where product.".$resi[2]."s.".$resi[3].$resi[4]."}=={products_quantity}",$input); break;
						case 'any':		$input = str_replace($resi[0],"{count products where product.".$resi[2]."s.".$resi[3].$resi[4]."}>0",$input); break;
						case 'sum':		$input = str_replace($resi[0],"{sum product.".$resi[2].".".$resi[3]."}",$input); break;
					}
				}
			}

			$regex = "((?:{| )product.(?:attribute|option))s.";
			if (preg_match('/'.$regex.'/',$input,$resi)) {
				$this->addMessage('warning',$row,$key,'Usage of deprecated syntax %s','<span class="osh-formula">'.$resi[0].'</span>');
				while (preg_match('/'.$regex.'/',$input,$resi)) {
					$input = str_replace($resi[0],$resi[1].'.',$input);
				}
			}

			$regex = "{table '([^']+)' (".self::COUPLE_REGEX."(?:, *".self::COUPLE_REGEX.")*)}";
			if (preg_match('/'.$regex.'/',$input,$resi)) {
				$this->addMessage('warning',$row,$key,'Usage of deprecated syntax %s','<span class="osh-formula">'.$resi[0].'</span>');
				while (preg_match('/'.$regex.'/',$input,$resi)) {
					switch ($resi[1]) {
						case 'products_quantity':
							$input = str_replace($resi[0],"{table {cart.weight} in ".$resi[2]."}*{cart.quantity}",$input);
							break;
						default:
							$input = str_replace($resi[0],"{table {cart.".$resi[1]."} in ".$resi[2]."}",$input);
							break;
					}
				}
			}
		}
		$row[$key]['value'] = $input;
	}

	protected function _parseInput($auto_correction)
	{
		$config_string = str_replace(
			array('&gt;', '&lt;', '“', '”', utf8_encode(chr(147)), utf8_encode(chr(148)), '&laquo;', '&raquo;', "\r\n", "\t"),
			array('>', '<', '"', '"', '"', '"', '"', '"', "\n", ' '),
			$this->_input
		);

		if (substr($config_string, 0, 2)=='$$') $config_string = $this->uncompress(substr($config_string, 2, strlen($config_string)));

		//echo ini_get('pcre.backtrack_limit');
		//exit;

		$this->debug('parse config (auto correction = '.self::esc(self::toString($auto_correction)).')');
		$config = null;
		$last_json_error = null;
		try {
			$config = self::json_decode($config_string);
		} catch (Exception $e) {
			$last_json_error = $e;
		}
		$auto_correction_warnings = array();
		$missing_enquote_of_property_name = array();
		if ($config) {
			foreach ($config as $code => $object) {
				if (!is_object($object)) {
					$config = null;
					break;
				}
			}
		}
		if ($auto_correction && !$config && $config_string!='[]') {
			if (preg_match_all('/((?:#+[^{\\n]*\\s+)+)\\s*{/s', $config_string, $result, PREG_SET_ORDER)) {
				$auto_correction_warnings[] = 'JSON: usage of incompatible comments';
				foreach ($result as $set) {
					$comment_lines = explode("\n", $set[1]);
					foreach ($comment_lines as $i => $line) {
						$comment_lines[$i] = preg_replace('/^#+\\s/', '', $line);
					}
					$comment = trim(implode("\n", $comment_lines));
					$config_string = str_replace($set[0], '{"about": "'.str_replace('"', '\\"', $comment).'",', $config_string);
				}
			}
			$property_regex = '\\s*(?<property_name>"?[a-z0-9_]+"?)\\s*:\\s*(?<property_value>"(?:(?:[^"]|\\\\")*[^\\\\])?"|'.self::FLOAT_REGEX.'|false|true|null)\\s*(?<property_separator>,)?\\s*(?:\\n)?';
			$object_regex = '(?:(?<object_name>"?[a-z0-9_]+"?)\\s*:\\s*)?{\\s*('.$property_regex.')+\\s*}\\s*(?<object_separator>,)?\\s*';
			preg_match_all('/('.$object_regex.')/is', $config_string, $object_set, PREG_SET_ORDER);
			//print_r($object_set);
			$json = array();
			$objects_count = count($object_set);
			$to_ignore_counter = -1;
			foreach ($object_set as $i => $object) {
				$pos = strpos($config_string, $object[0]);
				$to_ignore = trim(substr($config_string, 0, $pos));
				if ($to_ignore) {
					$to_ignore_counter++;
					if ($to_ignore_counter==0) {
						$bracket_pos = strpos($to_ignore, '{');
						if ($bracket_pos!==false) {
							$to_ignore = explode('{', $to_ignore, 2);
						}
					}
					$to_ignore = (array)$to_ignore;
					foreach ($to_ignore as $to_ignore_i) {
						$to_ignore_i = trim($to_ignore_i);
						if (!$to_ignore_i) continue;
						$auto_correction_warnings[] = 'JSON: ignored lines (<span class=osh-formula>'.self::toString($to_ignore_i).'</span>)';
						$n = 0;
						do {
							$key = 'meta'.$n;
							$n++;
						} while(isset($json[$key]));
						$json[$key] = array(
							'type' => 'meta',
							'ignored' => $to_ignore_i,
						);
					}
					$config_string = substr($config_string, $pos, strlen($config_string));
				}
				$config_string = str_replace($object[0], '', $config_string);
				$object_name = isset($object['object_name']) ? $object['object_name'] : null;
				$object_separator = isset($object['object_separator']) ? $object['object_separator'] : null;
				$is_last_object = ($i==$objects_count-1);
				if (!$is_last_object && $object_separator!=',') {
					$auto_correction_warnings[] = 'JSON: missing object separator (comma)';
				} else if ($is_last_object && $object_separator==',') {
					$auto_correction_warnings[] = 'JSON: no trailing object separator (comma) allowed';
				}
				$json_object = array();
				preg_match_all('/'.$property_regex.'/i', $object[0], $property_set, PREG_SET_ORDER);
				$properties_count = count($property_set);
				foreach ($property_set as $j => $property) {
					$name = $property['property_name'];
					if ($name{0}!='"' || $name{strlen($name)-1}!='"') {
						$auto_correction_warnings['missing_enquote_of_property_name'] = 'JSON: missing enquote of property name: %s';
						$missing_enquote_of_property_name[] = self::toString(trim($name, '"'));
					}
					$property_separator = isset($property['property_separator']) ? $property['property_separator'] : null;
					$is_last_property = ($j==$properties_count-1);
					if (!$is_last_property && $property_separator!=',') {
						$auto_correction_warnings[] = 'JSON: missing property separator (comma)';
					} else if ($is_last_property && $property_separator==',') {
						$auto_correction_warnings[] = 'JSON: no trailing property separator (comma) allowed';
					}
					$json_object[trim($name, '"')] = $this->parseProperty($property['property_value']);
				}
				if ($object_name) $json[trim($object_name, '"')] = $json_object;
				else if (isset($json_object['code'])) {
					$code = $json_object['code'];
					unset($json_object['code']);
					$json[$code] = $json_object;
				} else $json[] = $json_object;
			}
			$to_ignore = trim($config_string);
			if ($to_ignore) {
				$bracket_pos = strpos($to_ignore, '}');
				if ($bracket_pos!==false) {
					$to_ignore = explode('}', $to_ignore, 2);
				}
				$to_ignore = (array)$to_ignore;
				foreach ($to_ignore as $to_ignore_i) {
					$to_ignore_i = trim($to_ignore_i);
					if (!$to_ignore_i) continue;
					$auto_correction_warnings[] = 'JSON: ignored lines (<span class=osh-formula>'.self::toString($to_ignore_i).'</span>)';
					$n = 0;
					do {
						$key = 'meta'.$n;
						$n++;
					} while(isset($json[$key]));
					$json[$key] = array(
						'type' => 'meta',
						'ignored' => $to_ignore_i,
					);
				}
			}
			$config_string = $this->jsonEncode($json);//'['.$config_string2.']';
			$config_string = str_replace(array("\n"), array("\\n"), $config_string);
			//echo $config_string;

			$last_json_error = null;
			try {
				$config = self::json_decode($config_string);
			} catch (Exception $e) {
				$last_json_error = $e;
			}
		}
		if ($last_json_error) {
			$auto_correction_warnings[] = 'JSON: unable to parse config ('.$last_json_error->getMessage().')';
		}

		$row = null;
		$auto_correction_warnings = array_unique($auto_correction_warnings);
		foreach ($auto_correction_warnings as $key => $warning) {
			if ($key=='missing_enquote_of_property_name') {
				$missing_enquote_of_property_name = array_unique($missing_enquote_of_property_name);
				$warning = str_replace('%s', '<span class=osh-key>'.self::esc(implode('</span>, <span class=osh-key>', $missing_enquote_of_property_name)).'</span>', $warning);
			}
			$this->addMessage('warning', $row, null, $warning);
		}
		$config = (array)$config;

		$this->_config = array();
		$available_keys = array('type', 'about', 'label', 'enabled', 'description', 'fees', 'conditions', 'shipto', 'billto', 'origin', 'customer_groups', 'tracking_url');
		$reserved_keys = array('*id');
		if ($auto_correction) {
			$available_keys = array_merge($available_keys, array(
				'destination', 'code',
			));
		}

		$deprecated_properties = array();
		$unknown_properties = array();

		foreach ($config as $code => $object) {
			$object = (array)$object;
			if ($auto_correction) {
				if (isset($object['destination'])) {
					if (!in_array('destination', $deprecated_properties)) $deprecated_properties[] = 'destination';
					$object['shipto'] = $object['destination'];
					unset($object['destination']);
				}
				if (isset($object['code'])) {
					if (!in_array('code', $deprecated_properties)) $deprecated_properties[] = 'code';
					$code = $object['code'];
					unset($object['code']);
				}
			}

			$row = array();
			$i = 1;
			foreach ($object as $property_name => $property_value) {
				if (in_array($property_name, $reserved_keys)) {
					continue;
				}
				if (in_array($property_name, $available_keys)
					|| substr($property_name, 0, 1)=='_'
					|| in_array($object['type'], array('data', 'meta'))) {
					if (isset($property_value)) {
						$row[$property_name] = array('value' => $property_value, 'original_value' => $property_value);
						if ($auto_correction) $this->cleanProperty($row, $property_name);
					}
				} else {
					if (!in_array($property_name, $unknown_properties)) $unknown_properties[] = $property_name;
				}
				$i++;
			}
			$this->addRow($code, $row);
		}
		$row = null;
		if (count($unknown_properties)>0) $this->addMessage('error', $row, null, 'Usage of unknown properties %s', ': <span class=osh-key>'.implode('</span>, <span class=osh-key>', $unknown_properties).'</span>');
		if (count($deprecated_properties)>0) $this->addMessage('warning', $row, null, 'Usage of deprecated properties %s', ': <span class=osh-key>'.implode('</span>, <span class=osh-key>', $deprecated_properties).'</span>');
	}

	public function addRow($code, &$row)
	{
		if ($code) {
			if (isset($this->_config[$code])) $this->addMessage('error', $row, 'code', 'The id must be unique, `%s` has been found twice', $code);
			while (isset($this->_config[$code])) $code .= rand(0, 9);
		}
		$row['*id'] = $code;
		$this->_config[$code] = $row;
	}

	public function addMessage($type, &$row, $property) {
		$args = func_get_args();
		array_shift($args);
		array_shift($args);
		array_shift($args);
		$message = new GLS_Message($type,$args);
		if (isset($row)) {
			if (isset($property)) {
				$row[$property]['messages'][] = $message;
			} else {
				$row['*messages'][] = $message;
			}
		}
		$this->_messages[] = $message;
		self::debug('   => <span class="osh-'.$message->type.'">'.$message->toString().'</span>');
	}

	protected function _addRow(&$row) {
		if (isset($row['code'])) {
			$key = $row['code']['value'];
			if (isset($this->_config[$key])) $this->addMessage('error',$row,'code','The property `code` must be unique, `%s` has been found twice',$key);
			while (isset($this->_config[$key])) $key .= rand(0,9);
			//$row['code'] = $key;
		} else {
			$i = 1;
			do {
				$key = 'code_auto'.sprintf('%03d',$i);
				$i++;
			} while (isset($this->_config[$key]));
		}
		$row['*id'] = $key;
		$this->_config[$key] = $row;
	}

	protected function _addIgnoredLines($lines) {
		$this->_config[] = array('lines' => $lines);
	}

	protected function _addressMatch($address_filter, $address) {
		$excluding = false;

		$address_filter = trim($address_filter);
		$address_filter = str_replace(
			array('\(', '\)', '\,'),
			array('__opening_parenthesis__', '__closing_parenthesis__', '__comma__'),
			$address_filter
		);

		if ($address_filter=='*') {
			self::debug('      country code <span class="osh-replacement">'.$address['country_code'].'</span> matches');
			return true;
		}

		if (preg_match('#\* *- *\((.*)\)#s',$address_filter,$result)) {
			$address_filter = $result[1];
			$excluding = true;
		}

		$tmp_address_filter_array = explode(',',trim($address_filter));

		$concat = false;
		$concatened = '';
		$address_filter_array = array();
		$i = 0;

		foreach ($tmp_address_filter_array as $address_filter) {
			if ($concat) $concatened .= ','.$address_filter;
			else {
				if ($i<count($tmp_address_filter_array)-1 && preg_match('#\(#',$address_filter)) {
					$concat = true;
					$concatened .= $address_filter;
				} else $address_filter_array[] = $address_filter;
			}
			if (preg_match('#\)#',$address_filter)) {
				$address_filter_array[] = $concatened;
				$concatened = '';
				$concat = false;
			}
			$i++;
		}

		foreach ($address_filter_array as $address_filter) {
			$address_filter = trim($address_filter);
			if (preg_match('#([A-Z]{2}) *(-)? *(?:\( *(-)? *(.*)\))?#s', $address_filter, $result)) {
				$country_code = $result[1];
				$glsCountries = array('FR', 'MC', 'AD', 'BE');
				$international = Mage::getStoreConfig('carriers/gls/international');
				if ($international == '0') {
					$glsCountries = array('FR', 'MC', 'AD');
				} elseif ($international == '1') {
					$glsCountries = array('FR', 'MC', 'AD', 'BE');
				} elseif ($international == '2') {
					$glsCountries = array('BE');
				}
				if ($address['country_code']==$country_code && in_array($country_code, $glsCountries)) {//ADDONLINE : on limite GLS à la France (Monaco et Andorre) et la Belgique selon la configuration de l'option international
					self::debug('      country code <span class="osh-replacement">'.$address['country_code'].'</span> matches');
					if (!isset($result[4]) || $result[4]=='') return !$excluding;
					else {
						$region_codes = explode(',',$result[4]);
						$in_array = false;
						for ($i=count($region_codes); --$i>=0;) {
							$code = trim(str_replace(
								array('__opening_parenthesis__', '__closing_parenthesis__', '__comma__'),
								array('(', ')', ','),
								$region_codes[$i]
							));
							$region_codes[$i] = $code;
							if ($address['region_code']===$code) {
								self::debug('      region code <span class="osh-replacement">'.$address['region_code'].'</span> matches');
								$in_array = true;
							} else if ($address['postcode']===$code) {
								self::debug('      postcode <span class="osh-replacement">'.$address['postcode'].'</span> matches');
								$in_array = true;
							} else if (mb_substr($code,0,1)=='/' && mb_substr($code,mb_strlen($code)-1,1)=='/' && @preg_match($code, $address['postcode'])) {
								self::debug('      postcode <span class="osh-replacement">'.$address['postcode'].'</span> matches <span class="osh-formula">'.htmlentities($code).'</span>');
								$in_array = true;
							} else if (strpos($code,'*')!==false && preg_match('/^'.str_replace('*','(?:.*)',$code).'$/',$address['postcode'])) {
								self::debug('      postcode <span class="osh-replacement">'.$address['postcode'].'</span> matches <span class="osh-formula">'.htmlentities($code).'</span>');
								$in_array = true;
							}
							if ($in_array) break;
						}
						if (!$in_array) {
							self::debug('      region code <span class="osh-replacement">'.$address['region_code'].'</span> and postcode <span class="osh-replacement">'.$address['postcode'].'</span> don\'t match');
						}
						// Vérification stricte
						/*$in_array = in_array($address['region_code'],$region_codes,true) || in_array($address['postcode'],$region_codes,true);*/
						$excluding_region = $result[2]=='-' || $result[3]=='-';
						if ($excluding_region && !$in_array || !$excluding_region && $in_array) return !$excluding;
					}
				}
			}
		}
		return $excluding;
	}

	protected function _getProductProperty($product, $property_type, $property_name, $get_by_id=false) {
		switch ($property_type) {
			case 'attribute':
			case 'attributes': return $product->getAttribute($property_name,$get_by_id);
			case 'option':
			case 'options': return $product->getOption($property_name,$get_by_id);
			case 'stock': return $product->getStockData($property_name);
		}
		return null;
	}

	protected function _processProductProperty($products, $regex_result) {
		// count, sum, min, max, count distinct
		$operation = strtolower($regex_result[1]);
		switch ($operation) {
			case 'sum':
			case 'min':
			case 'max':
			case 'count distinct':
				$property_type = $regex_result[2];
				$property_name = $regex_result[3];
				$conditions = isset($regex_result[4]) ? $regex_result[4] : null;
				break;
			case 'count':
				$conditions = isset($regex_result[2]) ? $regex_result[2] : null;
				break;
		}

		self::debug('      :: start <span class="osh-replacement">'.$regex_result[0].'</span>');

		$return_value = null;

		preg_match_all('/product\.(attribute(?:s)?|option(?:s)?|stock)\.([a-z0-9_+-]+)(?:\.(id))?/i',$conditions,$properties_regex_result,PREG_SET_ORDER);
		$properties = array();
		foreach ($properties_regex_result as $property_regex_result) {
			$key = $property_regex_result[0];
			if (!isset($properties[$key])) $properties[$key] = $property_regex_result;
		}
		preg_match_all('/product\.(quantity)/i',$conditions,$properties_regex_result,PREG_SET_ORDER);
		foreach ($properties_regex_result as $property_regex_result) {
			$key = $property_regex_result[0];
			if (!isset($properties[$key])) $properties[$key] = $property_regex_result;
		}

		foreach ($products as $product) {
			if (isset($conditions) && $conditions!='') {
				$formula = $conditions;
				foreach ($properties as $property) {
					if ($property[1]=='quantity') {
						$value = $product->getQuantity();
					} else {
						$value = $this->_getProductProperty(
							$product,
							$tmp_property_type = $property[1],
							$tmp_property_name = $property[2],
							$get_by_id = isset($property[3]) && $property[3]=='id'
						);
					}
					//$formula = $this->replace($property[0],$value,$formula);
					$from = $property[0];
					$to = is_string($value) || empty($value) ? "'".$value."'" : $value;
					$formula = str_replace($from,$to,$formula);
					self::debug('         replace <span class="osh-replacement">'.$from.'</span> by <span class="osh-replacement">'.$to.'</span> =&gt; <span class="osh-formula">'.str_replace($from,'<span class="osh-replacement">'.$to.'</span>',$formula).'</span>');
				}
				$eval_result = $this->_evalFormula($formula);
				if (!isset($eval_result)) return 'null';
			}
			else $eval_result = true;

			if ($eval_result==true) {
				if ($operation=='count') {
					$return_value = (isset($return_value) ? $return_value : 0) + $product->getQuantity();
				} else {
					if ($property_type=='quantity') {
						$value = $product->getQuantity();
					} else {
						$value = $this->_getProductProperty($product,$property_type,$property_name);
					}
					switch ($operation) {
						case 'min':
							if (!isset($return_value) || $value<$return_value) $return_value = $value;
							break;
						case 'max':
							if (!isset($return_value) || $value>$return_value) $return_value = $value;
							break;
						case 'sum':
							//self::debug($product->getSku().'.'.$property_type.'.'.$property_name.' = "'.$value.'" x '.$product->getQuantity());
							$return_value = (isset($return_value) ? $return_value : 0) + $value*$product->getQuantity();
							break;
						case 'count distinct':
							if (!isset($return_value)) $return_value = 0;
							if (!isset($distinct_values)) $distinct_values = array();
							if (!in_array($value,$distinct_values)) {
								$distinct_values[] = $value;
								$return_value++;
							}
							break;
					}
				}
			}
		}

		self::debug('      :: end <span class="osh-replacement">'.$regex_result[0].'</span>');

		return $return_value;
	}

	protected static function json_decode($input)
	{
		if (function_exists('json_decode')) { // PHP >= 5.2.0
			$output = json_decode($input);
			if (function_exists('json_last_error')) { // PHP >= 5.3.0
				$error = json_last_error();
				if ($error!=JSON_ERROR_NONE) throw new Exception($error);
			}
			return $output;
		} else {
			return Zend_Json::decode($input);
		}
	}

	protected static function json_encode($input)
	{
		if (function_exists('json_encode')) {
			return json_encode($input);
		} else {
			return Zend_Json::encode($input);
		}
	}

}

interface GLS_Product_Interface {
	public function getOption($option);
	public function getAttribute($attribute);
	public function getName();
	public function getSku();
	public function getQuantity();
	public function getStockData($key);
}

class GLS_Message {
	public $type;
	public $message;
	public $args;

	public function GLS_Message($type, $args) {
		$this->type = $type;
		$this->message = array_shift($args);
		$this->args = $args;
	}

	public function toString() {
		return vsprintf($this->message,$this->args);
	}
}

class GLS_Result {
	public $success;
	public $result;

	public function GLS_Result($success, $result=null) {
		$this->success = $success;
		$this->result = $result;
	}

	public function __toString() {
		return is_bool($this->result) ? ($this->result ? 'true' : 'false') : (string)$this->result;
	}
}


?>