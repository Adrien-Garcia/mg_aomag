<?php

class LaPoste_ExpeditorINet_Model_Export {
	
	const LOG_FILE = 'expeditorinet.log';
	
	public function run() {

		if ( !Mage::getStoreConfig('expeditorinet/export/automatic')) {
			return;
		}
		
		$collection = Mage::getResourceModel('sales/order_collection');
		//on exclue les commandes expédiées, annulées et bloquées
		$collection->addAttributeToFilter('state', array('nin'=>array('complete', 'canceled', 'holded')) );
		//on exclue les commandes déjà exportées vers expeditor
		$collection->addAttributeToFilter('expeditorinet', 0);
		//Mage::log($collection->getSelect()->__toString(), null, self::LOG_FILE);

		if ($collection->getSize()>0) {
			
			$this->export($collection);

			$this->_write();
		
			foreach ($collection as $order) {
				$order->setExpeditorinet(true);
				$order->save();
			}
			
		} else {
			//Mage::log("Export : ".Mage::helper('expeditorinet')->__('No Order has been selected'), null, self::LOG_FILE);
		}
		
		
	}
	
	public $filename;
	public $content;
	public $fileMimeType;
	public $fileCharset;
	
	public function export($collection) {
		
		/**
		 * Get configuration
		 */
		$separator = Mage::helper('expeditorinet')->getConfigurationFieldSeparator();
		$delimiter = Mage::helper('expeditorinet')->getConfigurationFieldDelimiter();
		if ($delimiter == 'simple_quote') {
			$delimiter = "'";
		} else if ($delimiter == 'double_quotes') {
			$delimiter = '"';
		}
		$lineBreak = Mage::helper('expeditorinet')->getConfigurationEndOfLineCharacter();
		if ($lineBreak == 'lf') {
			$lineBreak = "\n";
		} else if ($lineBreak == 'cr') {
			$lineBreak = "\r";
		} else if ($lineBreak == 'crlf') {
			$lineBreak = "\r\n";
		}
		$fileExtension = Mage::helper('expeditorinet')->getConfigurationFileExtension();
		$this->fileCharset = Mage::helper('expeditorinet')->getConfigurationFileCharset();
		
		/* So Colissimo product codes for Hors Domicile , ajout Belgique */
		$hd_productcodes = array (
				'BPR',
				'ACP',
				'CIT',
				'A2P',
				'MRL',
				'CDI',
				'BDP',
				'CMT'
		);
		
		/* set the filename */
		$this->filename   = 'orders_export_'.Mage::getSingleton('core/date')->date('Ymd_His').$fileExtension;
		
		/* get company commercial name */
		$commercialName = Mage::helper('expeditorinet')->getCompanyCommercialName();
		
		/* initialize the content variable */
		$this->content = '';
		
		foreach ($collection as $order) {
	
			//if the product code is for Hors Domicile we should take the billing address
			if (in_array($order->getSocoProductCode(), $hd_productcodes)) {
				/* get the shipping address */
				$address = $order->getBillingAddress();
			} else {
				/* get the billing address */
				$address = $order->getShippingAddress();
			}
			/* real order id */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $order->getRealOrderId());
			$this->content .= $separator;
			/* customer first name */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getFirstname());
			$this->content .= $separator;
			/* customer last name */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getLastname());
			$this->content .= $separator;
			/* customer company */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getCompany());
			$this->content .= $separator;
			/* street address, on 4 fields */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getStreet(1));
			$this->content .= $separator;
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getStreet(2));
			$this->content .= $separator;
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getStreet(3));
			$this->content .= $separator;
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getStreet(4));
			$this->content .= $separator;
			/* postal code */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getPostcode());
			$this->content .= $separator;
			/* city */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getCity());
			$this->content .= $separator;
			/* country code */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $address->getCountry());
			$this->content .= $separator;
			/* telephone */
			$telephone = '';
			if ($order->getSocoPhoneNumber() != '' && $order->getSocoPhoneNumber() != null) {
				$telephone = $order->getSocoPhoneNumber();
			} elseif ($address->getTelephone() != '' && $address->getTelephone() != null) {
				$telephone = $address->getTelephone();
			}
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $telephone);
			$this->content .= $separator;
			/* code produit */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $order->getSocoProductCode());
			$this->content .= $separator;
			/* instruction de livraison */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $order->getSocoShippingInstruction());
			$this->content .= $separator;
			/* civilite */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $this->_getExpeditorCodeForCivility($order->getSocoCivility()));
			$this->content .= $separator;
			/* code porte 1 */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $order->getSocoDoorCode1());
			$this->content .= $separator;
			/* code porte 2 */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $order->getSocoDoorCode2());
			$this->content .= $separator;
			/* Interphone */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $order->getSocoInterphone());
			$this->content .= $separator;
			/* Code point retrait */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $order->getSocoRelayPointCode());
			$this->content .= $separator;
			/* E-mail de suivi socolissimo */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $order->getSocoEmail());
			$this->content .= $separator;
	
			/* total weight */
			$total_weight = 0;
			$items = $order->getAllItems();
			foreach ($items as $item) {
				$total_weight += $item['row_weight'];
			}
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $total_weight);
			$this->content .= $separator;
	
			/* company commercial name */
			$this->content = $this->_addFieldToCsv($this->content, $delimiter, $commercialName);
	
			$this->content .= $lineBreak;
		}
		
		/* decode the content, depending on the charset */
		if ($this->fileCharset == 'ISO-8859-1') {
			$this->content = utf8_decode($this->content);
		}
		

		/* pick file mime type, depending on the extension */
		if ($fileExtension == '.txt') {
			$this->fileMimeType = 'text/plain';
		} else if ($fileExtension == '.csv') {
			$this->fileMimeType = 'application/csv';
		} else {
			// default
			$this->fileMimeType = 'text/plain';
		}
			
	}
	
	/**
	 * Add a new field to the csv file
	 * @param csvContent : the current csv content
	 * @param fieldDelimiter : the delimiter character
	 * @param fieldContent : the content to add
	 * @return : the concatenation of current content and content to add
	 */
	private function _addFieldToCsv($csvContent, $fieldDelimiter, $fieldContent) {
		return $csvContent . $fieldDelimiter . $fieldContent . $fieldDelimiter;
	}
	
	/**
	 * convert civlity in letters to a code for Expeditor
	 * @param civility : string
	 */
	private function _getExpeditorCodeForCivility($civility)
	{
		if (strtolower($civility) == 'm.') {
			return 2;
		} elseif (strtolower($civility) == 'mme') {
			return 3;
		} elseif (strtolower($civility) == 'mlle') {
			return 4;
		} else {
			return 1;
		}
	}
	
	private function _write() {

		
		$path = Mage::getStoreConfig('expeditorinet/export/directory');
		if (!file_exists($path)) {
			mkdir ($path);
		}

		file_put_contents ($path . DS . $this->filename, $this->content);
		
		// Envoi du fichier sur le serveur FTP d'export
		if (Mage::getStoreConfig('expeditorinet/export/ftp_enabled')) {
			// FTP access parameters
			$ftp_server = Mage::getStoreConfig('expeditorinet/export/ftp_url');
			$ftp_user = Mage::getStoreConfig('expeditorinet/export/ftp_user');
			$ftp_password = Mage::getStoreConfig('expeditorinet/export/ftp_password');
			$ftp_path = Mage::getStoreConfig('expeditorinet/export/ftp_path');
		
			// connect to FTP server (port 21)
			$conn_id = ftp_connect($ftp_server, 21) or Mage::log("Export : Cannot connect to host", null, self::LOG_FILE);
			// send access parameters
			ftp_login($conn_id, $ftp_user, $ftp_password) or Mage::log("Export : Cannot login", null, self::LOG_FILE);
			// turn on passive mode transfers (some servers need this)
			ftp_pasv ($conn_id, true);

			$upload = ftp_put($conn_id, $ftp_path."/".$this->filename, $path . DS . $this->filename, FTP_ASCII);				

			Mage::log( (!$upload) ? "Export : Cannot upload on FTP" : "Export : The file $this->filename as been uploaded", null, self::LOG_FILE);
			ftp_close($conn_id);
			
		}
		
		
	}
	
}