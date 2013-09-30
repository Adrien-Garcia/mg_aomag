<?php

$installer = $this;

$connection = $installer->getConnection();

$installer->startSetup();

$installer->removeAttribute('catalog_category', "meta_robots");
$installer->addAttribute('catalog_category', "meta_robots", array(
		'label'        => "Meta Robots",
		'visible'      => true,
		'required'     => false,
		'user_defined' => false,
		'type'         => 'varchar',
		'input' 		=> 'select',
		'default'       => '',
		'source'  		=> 'seo/source_robots',
));

$installer->endSetup();
