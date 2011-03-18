
use aomagento;

-- de local vers préprod
UPDATE core_config_data SET value='http://dam.preprod.addonline.biz/' WHERE value='http://aomagento.addonline.devl/';
UPDATE cms_page SET content=REPLACE(content,'http://aomagento.addonline.devl/','http://aomagento.preprod.addonline.biz/');
UPDATE cms_block SET content=REPLACE(content,'http://aomagento.addonline.devl/','http://aomagento.preprod.addonline.biz/');
UPDATE core_config_data SET value='mail.add-online.fr' WHERE path='system/smtp/host';
UPDATE core_config_data SET value='1' WHERE path='dev/log/active';
-- FIN : de local vers préprod
