<?php
$installer = $this;

$installer->startSetup();

$installer->run("
		
		INSERT INTO cms_page(title, identifier, content) VALUES('Toutes les marques', 'nos-marques', '<p>{{block type=\"brand/brand_list\" template=\"brand/listbrand.phtml\"}}</p>');
		INSERT INTO cms_page_store(page_id) SELECT max(page_id) FROM cms_page;
		UPDATE cms_page_store SET store_id = 0 WHERE page_id = (SELECT max(page_id) FROM cms_page);
");

$installer->endSetup();