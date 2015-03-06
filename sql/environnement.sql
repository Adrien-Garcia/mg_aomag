
use aomagento;

-- de local vers pr�prod
UPDATE core_config_data SET value='http://innerjoy.preprod.addonline.biz/' WHERE value='http://aomagento.clement.addonline.devl/';
UPDATE cms_page SET content=REPLACE(content,'http://aomagento.clement.addonline.devl/','http://innerjoy.preprod.addonline.biz/');
UPDATE cms_block SET content=REPLACE(content,'http://aomagento.clement.addonline.devl/','http://innerjoy.preprod.addonline.biz/');
UPDATE catalog_product_entity_text SET value=REPLACE(value,'http://aomagento.clement.addonline.devl/','http://innerjoy.preprod.addonline.biz/') WHERE attribute_id IN (97,506);
UPDATE core_config_data SET value='mail.add-online.fr' WHERE path='system/smtp/host';
UPDATE core_config_data SET value='1' WHERE path='dev/log/active';
-- FIN : de local vers pr�prod

-- de pr�prod vers local
UPDATE core_config_data SET value='http://btb.laetitia.jetpulp.dev/' WHERE value='http://aomagento.clement.addonline.biz/';
UPDATE cms_page SET content=REPLACE(content,'http://aomagento.clement.addonline.biz/','http://btb.laetitia.jetpulp.dev/');
UPDATE cms_block SET content=REPLACE(content,'http://aomagento.clement.addonline.biz/','http://btb.laetitia.jetpulp.dev/');
UPDATE catalog_product_entity_text SET value=REPLACE(value,'http://aomagento.clement.addonline.biz/','http://btb.laetitia.jetpulp.dev/') WHERE attribute_id IN (97,506);
UPDATE core_config_data SET value='mail.add-online.fr' WHERE path='system/smtp/host';
UPDATE core_config_data SET value='1' WHERE path='dev/log/active';
-- FIN : de local vers pr�prod