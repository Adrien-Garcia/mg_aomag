<?php

class Centerax_DeveloperTools_Model_Log
{

	/*
	 * Méthode modifié par addonline pour pouvoir explorer récursivement les répertoires dans var/log 
	 */
	public function fetchFiles($initLogDir = null)
	{
		$logFiles = array();

		if ($initLogDir == null) {
			$logDir = $this->_getLogDir();
			$initLogDir = "";
		} else {
			$logDir = $initLogDir;
			$initLogDir = str_replace($this->_getLogDir(), "", $initLogDir).DS;
		}
		
		foreach (new DirectoryIterator($logDir) as $fileInfo) {
		    if($fileInfo->isDot()){
		    	continue;
		    }

			if (is_dir($fileInfo->getPathname())) {
				$logFiles = array_merge($logFiles ,$this->fetchFiles($fileInfo->getPathname()));
			}
			
			if(preg_match('/.*(\.log)|(\.logs)$/', $fileInfo->getFilename())){
				$logFiles [] = array('file' => $fileInfo->getPathname(), 'filename'=>$initLogDir.$fileInfo->getFilename());
			}
		}

		return $logFiles;
	}

	protected function _getLogDir()
	{
		return Mage::getBaseDir('var').DS.'log'.DS;
	}
}