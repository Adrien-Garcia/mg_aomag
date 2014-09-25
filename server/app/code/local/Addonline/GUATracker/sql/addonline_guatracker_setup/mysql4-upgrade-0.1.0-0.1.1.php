<?php

$installer = $this;

$installer->startSetup();

$installer->run("

        CREATE TABLE IF NOT EXISTS {$this->getTable('guatracker_ordersinfos')}  (
				`id_quote` varchar(100) NOT NULL,				
				`ga_unique_id` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
				`ga_utmz` varchar(400) COLLATE utf8_unicode_ci,
				`tracked_in` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
				UNIQUE KEY `id_quote` (`id_quote`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
");

$installer->endSetup();