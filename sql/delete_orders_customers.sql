
SET FOREIGN_KEY_CHECKS=0;

TRUNCATE sales_flat_order;
TRUNCATE sales_flat_order_grid;
TRUNCATE sales_flat_order_item;
TRUNCATE sales_flat_order_payment;
TRUNCATE sales_flat_order_status_history;
TRUNCATE sales_flat_quote;
TRUNCATE sales_flat_quote_address;
TRUNCATE sales_flat_quote_address_item;
TRUNCATE sales_flat_quote_item;
TRUNCATE sales_flat_quote_item_option;
TRUNCATE sales_flat_order_item;
TRUNCATE sendfriend_log;
TRUNCATE tag;
TRUNCATE tag_relation;
TRUNCATE tag_summary;
TRUNCATE wishlist;
TRUNCATE log_quote;
TRUNCATE report_event;
TRUNCATE sales_bestsellers_aggregated_daily;
TRUNCATE sales_bestsellers_aggregated_monthly;
TRUNCATE sales_bestsellers_aggregated_yearly;
TRUNCATE sales_invoiced_aggregated;
TRUNCATE sales_invoiced_aggregated_order;
TRUNCATE sales_order_aggregated_created;
TRUNCATE sales_refunded_aggregated;
TRUNCATE sales_refunded_aggregated_order;

ALTER TABLE sales_flat_order AUTO_INCREMENT=1;
ALTER TABLE sales_flat_order_grid AUTO_INCREMENT=1;
ALTER TABLE sales_flat_order_item AUTO_INCREMENT=1;
ALTER TABLE sales_flat_order_payment AUTO_INCREMENT=1;
ALTER TABLE sales_flat_order_status_history AUTO_INCREMENT=1;
ALTER TABLE sales_flat_quote AUTO_INCREMENT=1;
ALTER TABLE sales_flat_quote_address AUTO_INCREMENT=1;
ALTER TABLE sales_flat_quote_address_item AUTO_INCREMENT=1;
ALTER TABLE sales_flat_quote_item AUTO_INCREMENT=1;
ALTER TABLE sales_flat_quote_item_option AUTO_INCREMENT=1;
ALTER TABLE sales_flat_order_item AUTO_INCREMENT=1;
ALTER TABLE sendfriend_log AUTO_INCREMENT=1;
ALTER TABLE tag AUTO_INCREMENT=1;
ALTER TABLE tag_relation AUTO_INCREMENT=1;
ALTER TABLE tag_summary AUTO_INCREMENT=1;
ALTER TABLE wishlist AUTO_INCREMENT=1;
ALTER TABLE log_quote AUTO_INCREMENT=1;
ALTER TABLE report_event AUTO_INCREMENT=1;
ALTER TABLE sales_bestsellers_aggregated_daily AUTO_INCREMENT=1;
ALTER TABLE sales_bestsellers_aggregated_monthly AUTO_INCREMENT=1;
ALTER TABLE sales_bestsellers_aggregated_yearly AUTO_INCREMENT=1;
ALTER TABLE sales_invoiced_aggregated AUTO_INCREMENT=1;
ALTER TABLE sales_invoiced_aggregated_order AUTO_INCREMENT=1;
ALTER TABLE sales_order_aggregated_created AUTO_INCREMENT=1;
ALTER TABLE sales_refunded_aggregated AUTO_INCREMENT=1;
ALTER TABLE sales_refunded_aggregated_order AUTO_INCREMENT=1;

-- reset customer
TRUNCATE customer_address_entity; 
TRUNCATE customer_address_entity_datetime; 
TRUNCATE customer_address_entity_decimal; 
TRUNCATE customer_address_entity_int; 
TRUNCATE customer_address_entity_text; 
TRUNCATE customer_address_entity_varchar; 
TRUNCATE customer_entity; 
TRUNCATE customer_entity_datetime; 
TRUNCATE customer_entity_decimal; 
TRUNCATE customer_entity_int; 
TRUNCATE customer_entity_text; 
TRUNCATE customer_entity_varchar; 
TRUNCATE log_customer; 
TRUNCATE log_visitor;
TRUNCATE log_visitor_info;
TRUNCATE sponsorship; 
TRUNCATE sponsorship_change;
TRUNCATE sponsorship_fidelity_log;
TRUNCATE sponsorship_openinviter; 
TRUNCATE sponsorship_sponsor_log;
TRUNCATE rewardpoints_account;
TRUNCATE rewardpoints_catalogrules;
TRUNCATE rewardpoints_pointrules;
TRUNCATE rewardpoints_referral;
TRUNCATE rewardpoints_rule;
   
ALTER TABLE customer_address_entity AUTO_INCREMENT=1; 
ALTER TABLE customer_address_entity_datetime AUTO_INCREMENT=1; 
ALTER TABLE customer_address_entity_decimal AUTO_INCREMENT=1; 
ALTER TABLE customer_address_entity_int AUTO_INCREMENT=1; 
ALTER TABLE customer_address_entity_text AUTO_INCREMENT=1; 
ALTER TABLE customer_address_entity_varchar AUTO_INCREMENT=1; 
ALTER TABLE customer_entity AUTO_INCREMENT=1; 
ALTER TABLE customer_entity_datetime AUTO_INCREMENT=1; 
ALTER TABLE customer_entity_decimal AUTO_INCREMENT=1; 
ALTER TABLE customer_entity_int AUTO_INCREMENT=1; 
ALTER TABLE customer_entity_text AUTO_INCREMENT=1; 
ALTER TABLE customer_entity_varchar AUTO_INCREMENT=1; 
ALTER TABLE log_customer AUTO_INCREMENT=1; 
ALTER TABLE log_visitor AUTO_INCREMENT=1;
ALTER TABLE log_visitor_info AUTO_INCREMENT=1;
ALTER TABLE sponsorship AUTO_INCREMENT=1;
ALTER TABLE sponsorship_change AUTO_INCREMENT=1;
ALTER TABLE sponsorship_fidelity_log AUTO_INCREMENT=1;
ALTER TABLE sponsorship_openinviter AUTO_INCREMENT=1;
ALTER TABLE sponsorship_sponsor_log AUTO_INCREMENT=1;
ALTER TABLE rewardpoints_account AUTO_INCREMENT=1;
ALTER TABLE rewardpoints_catalogrules AUTO_INCREMENT=1;
ALTER TABLE rewardpoints_pointrules AUTO_INCREMENT=1;
ALTER TABLE rewardpoints_referral AUTO_INCREMENT=1;
ALTER TABLE rewardpoints_rule AUTO_INCREMENT=1;


-- RESET ALL ID counters
-- TRUNCATE eav_entity_store;
-- ALTER TABLE eav_entity_store AUTO_INCREMENT=1;

SET FOREIGN_KEY_CHECKS=1;

-- 
-- FIN FICHIER
-- 