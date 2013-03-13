<?php

class WebMods_Solrsearch_Helper_Data extends Mage_Core_Helper_Abstract
{
	const QUERY_VAR_NAME = 'q';
	const FILTER_QUERY_VAR_NAME = 'fq';
	public function getQueryParamName()
    {
        return self::QUERY_VAR_NAME;
    }
	public function getFilterQueryParamName()
    {
        return self::FILTER_QUERY_VAR_NAME;
    }
	public function getResultUrl($query = null,$filterQuery = null)
    {
        return $this->_getUrl('solrsearch', array(
            '_query' => array(self::QUERY_VAR_NAME => $query, self::FILTER_QUERY_VAR_NAME=>$filterQuery),
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
        ));
    }
	/**
     * Returns the resized Image URL
     *
     * @param string $imgUrl - This is relative to the the media folder (custom/module/images/example.jpg)
     * @param int $x Width
     * @param int $y Height
     */
    public function getResizedUrl($imgUrl,$productId,$x,$y=NULL){
        $imgPath=$this->splitImageValue($imgUrl,"path");
        $imgName=$this->splitImageValue($imgUrl,"name");
 
        /**
         * Path with Directory Seperator
         */
        $imgPath=str_replace("/",DS,$imgPath);
 
        /**
         * Absolute full path of Image
         */
        $imgPathFull=Mage::getBaseDir("media").DS.$imgPath.DS.$imgName;
 		return $imgPathFull;
        /**
         * If Y is not set set it to as X
         */
        $widht=$x;
        $y?$height=$y:$height=$x;
 
        /**
         * Resize folder is widthXheight
         */
        $resizeFolder=$widht."X".$height;
 
        /**
         * Image resized path will then be
         */
        $imageResizedPath=Mage::getBaseDir("media").DS.'solrbridge'.DS.'thumb'.DS.$productId.'.jpg';
	    if (!file_exists($imageResizedPath) && file_exists($imgPathFull)){
			$imageObj = new Varien_Image($imgPathFull);
			$imageObj->constrainOnly(TRUE);
			$imageObj->keepAspectRatio(TRUE);
			$imageObj->resize($widht,$height);
			$imageObj->save($imageResizedPath);
		}
 
        /**
         * Else image is in cache replace the Image Path with / for http path.
         */
        $imgUrl=str_replace(DS,"/",$imgPath);
 
        /**
         * Return full http path of the image
         */
        return Mage::getBaseUrl("media").$imgUrl."/".$resizeFolder."/".$imgName;
    }
 
    /**
     * Splits images Path and Name
     *
     * Path=custom/module/images/
     * Name=example.jpg
     *
     * @param string $imageValue
     * @param string $attr
     * @return string
     */
    public function splitImageValue($imageValue,$attr="name"){
        $imArray=explode("/",$imageValue);
 
        $name=$imArray[count($imArray)-1];
        $path=implode("/",array_diff($imArray,array($name)));
        if($attr=="path"){
            return $path;
        }
        else
            return $name;
 
    }
    
	function GetImageFromUrl($link) {
		$ch = curl_init();
		 
		curl_setopt($ch, CURLOPT_POST, 0);
		 
		curl_setopt($ch,CURLOPT_URL,$link);
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 
		$result=curl_exec($ch);
		 
		curl_close($ch);
		 
		return $result;	 
	}
}