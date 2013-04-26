<?php


$installer = $this;

$installer->startSetup();

$this->addAttribute('order', 'expeditorinet', array(
		'type'     => 'int',
		'label'    => 'Exporté Expeditorinet',
		'visible'  => true,
		'required' => false,
		'input'    => 'text',
		'default'  => 0,
));

$installer->endSetup();