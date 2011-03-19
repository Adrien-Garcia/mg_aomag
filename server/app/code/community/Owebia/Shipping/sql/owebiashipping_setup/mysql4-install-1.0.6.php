<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
INSERT INTO `{$installer->getTable('adminnotification_inbox')}` (`severity`,`date_added`,`title`,`description`,`url`,`is_read`,`is_remove`)
  VALUES (1,NOW(),'Configuration du module Owebia Shipping','Depuis la version 1.0.5, le module Owebia Shipping est fournit avec la configuration complète de Colissimo aux tarifs du 1er mars 2008. Vous pouvez retrouver des modèles de configuration à l''adresse suivante : http://www.magentocommerce.com/boards/viewthread/20422/','http://www.magentocommerce.com/boards/viewthread/20422/',0,0);
");
$installer->endSetup();
