<?php
/**
 * Addonline_GLS
 *
 * @category    Addonline
 * @package     Addonline_GLS
 * @copyright   Copyright (c) 2013 GLS
 * @author 	    Addonline (http://www.addonline.fr)
 */

$installer = $this;

$installer->startSetup();

$this->addAttribute('order', 'gls_relay_point_id', array(
		'type'     => 'varchar',
		'label'    => 'Id du point relay GLS',
		'visible'  => true,
		'required' => false,
		'input'    => 'text',
));

$this->addAttribute('order', 'gls_warn_by_phone', array(
		'type'     => 'varchar',
		'label'    => 'Prévenir par téléphone',
		'visible'  => true,
		'required' => false,
		'input'    => 'text',
));

$installer->endSetup();