<?php 
require 'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
Mage::app()->getStore()->setConfig('dev/log/active', true);

function _log($message) {
	Mage::log($message, null, 'aoclear.log');
}

$autorized_ips = array();
$autorized_ips[] = '127.0.0.1';
$autorized_ips[] = '::1';
$autorized_ips [] = '195.28.202.129'; // IP Addonline 

$remoteIp = @$_SERVER['REMOTE_ADDR'];
if (strpos ($remoteIp, '192.168.') ===0 ) { //si on est derrière un proxy
	$remoteIp = @$_SERVER['HTTP_X_FORWARDED_FOR'];
}

if (in_array($remoteIp, $autorized_ips))
{
	Mage::getSingleton('core/session', array('name'=>'adminhtml'));

	//verify if the user is logged in to the backend
	if(Mage::getSingleton('admin/session')->isLoggedIn()){

		?>
		
		<H1>Nettoyage base magento</H1>

<?php 

$productEntityTypeId = Mage::getModel('catalog/product')
->getResource()
->getEntityType()
->getId(); //product entity type

if (isset($_GET['deleteAttributeSetId']) && ($attributeSetId = $_GET['deleteAttributeSetId'])) {
	
	$attributeSet = Mage::getModel('eav/entity_attribute_set')->load($attributeSetId);

	if (isset($_GET['deleteProducts'])) {
		$products = Mage::getModel('catalog/product')->getCollection();
		$products->addAttributeToFilter('attribute_set_id', $attributeSetId);
		foreach ($products as $product) {
			$product->delete();
			echo "Product ".$product->getSku()." supprim&eacute;<br/>";
		}
	}
	
	//Supprimer les attributs qui ne sont utilisés nulle part ailleurs que dans cet attribute set 
	$resource = Mage::getSingleton('core/resource');
	$readConnection = $resource->getConnection('core_read');
	$eavEntitytable  = $resource->getTableName('eav_entity_attribute');

	if (isset($_GET['deleteUnusedAttributes'])) {
		$query = 'SELECT * from ' .$resource->getTableName('eav_entity_attribute');
		$query .= ' JOIN '.$resource->getTableName('eav_attribute').' USING (attribute_id) ';
		$query .= ' WHERE attribute_set_id='.$attributeSet->getId().' AND is_user_defined=1 AND eav_attribute.entity_type_id='.$productEntityTypeId;
		$query .= ' AND NOT EXISTS (SELECT * FROM '.$resource->getTableName('eav_entity_attribute').' as eat ';
		$query .= ' WHERE eat.attribute_id=eav_entity_attribute.attribute_id AND attribute_set_id!='.$attributeSet->getId().' )';
		//echo $query;
		
		$results = $readConnection->fetchAll($query);	
	}
	
	
	//$attributeSet->delete();
	echo "Attribute Set ".$attributeSet->getAttributeSetName()." supprim&eacute;<br/>";

	if (isset($_GET['deleteUnusedAttributes'])) {
		foreach ($results as $row) {
			$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($row['attribute_id']);
			//$attribute->delete();
			echo "Attribut ".$attribute->getAttributeCode()." supprim&eacute;<br/>";
		}
	}
	
} 
	
	$attributeSetColl = Mage::getModel('eav/entity_attribute_set')->getCollection()
		->addFieldToFilter('entity_type_id', $productEntityTypeId)

	?>
	<h2>Donn&eacute;es produits :</h2> 
	<form>
	<label>Attribute Set : <select name="deleteAttributeSetId" >
	<option value="" >S&eacute;lectionnez un attibute Set</option>
	<?php foreach ($attributeSetColl as $attributeSet) :?>
	<option value="<?php echo $attributeSet->getId();?>" ><?php echo $attributeSet->getAttributeSetName();?></option>
	<?php endforeach;?>
	</select></label><br>
	<label>Supprimer ses produits associ&eacute;s : <input type="checkbox" name="deleteProducts" checked="checked" /></label><br>
	<label>Supprimer ses attributs non utilis&eacute;s par ailleurs : <input type="checkbox" name="deleteUnusedAttributes" checked="checked" /></label><br>
	<button type="submit">Supprimer l'attribute Set</button>
	</form>

	<h2>Donn&eacute;es Clients :</h2> 
		
		<form><input type="hidden" name="deleteOrders" value="all"><button type="submit">Supprimer les commandes</button></form>
		
		<form><input type="hidden" name="deleteCustomers" value="all"><button type="submit">Supprimer les clients</button></form>

		<form><input type="hidden" name="deleteNotifys" value="all"><button type="submit">Supprimer les notifications, termes de recherche, logs, rapports, etc ...</button></form>

	<h2>Supprimer ce script apr&egrave;s utilisation !</h2> 
<?php 
	if (isset($_GET['deleteOrders']) && ("all" == $_GET['deleteOrders'])) {
		/** Suppression de toutes les commandes **/

		$setup = Mage::getResourceModel('core/setup','core_write');
		$setup->startSetup();
		
		$scriptSql = "SET FOREIGN_KEY_CHECKS=0;
		TRUNCATE {$setup->getTable('sales_flat_order')};
		TRUNCATE {$setup->getTable('sales_flat_order_address')};
		TRUNCATE {$setup->getTable('sales_flat_order_grid')};
		TRUNCATE {$setup->getTable('sales_flat_order_item')};
		TRUNCATE {$setup->getTable('sales_flat_order_payment')};
		TRUNCATE {$setup->getTable('sales_flat_order_status_history')};
		TRUNCATE {$setup->getTable('sales_flat_quote')};
		TRUNCATE {$setup->getTable('sales_flat_quote_address')};
		TRUNCATE {$setup->getTable('sales_flat_quote_address_item')};
		TRUNCATE {$setup->getTable('sales_flat_quote_item')};
		TRUNCATE {$setup->getTable('sales_flat_quote_item_option')};
		TRUNCATE {$setup->getTable('sales_flat_invoice')};
		TRUNCATE {$setup->getTable('sales_flat_invoice_grid')};
		TRUNCATE {$setup->getTable('sales_flat_invoice_item')};
		TRUNCATE {$setup->getTable('sales_flat_invoice_comment')};
		TRUNCATE {$setup->getTable('sales_flat_shipment')};
		TRUNCATE {$setup->getTable('sales_flat_shipment_comment')};
		TRUNCATE {$setup->getTable('sales_flat_shipment_grid')};
		TRUNCATE {$setup->getTable('sales_flat_shipment_item')};
		TRUNCATE {$setup->getTable('sales_flat_shipment_track')};
		TRUNCATE {$setup->getTable('sales_flat_creditmemo')};
		TRUNCATE {$setup->getTable('sales_flat_creditmemo_comment')};
		TRUNCATE {$setup->getTable('sales_flat_creditmemo_grid')};
		TRUNCATE {$setup->getTable('sales_flat_creditmemo_item')};
		TRUNCATE {$setup->getTable('sendfriend_log')};
		TRUNCATE {$setup->getTable('tag')};
		TRUNCATE {$setup->getTable('tag_relation')};
		TRUNCATE {$setup->getTable('tag_summary')};
		TRUNCATE {$setup->getTable('wishlist')};
		TRUNCATE {$setup->getTable('log_quote')};
		TRUNCATE {$setup->getTable('report_event')};
		TRUNCATE {$setup->getTable('sales_bestsellers_aggregated_daily')};
		TRUNCATE {$setup->getTable('sales_bestsellers_aggregated_monthly')};
		TRUNCATE {$setup->getTable('sales_bestsellers_aggregated_yearly')};
		TRUNCATE {$setup->getTable('sales_invoiced_aggregated')};
		TRUNCATE {$setup->getTable('sales_invoiced_aggregated_order')};
		TRUNCATE {$setup->getTable('sales_order_aggregated_created')};
		TRUNCATE {$setup->getTable('sales_refunded_aggregated')};
		TRUNCATE {$setup->getTable('sales_refunded_aggregated_order')};
		
		ALTER TABLE {$setup->getTable('sales_flat_order')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_order_address')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_order_grid')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_order_item')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_order_payment')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_order_status_history')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_quote')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_quote_address')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_quote_address_item')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_quote_item')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_quote_item_option')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_invoice')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_invoice_grid')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_invoice_item')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_invoice_comment')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_shipment')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_shipment_comment')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_shipment_grid')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_shipment_item')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_shipment_track')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_creditmemo')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_creditmemo_comment')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_creditmemo_grid')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_flat_creditmemo_item')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sendfriend_log')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('tag')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('tag_relation')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('tag_summary')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('wishlist')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('log_quote')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('report_event')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_bestsellers_aggregated_daily')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_bestsellers_aggregated_monthly')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_bestsellers_aggregated_yearly')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_invoiced_aggregated')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_invoiced_aggregated_order')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_order_aggregated_created')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_refunded_aggregated')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('sales_refunded_aggregated_order')} AUTO_INCREMENT=1;
		SET FOREIGN_KEY_CHECKS=1;";
		
		echo $scriptSql;
		
		$setup->run($scriptSql);
		$setup->endSetup();
				
	}

		if (isset($_GET['deleteCustomers']) && ("all" == $_GET['deleteCustomers'])) {
		/** Suppression de tous les clients **/
		$setup = Mage::getResourceModel('core/setup','core_write');
		$setup->startSetup();
	
		$scriptSql = "SET FOREIGN_KEY_CHECKS=0;
		TRUNCATE {$setup->getTable('customer_address_entity')};
		TRUNCATE {$setup->getTable('customer_address_entity_datetime')};
		TRUNCATE {$setup->getTable('customer_address_entity_decimal')};
		TRUNCATE {$setup->getTable('customer_address_entity_int')};
		TRUNCATE {$setup->getTable('customer_address_entity_text')};
		TRUNCATE {$setup->getTable('customer_address_entity_varchar')};
		TRUNCATE {$setup->getTable('customer_entity')};
		TRUNCATE {$setup->getTable('customer_entity_datetime')};
		TRUNCATE {$setup->getTable('customer_entity_decimal')};
		TRUNCATE {$setup->getTable('customer_entity_int')};
		TRUNCATE {$setup->getTable('customer_entity_text')};
		TRUNCATE {$setup->getTable('customer_entity_varchar')};
		TRUNCATE {$setup->getTable('log_customer')};
		TRUNCATE {$setup->getTable('log_visitor')};
		TRUNCATE {$setup->getTable('log_visitor_info')};
		TRUNCATE {$setup->getTable('log_visitor_online')};
				 
		ALTER TABLE {$setup->getTable('customer_address_entity')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_address_entity_datetime')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_address_entity_decimal')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_address_entity_int')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_address_entity_text')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_address_entity_varchar')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_entity')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_entity_datetime')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_entity_decimal')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_entity_int')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_entity_text')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('customer_entity_varchar')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('log_customer')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('log_visitor')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('log_visitor_online')} AUTO_INCREMENT=1;
		SET FOREIGN_KEY_CHECKS=1;";
	
		echo $scriptSql;

		$setup->run($scriptSql);
		$setup->endSetup();

	}

	if (isset($_GET['deleteNotifys']) && ("all" == $_GET['deleteNotifys'])) {
		/** Suppression de tous les clients **/
		$setup = Mage::getResourceModel('core/setup','core_write');
		$setup->startSetup();

		$scriptSql = "SET FOREIGN_KEY_CHECKS=0;
		TRUNCATE {$setup->getTable('adminnotification_inbox')}; 
		TRUNCATE {$setup->getTable('atos_log_request')}; 
		TRUNCATE {$setup->getTable('atos_log_response')}; 
		TRUNCATE {$setup->getTable('captcha_log')}; 
		TRUNCATE {$setup->getTable('dataflow_batch')}; 
		TRUNCATE {$setup->getTable('dataflow_batch_export')}; 
		TRUNCATE {$setup->getTable('dataflow_batch_import')}; 
		TRUNCATE {$setup->getTable('dataflow_import_data')}; 
		TRUNCATE {$setup->getTable('dataflow_profile_history')}; 
		TRUNCATE {$setup->getTable('dataflow_session')}; 
		TRUNCATE {$setup->getTable('catalogsearch_fulltext')}; 
		TRUNCATE {$setup->getTable('catalogsearch_query')}; 
		TRUNCATE {$setup->getTable('catalogsearch_result')}; 
		
		ALTER TABLE {$setup->getTable('adminnotification_inbox')} AUTO_INCREMENT=1; 
		ALTER TABLE {$setup->getTable('atos_log_request')} AUTO_INCREMENT=1; 
		ALTER TABLE {$setup->getTable('atos_log_response')} AUTO_INCREMENT=1; 
		ALTER TABLE {$setup->getTable('dataflow_batch')} AUTO_INCREMENT=1; 
		ALTER TABLE {$setup->getTable('dataflow_batch_export')} AUTO_INCREMENT=1;
		ALTER TABLE {$setup->getTable('dataflow_batch_import')} AUTO_INCREMENT=1; 		
		ALTER TABLE {$setup->getTable('dataflow_import_data')} AUTO_INCREMENT=1; 
		ALTER TABLE {$setup->getTable('dataflow_profile_history')} AUTO_INCREMENT=1; 
		ALTER TABLE {$setup->getTable('catalogsearch_fulltext')} AUTO_INCREMENT=1; 
		ALTER TABLE {$setup->getTable('catalogsearch_query')} AUTO_INCREMENT=1; 
				
		SET FOREIGN_KEY_CHECKS=1;";

		echo $scriptSql;

		$setup->run($scriptSql);
		$setup->endSetup();

	}

	
	}
	else
	{
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: ".str_replace($_SERVER['PHP_SELF'], "", Mage::getBaseUrl())."aoadmin"); 
	}
}
else
{
	_log("Tentative non autorisée ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
	header("HTTP/1.0 404 Not Found");
}

