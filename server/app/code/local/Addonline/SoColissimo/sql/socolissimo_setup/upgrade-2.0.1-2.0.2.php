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

		UPDATE  {$this->getTable('socolissimoliberte_relais')}  SET code_reseau = 'R01' WHERE code_reseau='';
		ALTER TABLE {$this->getTable('socolissimoliberte_relais')} 
		ADD UNIQUE INDEX identifiant_reseau (identifiant ASC, code_reseau ASC),
		DROP INDEX identifiant;
		
");

$installer->endSetup();