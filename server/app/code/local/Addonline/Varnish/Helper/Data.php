<?php
/**
 * Data helper
 *
 * @category    Addonline
 * @package     Addonline_Varnish
 */
class Addonline_Varnish_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 * Check if varnish page cache is enabled 
	 *
	 * @return boolean  True if varnish is enable in Cache management.
	 */
	 public function isEnabled(){
	 	return Mage::helper('pagecache')->isEnabled() && Mage::getStoreConfig(Mage_PageCache_Helper_Data::XML_PATH_EXTERNAL_CACHE_CONTROL) == "varnish_page_cache";
	 }
	
	/**
	 * Return varnish servers from configuration
	 *
	 * @return array
	 */
	public function getVarnishServers()
	{
		$serverConfig = Mage::getStoreConfig('system/external_page_cache/varnish_servers');
		$varnishServers = array();
	
		foreach (explode(',', $serverConfig) as $value ) {
			$varnishServers[] = trim($value);
		}
	
		return $varnishServers;
	}
	
	/**
	 * Purge an array of urls on all varnish servers.
	 *
	 * @param array $urls
	 * @return array with all errors
	 */
	public function purge(array $urls)
	{

		
		$varnishServers = $this->getVarnishServers();
		$errors = array();
	
		// Init curl handler
		$curlHandlers = array(); // keep references for clean up
		$mh = curl_multi_init();
	
		foreach ((array)$varnishServers as $varnishServer) {
			foreach ($urls as $url) {
				$varnishUrl = "http://" . $varnishServer . $url;
				$purgeHost = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);//attention à prendre l'url unsecure car sinon on risque d'avoir le https en cas de BO ssl
				$purgeHost = substr($purgeHost, 0, strlen($purgeHost)-1);
				$purgeHost = substr($purgeHost,strlen( "http://") );
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $varnishUrl);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PURGE');
				curl_setopt($ch, CURLOPT_HTTPHEADER,array("X-Purge-Host:".$purgeHost,"X-Purge-URL-Regex:^".$url));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	
				curl_multi_add_handle($mh, $ch);
				$curlHandlers[] = $ch;
				
				//Mage::log($varnishUrl, null, 'varnish.log');
			}
		}

		do {
			$n = curl_multi_exec($mh, $active);
		} while ($active);

		// Error handling and clean up
		foreach ($curlHandlers as $ch) {
			$info = curl_getinfo($ch);
	
			if (curl_errno($ch)) {
				$errors[] = "Cannot purge url {$info['url']} due to error" . curl_error($ch);
			} else if ($info['http_code'] != 200 && $info['http_code'] != 404) {
				$errors[] = "Cannot purge url {$info['url']}, http code: {$info['http_code']}";
			}
	
			curl_multi_remove_handle($mh, $ch);
			curl_close($ch);
		}
		curl_multi_close($mh);
	
		return $errors;
	}
}