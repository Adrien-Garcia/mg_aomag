<?php 
require 'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
Mage::app()->getStore()->setConfig('dev/log/active', true);

function _log($message, $echo=false) {
	Mage::log($message, null, 'beanstalk.log');
	if ($echo) {
		if (is_array($message)) {
			foreach ($message as $line) {
				echo $line."<br/>";
			}
		} else {
			echo $message."<br/>";
		} 
	}
}


/*
 *  Vérification de l'adresse IP appelante 
 */
$autorized_ips = array();
$autorized_ips[] = '127.0.0.1';
$autorized_ips[] = '::1';
$autorized_ips [] = '195.28.202.129'; // IP Addonline 
for ($i=48; $i<=79; $i++) {
	$autorized_ips [] = '50.31.156.'.$i; // IPs Beanstalk (1ère plage)
}
for ($i=108; $i<=122; $i++) {
	$autorized_ips [] = '50.31.189.'.$i; // IPs Beanstalk (2eme plage)
}
$remoteIp = @$_SERVER['REMOTE_ADDR'];
if (strpos ($remoteIp, '192.168.') ===0 || $remoteIp=='127.0.0.1') { //si on est derrière un proxy
	$remoteIp = @$_SERVER['HTTP_X_FORWARDED_FOR'];
}
if (count(explode(', ', $remoteIp))>1) { //si on est derrière un proxy qui ajoute , 127.0.0.1 
    $remoteIp = explode(', ', $remoteIp)[0];
}
if (!in_array($remoteIp, $autorized_ips))
{
	_log("Tentative non autorisée ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
	die("TOP SECRET");
}


/*
 *  Traitement web hook
 */	
if (isset ($_GET['hook'])) {
	$hook = $_GET['hook'];
	
	if ($hook =='pre' ) {
		
		/*
		 *  AVANT le déploiement
  		 */	
		_log("Pre-Deployment depuis ".$remoteIp);
		
		//On lève le flag maintenance
		touch (dirname(__FILE__).DS.'maintenance.flag');

		_log("Pre-Deployment OK", true);
			
	} else if ($hook =='post' ) {

		/*
		 *  APRES le déploiement
  		 */	
		_log("Post-Deployment depuis ".$remoteIp);

		//on vide le cache APC
		if(function_exists("apc_clear_cache")) {
			apc_clear_cache('user');
			apc_clear_cache('opcode');
		}
		//On vide le cache d'opcode
		if(function_exists('opcache_reset')) {
			opcache_reset();
		}

		//Vider le cache magento
		Mage::dispatchEvent('adminhtml_cache_flush_all');
		Mage::app()->getCacheInstance()->flush();

		//Par acquis de conscience on vide le cache backend File
		$cacheDir = dirname(__FILE__).DS.'var'.DS.'cache';
		emptyDir($cacheDir, false);

		//Vide le cache du module AOE ClassPathCache
		if (Mage::helper('aoe_classpathcache')) {
			Mage::helper('aoe_classpathcache')->clearClassPathCache();
		}

		//Vider le cache CSS/JS
		Mage::getModel('core/design_package')->cleanMergedJsCss();
		Mage::dispatchEvent('clean_media_cache_after');

		if ($varnish = Mage::getModel('varnish/control_varnish')) {
			//Vider le cache page varnish
			$varnish->clean();
		}

		/*
		 *  Compilation 
		 *   - des css par grunt à faire et déployer manuellement
		 *  (en attendant mieux :-( )
  		 */	

		//On baisse le flag maintenance
		unlink (dirname(__FILE__).DS.'maintenance.flag');
		
		_log("Post-Deployment OK", true);
				
	} else {
		_log("Tentative non conforme ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
		die("TOP SECRET");
	}	
} else {
	_log("Tentative non conforme ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
	die("TOP SECRET");
	
}



function emptyDir($cacheDir, $deleteMe) {
	
	if(!$dh = @opendir($cacheDir)) return;
	while (false !== ($obj = readdir($dh))) {
		if($obj=='.' || $obj=='..') continue;
		if($obj=='.gitignore' || $obj=='readme.txt') continue;
		if (!@unlink($cacheDir.'/'.$obj)) emptyDir($cacheDir.'/'.$obj, true); 
	}
	closedir($dh);
	if ($deleteMe){
		@rmdir($cacheDir);
	}
}