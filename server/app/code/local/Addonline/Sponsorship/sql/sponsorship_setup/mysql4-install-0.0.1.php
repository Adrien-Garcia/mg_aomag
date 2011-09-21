<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();
$date = now();
$storeId = Mage::app()->getStore()->getId();
$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('sponsorship')} (
  `sponsorship_id` int(11) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL,
  `child_mail` varchar(255) NOT NULL default '',
  `child_firstname` varchar(255) NOT NULL default '',
  `child_lastname` varchar(255) NOT NULL default '',
  `datetime` datetime NULL,
  `message` text,
  `parent_mail` varchar(255),
  `parent_name` varchar(255),
  `subject` varchar(255),
  `datetime_boost` datetime,
  PRIMARY KEY (`sponsorship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('cms_page')}`
    (`title`, `root_template`, `meta_keywords`, `meta_description`, `identifier`, `content_heading`, `content`, `creation_time`,
        `update_time`, `is_active`, `sort_order`, `layout_update_xml`, `custom_theme`, `custom_theme_from`,
        `custom_theme_to`)
VALUES (
'Sample of sponsorship principles', 'two_columns_right', 'Sponsorship', 'Information on sponsorship', 'sponsorship_info',
'Sample of sponsorship principles',
 '<p>By sponsoring friends you can earn:</p>\r\n<ul class=\"disc\">\r\n<li>Cash</li>\r\n<li>Vouchers</li>\r\n</ul>\r\n<p>Every time one of your godson order, you win 5% of its order !</p>\r\n<p>But that\'s not all, if your godson sponsors too, you earn 50% of what your godson has won...</p>\r\n<p>For example,</p>\r\n<ul class=\"disc\">\r\n<li>your godson place an order of 100 euros (you win 5 points)</li>\r\n<li>your godson sponsors 2 friends who order 100 euros each (you win 5 points)</li>\r\n<li>the godchildren of your godson each sponsor 2 people who order 100 euros each (you win 5 points)...</li>\r\n</ul>\r\n<p>Then you can exchange your points into cash or vouchers.</p>\r\n<p style=\"text-align:right;\"><a href=\"../sponsorship\">Yes I want sponsors friends to earn cash or vouchers !</a></p>',
 '$date', '$date', 1, 0, '', '', NULL , NULL
);

INSERT INTO `{$this->getTable('cms/page_store')}` (`page_id`, `store_id`) VALUES
(LAST_INSERT_ID(), $storeId);
");
    
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$setup->addAttribute('customer', 'sponsor', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Parrain',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
    ));
    
$setup->endSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('sponsorship_sponsor_log')} (
  `sponsorship_sponsor_log_id` int(10) unsigned NOT NULL auto_increment,
  `godson_id` int(10) unsigned NOT NULL,
  `sponsor_id` int(10) unsigned NOT NULL,
  `record_id` int(10) unsigned NOT NULL,
  `record_type` enum('order', 'gift', 'coupon_code', 'cash', 'admin', 'first') CHARACTER SET utf8 NOT NULL,
  `datetime` datetime NOT NULL,
  `points` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY (`sponsorship_sponsor_log_id`),
  KEY `INDEX_SPONSOR_LOG_GODSON_ID` (`godson_id`),
  KEY `INDEX_SPONSOR_LOG_SPONSOR_ID` (`sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
    
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('sponsorship_change')} (
  `sponsorship_change_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL,
  `type` enum('gift', 'coupon', 'cash') NOT NULL,
  `module` enum('fidelity', 'sponsor') NOT NULL,
  `statut` enum('waiting', 'exported', 'solved', 'canceled') NOT NULL,
  `datetime` datetime NOT NULL,
  `points` decimal(12,4) NOT NULL default '0.0000',
  `value` varchar(250) NOT NULL,
  PRIMARY KEY (`sponsorship_change_id`),
  KEY `INDEX_CHANGE_CUSTOMER_ID` (`customer_id`),
  KEY `INDEX_CHANGE_TYPE` (`type`),
  KEY `INDEX_CHANGE_MODULE` (`module`),
  KEY `INDEX_CHANGE_STATUT` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
    
    
$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('sponsorship_openinviter')} (
  `sponsorship_openinviter_id` int(11) unsigned NOT NULL auto_increment,
  `code` varchar(255) NOT NULL,
  `image` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`sponsorship_openinviter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 

Mage::getSingleton( 'eav/config' )
		->getAttribute( 'customer', 'sponsor' )
		->setData( 'used_in_forms', array( 'adminhtml_customer' ) )
		->save();
		
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->updateAttribute('customer', 'sponsor', 'group', 'Default');
		
$setup->updateAttribute('customer', 'sponsor', 'sort_order', 250);
$setup->endSetup();

