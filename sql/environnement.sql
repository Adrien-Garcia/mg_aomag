###################################################################
# MERCI DE NE PAS MODIFIER LES URLS DE CE FICHIER !!!!!!!!!!!!!!!!!
###################################################################

use aomagento;

-- de local vers pr�prod
UPDATE core_config_data SET value='http://aomagento.addonline.biz/' WHERE value='http://aomagento.jetpulp.dev/';
UPDATE cms_page SET content=REPLACE(content,'http://aomagento.jetpulp.dev/','http://aomagento.addonline.biz/');
UPDATE cms_block SET content=REPLACE(content,'http://aomagento.jetpulp.dev/','http://aomagento.addonline.biz/');
UPDATE catalog_product_entity_text SET value=REPLACE(value,'http://aomagento.jetpulp.dev/','http://aomagento.addonline.biz/') WHERE attribute_id IN (97,506);
UPDATE core_config_data SET value='mail.add-online.fr' WHERE path='system/smtp/host';
UPDATE core_config_data SET value='1' WHERE path='dev/log/active';
-- FIN : de local vers pr�prod

-- de pr�prod vers local
UPDATE core_config_data SET value='http://aomagento.jetpulp.dev/' WHERE value='http://aomagento.addonline.biz/';
UPDATE cms_page SET content=REPLACE(content,'http://aomagento.addonline.biz/','http://aomagento.jetpulp.dev/');
UPDATE cms_block SET content=REPLACE(content,'http://aomagento.addonline.biz/','http://aomagento.jetpulp.dev/');
UPDATE catalog_product_entity_text SET value=REPLACE(value,'http://aomagento.addonline.biz/','http://aomagento.jetpulp.dev/') WHERE attribute_id IN (97,506);
UPDATE core_config_data SET value='mail.add-online.fr' WHERE path='system/smtp/host';
UPDATE core_config_data SET value='1' WHERE path='dev/log/active';
-- FIN : de local vers pr�prod