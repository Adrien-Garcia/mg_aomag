<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$installer->startSetup();
$connection->insert($installer->getTable('cms/page'), array(
    'title'             => 'Meilleurs ventes',
    'root_template'     => 'one_column',
    'identifier'        => 'products_bestseller',
    'content'           => "<h1><strong>Meilleures ventes</strong></h1><p><strong style=\"color: #de036f;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.</strong></p><p>Vivamus tortor nisl, lobortis in, faucibus et, tempus at, dui. Nunc  risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.  Pellentesque nisl. Pellentesque habitant morbi tristique senectus et  netus et malesuada fames ac turpis egestas. Nunc augue. Aenean sed justo  non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec,  tempor nec, justo.</p>",
    'layout_update_xml' => "<reference name=\"content\">
<block type=\"aocatalog/product_list_bestseller\" name=\"bestseller.product.list\" alias=\"bestseller_list\" template=\"catalog\product\list.phtml\" after=\"cms_page\">
                    <block type=\"catalog/product_list_toolbar\" name=\"product_list_toolbar\" template=\"catalog/product/list/toolbar.phtml\">
                        <block type=\"page/html_pager\" name=\"product_list_toolbar_pager\"/>
                    </block>
                    <action method=\"addColumnCountLayoutDepend\"><layout>empty</layout><count>6</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>one_column</layout><count>5</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>two_columns_left</layout><count>4</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>two_columns_right</layout><count>4</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>three_columns</layout><count>3</count></action>
                    <action method=\"setToolbarBlockName\"><name>product_list_toolbar</name></action>
</block>
</reference>",
	'creation_time'     => now(),
    'update_time'       => now(),
));
$connection->insert($installer->getTable('cms/page_store'), array(
    'page_id'   => $connection->lastInsertId(),
    'store_id'  => 0
));
$connection->insert($installer->getTable('cms/page'), array(
    'title'             => 'Promotions',
    'root_template'     => 'one_column',
    'identifier'        => 'products_promotion',
    'content'           => "<h1><strong>Promotions</strong></h1><p><strong style=\"color: #de036f;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.</strong></p><p>Vivamus tortor nisl, lobortis in, faucibus et, tempus at, dui. Nunc  risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.  Pellentesque nisl. Pellentesque habitant morbi tristique senectus et  netus et malesuada fames ac turpis egestas. Nunc augue. Aenean sed justo  non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec,  tempor nec, justo.</p>",
    'layout_update_xml' => "<reference name=\"content\">
<block type=\"aocatalog/product_list_promotion\" name=\"promotion.product.list\" alias=\"promotion_list\" template=\"catalog\product\list.phtml\" after=\"cms_page\">
                    <block type=\"catalog/product_list_toolbar\" name=\"product_list_toolbar\" template=\"catalog/product/list/toolbar.phtml\">
                        <block type=\"page/html_pager\" name=\"product_list_toolbar_pager\"/>
                    </block>
                    <action method=\"addColumnCountLayoutDepend\"><layout>empty</layout><count>6</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>one_column</layout><count>5</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>two_columns_left</layout><count>4</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>two_columns_right</layout><count>4</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>three_columns</layout><count>3</count></action>
                    <action method=\"setToolbarBlockName\"><name>product_list_toolbar</name></action>
</block>
</reference>",
	'creation_time'     => now(),
    'update_time'       => now(),
));
$connection->insert($installer->getTable('cms/page_store'), array(
    'page_id'   => $connection->lastInsertId(),
    'store_id'  => 0
));
$connection->insert($installer->getTable('cms/page'), array(
    'title'             => 'Nouveautés',
    'root_template'     => 'one_column',
    'identifier'        => 'products_new',
    'content'           => "<h1><strong>Nouveautés</strong></h1><p><strong style=\"color: #de036f;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.</strong></p><p>Vivamus tortor nisl, lobortis in, faucibus et, tempus at, dui. Nunc  risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.  Pellentesque nisl. Pellentesque habitant morbi tristique senectus et  netus et malesuada fames ac turpis egestas. Nunc augue. Aenean sed justo  non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec,  tempor nec, justo.</p>",
    'layout_update_xml' => "<reference name=\"content\">
<block type=\"aocatalog/product_list_new\" name=\"new.product.list\" alias=\"new_list\" template=\"catalog\product\list.phtml\" after=\"cms_page\">
                    <block type=\"catalog/product_list_toolbar\" name=\"product_list_toolbar\" template=\"catalog/product/list/toolbar.phtml\">
                        <block type=\"page/html_pager\" name=\"product_list_toolbar_pager\"/>
                    </block>
                    <action method=\"addColumnCountLayoutDepend\"><layout>empty</layout><count>6</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>one_column</layout><count>5</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>two_columns_left</layout><count>4</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>two_columns_right</layout><count>4</count></action>
                    <action method=\"addColumnCountLayoutDepend\"><layout>three_columns</layout><count>3</count></action>
                    <action method=\"setToolbarBlockName\"><name>product_list_toolbar</name></action>
</block>
</reference>",
    'creation_time'     => now(),
    'update_time'       => now(),
));
$connection->insert($installer->getTable('cms/page_store'), array(
    'page_id'   => $connection->lastInsertId(),
    'store_id'  => 0
));
$installer->endSetup();
