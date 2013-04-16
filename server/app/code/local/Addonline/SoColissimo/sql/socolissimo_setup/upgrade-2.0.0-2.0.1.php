<?php
/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

$installer = $this;

$installer->startSetup();

$installer->run("

		ALTER TABLE {$this->getTable('socolissimoliberte_relais')} 
		ADD COLUMN code_reseau VARCHAR(3) NOT NULL DEFAULT '',
		ADD COLUMN libelle_nl varchar(50) NOT NULL DEFAULT '',
		ADD COLUMN adresse_nl varchar(38) NOT NULL DEFAULT '',
		ADD COLUMN commune_nl varchar(38) NOT NULL DEFAULT '',
		MODIFY COLUMN commune varchar(38) NOT NULL,
		ADD INDEX type_relais (type_relais ASC);
");

$installer->endSetup();