<?php

$installer = $this;

$connection = $installer->getConnection();

$installer->startSetup();

$installer->run("
		DELETE FROM {$this->getTable('core_config_data')} WHERE path = 'carriers/gls/livraisontoyou';
		DELETE FROM {$this->getTable('core_config_data')} WHERE path = 'carriers/gls/configtoyou';
		DELETE FROM {$this->getTable('core_config_data')} WHERE path = 'carriers/gls/ordertoyou';
		");

$installer->endSetup();
