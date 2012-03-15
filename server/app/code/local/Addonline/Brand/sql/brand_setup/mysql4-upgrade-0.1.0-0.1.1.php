<?php
$installer = $this;
$installer->startSetup();

$installer->run("

		ALTER TABLE {$this->getTable('brand')}
		ADD url_key varchar(255) NOT NULL default '';
		
");

$installer->endSetup();