<?php

class Centerax_DeveloperTools_Model_Report
{

	public function fetchFiles()
	{
		$reports = array();

		foreach (new DirectoryIterator($this->_getReportDir()) as $fileInfo) {
		    if($fileInfo->isDot()){
		    	continue;
		    }

			$reports [] = array('file' => $fileInfo->getPathname(), 'filename'=>$fileInfo->getFilename());
		}

		return $reports;
	}

	protected function _getReportDir()
	{
		return Mage::getBaseDir('var').DS.'report'.DS;
	}
}