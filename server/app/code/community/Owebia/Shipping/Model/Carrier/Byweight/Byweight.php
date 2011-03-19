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

include_once dirname(__FILE__).'/AbstractDestinationWeightShipping.php';

class Owebia_Shipping_Model_Carrier_Byweight_Byweight
	extends Owebia_Shipping_Model_Carrier_Byweight_AbstractDestinationWeightShipping
{
	/**
	 * unique internal shipping method identifier
	 *
	 * @var string [a-z0-9_]
	 */
	protected $_code = 'byweight';
}

?>