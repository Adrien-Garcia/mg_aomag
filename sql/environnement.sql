
use aomagento;

-- de local vers pr�prod
UPDATE core_config_data SET value='http://aomagento.addonline.biz/' WHERE value='http://aomagento.addonline.devl/';
UPDATE cms_page SET content=REPLACE(content,'http://aomagento.addonline.devl/','http://aomagento.addonline.biz/');
UPDATE cms_block SET content=REPLACE(content,'http://aomagento.addonline.devl/','http://aomagento.addonline.biz/');
UPDATE core_config_data SET value='mail.add-online.fr' WHERE path='system/smtp/host';
UPDATE core_config_data SET value='1' WHERE path='dev/log/active';
-- FIN : de local vers pr�prod

-- de pr�prod vers local
UPDATE core_config_data SET value='http://aomagento.addonline.devl/' WHERE value='http://aomagento.addonline.biz/';
UPDATE cms_page SET content=REPLACE(content,'http://aomagento.addonline.biz/','http://aomagento.addonline.devl/');
UPDATE cms_block SET content=REPLACE(content,'http://aomagento.addonline.biz/','http://aomagento.addonline.devl/');
UPDATE core_config_data SET value='mail.add-online.fr' WHERE path='system/smtp/host';
UPDATE core_config_data SET value='1' WHERE path='dev/log/active';
-- FIN : de local vers pr�prod