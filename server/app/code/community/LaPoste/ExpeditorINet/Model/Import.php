<?php

class LaPoste_ExpeditorINet_Model_Import {

	const LOG_FILE = 'expeditorinet.log';
	
	private $_log = false;
	
	public function run() {

		if ( !Mage::getStoreConfig('expeditorinet/import/automatic')) {
			return;
		}
		
		$directory = Mage::getStoreConfig('expeditorinet/import/directory');
		
		if ( Mage::getStoreConfig('expeditorinet/import/ftp_enabled')) {

			// FTP access parameters
			$ftp_server = Mage::getStoreConfig('expeditorinet/import/ftp_url');
			$ftp_user = Mage::getStoreConfig('expeditorinet/import/ftp_user');
			$ftp_password = Mage::getStoreConfig('expeditorinet/import/ftp_password');
			$ftp_path = Mage::getStoreConfig('expeditorinet/import/ftp_path');
			
			// connect to FTP server (port 21)
			$conn_id = ftp_connect($ftp_server, 21) or Mage::log("Import : Cannot connect to host", null, self::LOG_FILE);
			// send access parameters
			ftp_login($conn_id, $ftp_user, $ftp_password) or Mage::log("Import : Cannot login", null, self::LOG_FILE);
			// turn on passive mode transfers (some servers need this)
			ftp_pasv ($conn_id, true);
			
			// get contents of the current directory
			$contents = ftp_nlist($conn_id, $ftp_path);
			foreach ($contents as $filepath) {
				if ($ftp_path) {
					$filename = str_replace($ftp_path."/", "", $filepath);
				} else {
					$filename = $filepath;
				}
				if (@ftp_chdir($conn_id, $filename)) {
					ftp_chdir($conn_id, "..");//on ne récupère pas les répertoires 
				} else {
					$download = ftp_get($conn_id , $directory . DS . $filename , $filepath , FTP_ASCII);
					Mage::log( (!$download) ? "Import : Cannot download on FTP" : "Import : The file $filepath as been downloaded", null, self::LOG_FILE);
					if ($download) {
						ftp_delete ($conn_id, $filepath);
					}
				}
			}
			ftp_close ($conn_id);
			
		
		}
		
		// parcourir le répertoire d'import
		$dir_handle = opendir($directory) or die("Erreur le repertoire $directory existe pas");
		while (false !== ($filename = @readdir($dir_handle))) {
			// enlever les cas inutile
			if ($filename == "." || $filename == "..") continue;
			if (is_dir($directory . DS . $filename)) continue;

			$this->_log = true;
			$this->importExpeditorInetFile($directory . DS . $filename, Mage::getStoreConfig('expeditorinet/import/default_tracking_title'));

			$this->_moveToArchive($directory , $filename);
				
		}
		closedir($dir_handle);
			
		
	}
	
	protected function addSuccess($message)
	{
		if ($this->_log) {
			Mage::log("Export : ".$message , null, self::LOG_FILE);
		} else {
			Mage::getSingleton('adminhtml/session')->addSuccess($message);
		}
	}

	protected function addError($message)
	{
		if ($this->_log) {
			Mage::log("Export erreur : ".$message , null, self::LOG_FILE);
		} else {
			Mage::getSingleton('adminhtml/session')->addError($message);
		}
	}
	

	
	/**
	 * Importation logic
	 * @param string $fileName
	 * @param string $trackingTitle
	 */
	public function importExpeditorInetFile($fileName, $trackingTitle)
	{
		/**
		 * File handling
		 **/
		ini_set('auto_detect_line_endings', true);
		$csvObject = new Varien_File_Csv();
		$csvData = $csvObject->getData($fileName);
	
		/**
		 * File expected fields
		*/
		$expectedCsvFields  = array(
				0   => Mage::helper('expeditorinet')->__('Order Id'),
				1   => Mage::helper('expeditorinet')->__('Tracking Number')
		);
	
		/**
		 * Get configuration
		*/
		$sendEmail = Mage::helper('expeditorinet')->getConfigurationSendEmail();
		$comment = Mage::helper('expeditorinet')->getConfigurationShippingComment();
		$includeComment = Mage::helper('expeditorinet')->getConfigurationIncludeComment();
	
		/* debug */
		//$this->addSuccess( Mage::helper('expeditorinet')->__('%s - %s - %s - %s', $sendEmail, $comment, $includeComment, $trackingTitle));
	
		/**
		 * $k is line number
		 * $v is line content array
		*/
		foreach ($csvData as $k => $v) {
	
			/**
			 * End of file has more than one empty lines
			 */
			if (count($v) <= 1 && !strlen($v[0])) {
				continue;
			}
	
			/**
			 * Check that the number of fields is not lower than expected
			 */
			if (count($v) < count($expectedCsvFields)) {
				$this->addError( Mage::helper('expeditorinet')->__('Line %s format is invalid and has been ignored', $k));
				continue;
			}
	
			/**
			 * Get fields content
			 */
			$orderId = $v[0];
			$trackingNumber = $v[1];
	
			/* for debug */
			//$this->addSuccess( Mage::helper('expeditorinet')->__('Lecture ligne %s: %s - %s', $k, $orderId, $trackingNumber));
	
			/**
			 * Try to load the order
			 */
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
			if (!$order->getId()) {
				$this->addError( Mage::helper('expeditorinet')->__('Order %s does not exist', $orderId));
				continue;
			}
	
			/**
			 * Try to create a shipment
			 */
			$shipmentId = $this->_createShipment($order, $trackingNumber, $trackingTitle, $sendEmail, $comment, $includeComment);
	
			if ($shipmentId != 0) {
				$this->addSuccess( Mage::helper('expeditorinet')->__('Shipment %s created for order %s, with tracking number %s', $shipmentId, $orderId, $trackingNumber));
			}
			 
		}//foreach
	
	}
	
	/**
	 * Create new shipment for order
	 * Inspired by Mage_Sales_Model_Order_Shipment_Api methods
	 *
	 * @param Mage_Sales_Model_Order $order (it should exist, no control is done into the method)
	 * @param string $trackingNumber
	 * @param string $trackingTitle
	 * @param booleam $email
	 * @param string $comment
	 * @param boolean $includeComment
	 * @return int : shipment real id if creation was ok, else 0
	 */
	private function _createShipment($order, $trackingNumber, $trackingTitle, $email, $comment, $includeComment)
	{
		/**
		 * Check shipment creation availability
		 */
		if (!$order->canShip()) {
			$this->addError( Mage::helper('expeditorinet')->__('Order %s can not be shipped or has already been shipped', $order->getRealOrderId()));
			return 0;
		}
	
		/**
		 * Initialize the Mage_Sales_Model_Order_Shipment object
		 */
		$convertor = Mage::getModel('sales/convert_order');
		$shipment = $convertor->toShipment($order);
	
		/**
		 * Add the items to send
		*/
		foreach ($order->getAllItems() as $orderItem) {
			if (!$orderItem->getQtyToShip()) {
				continue;
			}
			if ($orderItem->getIsVirtual()) {
				continue;
			}
	
			$item = $convertor->itemToShipmentItem($orderItem);
			$qty = $orderItem->getQtyToShip();
			$item->setQty($qty);
	
			$shipment->addItem($item);
		}//foreach
	
		$shipment->register();
	
		/**
		 * Tracking number instanciation
		*/
		$carrierCode = Mage::helper('expeditorinet')->getConfigurationCarrierCode();
		if(!$carrierCode) $carrierCode = 'custom';
	
		$track = Mage::getModel('sales/order_shipment_track')
		->setNumber($trackingNumber)
		->setCarrierCode($carrierCode)
		->setTitle($trackingTitle);
		$shipment->addTrack($track);
	
		/**
		 * Comment handling
		*/
		$shipment->addComment($comment, $email && $includeComment);
	
		/**
		 * Change order status to Processing
		*/
		$shipment->getOrder()->setIsInProcess(true);
	
		/**
		 * If e-mail, set as sent (must be done before shipment object saving)
		*/
		if ($email) {
			$shipment->setEmailSent(true);
		}
	
		try {
			/**
			 * Save the created shipment and the updated order
			 */
			$shipment->save();
			$shipment->getOrder()->save();
	
			/**
			 * Email sending
			*/
			$shipment->sendEmail($email, ($includeComment ? $comment : ''));
		} catch (Mage_Core_Exception $e) {
			$this->addError( Mage::helper('expeditorinet')->__('Shipment creation error for Order %s : %s', $orderId, $e->getMessage()));
			return 0;
		}
	
		/**
		 * Everything was ok : return Shipment real id
		 */
		return $shipment->getIncrementId();
	}
	
	protected function _moveToArchive($path, $filename){
	
		if(file_exists($path.DS."archive") !== TRUE) {
			mkdir($path.DS."archive");
		}
	
		copy  ( $path.DS.$filename  , $path.DS."archive".DS.$filename );
		unlink( $path.DS.$filename );
	
	}
	
}