<?php

class Addonline_Gls_Model_Export {

	const LOG_FILE = 'gls_export.log';

	public $filename;
	public $content;
	public $fileMimeType;
	public $fileCharset;

	public function run() {
		Mage::log('run GLS export', null, self::LOG_FILE);
		if ( !Mage::getStoreConfig('carrier/gls/export')) {
			return;
		}
	}

	public function export($collection){

		if ($collection->getSize()>0) {

			/*
			 * Csv export configuration
			 */
			$delimiter =';';
			$encloser = '"';
			$this->filename = 'GlsCmd_'.$this->udate('YmdHisu').'.csv';

			/*
			 * Get the export Folder
			 */
			$exportFolder = Mage::helper('gls')->getExportFolder();

			/*
			 * Populate orders array
			 */
			$aOrdersToExport = array();

				// HEADERS of the file
				$aheaders = array('ORDERID','ORDERNAME','PRODUCTNO','ORDERWEIGHTTOT','CONSID','CONTACTMAIL','CONTACTMOBILE','CONTACTPHONE','STREET1','STREET2','STREET3','COUNTRYCODE','CITY','ZIPCODE','REFPR');
				$aOrdersToExport[] = $aheaders;

				// Parsing of the orders
				foreach ($collection as $order) {
					$aRow = array();

					// Getting the addresses of the order
					$billingAddress = $order->getBillingAddress();
					$shippingAddress = $order->getShippingAddress();

					// ORDERID
					$aRow[] = $order->getIncrementId();

					// ORDERNAME
					$aRow[] = strtoupper($shippingAddress->getFirstname().' '.$shippingAddress->getLastname());

					// PRODUCTNO
					$shipping_method = $order->getShippingMethod();
					$shipping_code = $shipping_method;
					if (strpos($shipping_method, 'ls_tohome') > 0) {
						//$shipping_code = 'BP';
						$shipping_code = ''; // le bon code sera déterminé par winExpé, selon le pays de destination
					}
					if (strpos($shipping_method, 'ls_toyou') > 0) {
						$shipping_code = 'ADO';
					}
					if (strpos($shipping_method, 'ls_relay') > 0) {
						$shipping_code = 'SHD';
					}
					$aRow[] = $shipping_code;

					// ORDERWEIGHTTOT
					$total_weight = 0;
					$items = $order->getAllItems();
					foreach ($items as $item) {
						$total_weight += $item['row_weight'];
					}
					$aRow[] = $total_weight;

					// CONSID
					$aRow[] = $order->getCustomerId();

					// CONTACTMAIL
					$aRow[] = $shippingAddress->getEmail();

					// CONTACTMOBILE
					$aRow[] = $order->getGlsWarnByPhone()?$shippingAddress->getTelephone():'';

					// CONTACTPHONE
					$aRow[] = $shippingAddress->getTelephone();

					// STREET1
					$aRow[] = strtoupper($shippingAddress->getStreet(1));

					// STREET2
					$aRow[] = strtoupper($shippingAddress->getStreet(2));

					// STREET3
					$aRow[] = strtoupper($shippingAddress->getStreet(3));

					// COUNTRYCODE
					$aRow[] = strtoupper($shippingAddress->getCountry());

					// CITY
					$aRow[] = strtoupper($shippingAddress->getCity());

					// ZIPCODE
					$aRow[] = strtoupper($shippingAddress->getPostcode());

					// REFPR (identifiant du point relais)
					$aRow[] = $order->getGlsRelayPointId();

					// Adding the order to the export array
					$aOrdersToExport[] = $aRow;
				}

			/*
			 * Save the file
			 */
			$this->array2csv($aOrdersToExport, $this->filename,$delimiter,$encloser,$exportFolder);

		} else {
			Mage::log("Export : ".Mage::helper('gls')->__('No Order has been selected'), null, self::LOG_FILE);
		}


	}

	private function udate($format = 'u', $utimestamp = null) {
		if (is_null($utimestamp))
			$utimestamp = microtime(true);

		$timestamp = floor($utimestamp);
		$milliseconds = round(($utimestamp - $timestamp) * 1000000);
		$milliseconds = substr($milliseconds,0,2);
		return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
	}

	private function array2csv(array &$array,$filename,$delimiter = ';',$encloser = '"',$folder ='var/export/gls/')
	{
		if (count($array) == 0) {
			return null;
		}

		if (!file_exists($folder) and !is_dir($folder)) {
			mkdir($folder);
		}

		ob_start();
		$df = fopen($folder.$filename, 'w+');
		foreach ($array as $row) {
			fputcsv($df, $row,$delimiter,$encloser);
		}
		fclose($df);
		return ob_get_clean();
	}

}