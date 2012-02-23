<?php
/**
 * Addonline_SoColissimoLiberte
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimoLiberte
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();
$this->addAttribute('quote_address', 'soco_product_code', array(
    'type'     => 'static',
    'label'    => 'Code livrasion socolissimo',
    'required' => false,
    'input'    => 'text',
));

$installer->endSetup();