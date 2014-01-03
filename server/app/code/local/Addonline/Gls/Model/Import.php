<?php

class Addonline_Gls_Model_Import {

	const LOG_FILE = 'gls_import.log';

	public $filename;
	public $content;
	public $fileMimeType;
	public $fileCharset;

	public function run() {

		Mage::log('run GLS import', null, self::LOG_FILE);

		if ( !Mage::getStoreConfig('carrier/gls/export')) {
			return;
		}
	}

	public function import(){
		$importFolder = Mage::helper('gls')->getImportFolder();
		$dir = opendir($importFolder);
		$count = 0;

		//Parcour du dossier
		while($file = readdir($dir)) {
			if($file != '.' && $file != '..' && !is_dir($importFolder.$file) && strpos($file,'GlsWinExpe6_') !== FALSE)
			{
				$aOrdersUpdated = array();
				// Parcour du fichier
				if (($handle = fopen($importFolder.DS.$file, "r")) !== FALSE) {
					$row = 0;
					while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
						$num = count($data);
						$row++;
						if($row > 1){
							// On récupère le champ 5 qui contient le numéro de la commande
							$order = Mage::getModel('sales/order')
  										->getCollection()
  										->addAttributeToFilter('increment_id', $data[4])
  										->getFirstItem();

							// On met à jour le trackid avec le champ 18
							if($order && !isset($aOrdersUpdated[$data[4]])){
								$order->setGlsTrackid($data[17]);
								$order->save();
								$aOrdersUpdated[$data[4]] = 1;
								$count++;
								continue;
							}

							if($order && $aOrdersUpdated[$data[4]]){
								$order->setGlsTrackid($order->getGlsTrackid().','.$data[17]);
								$order->save();
							}

						}
					}
					fclose($handle);
					try{
						unlink($importFolder.$file);
					}catch (Exception $e){
						Mage::log("Import : unable to delete file : ".$importFolder.$file, null, self::LOG_FILE);
					}
				}
			}
		}

		closedir($dir);
		return $count;
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