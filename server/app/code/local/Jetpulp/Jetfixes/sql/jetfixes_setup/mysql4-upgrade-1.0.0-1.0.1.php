<?php
/**
 * There is a bug with some versions of Magento : THe purge of core_email_queue is OK, but not that of core_email_queue_recipients. So, sometimes,
 * core_email_queue_recipients table is overfilled with dirty data. So, we have added a foreign key between core_email_queue and core_email_queue_recipients for put a cascade deletion and
 * correctly clean up core_email_queue_recipients table.
 * For more informations: please visit http://magento.stackexchange.com/questions/53961/new-order-email-being-sent-twice/87299#87299.
 *
 * User: Fabien SERRA
 * Date: 10/12/2016
 * Time: 11:00
 */

$installer = $this;

$installer->startSetup();

// 1. Execute the following two SQL queries to clean the core_email_queue_recipients table from orphan records and repeated messages ids
$installer->run( "
DELETE FROM core_email_queue_recipients WHERE message_id NOT IN ( SELECT message_id FROM core_email_queue );
DELETE FROM core_email_queue_recipients WHERE recipient_id < ( SELECT recipient_id FROM ( SELECT recipient_id FROM core_email_queue_recipients ORDER BY message_id ASC, recipient_id DESC LIMIT 1 ) AS r );
" );

// 2. Create a foreign key on the core_email_queue_recipients table to delete Recipients records on cascade. The SQL query to create this foreign key is:
$installer->run( "
ALTER TABLE core_email_queue_recipients ADD FOREIGN KEY( message_id ) REFERENCES core_email_queue( message_id ) ON DELETE CASCADE;
" );

$installer->endSetup();