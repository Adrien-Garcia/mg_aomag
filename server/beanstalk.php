<?php 
require 'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
Mage::app()->getStore()->setConfig('dev/log/active', true);

function _log($message) {
	Mage::log($message, null, 'beanstalk.log');
}

$autorized_ips = array();
$autorized_ips[] = '127.0.0.1';
$autorized_ips[] = '::1';
$autorized_ips [] = '195.28.202.129'; // IP Addonline 
for ($i=48; $i<=79; $i++) {
	$autorized_ips [] = '50.31.156.'.$i; // IPs Beanstalk
}
for ($i=108; $i<=122; $i++) {
	$autorized_ips [] = '50.31.189.'.$i; // IPs Beanstalk
}
$remoteIp = @$_SERVER['REMOTE_ADDR'];
if (strpos ($remoteIp, '192.168.') ===0 || $remoteIp=='127.0.0.1') { //si on est derrière un proxy
	$remoteIp = @$_SERVER['HTTP_X_FORWARDED_FOR'];
}

if (in_array($remoteIp, $autorized_ips))
{
	if (isset ($_GET['hook'])) {
		$hook = $_GET['hook'];
		
		if ($hook =='pre' ) {
			
			_log("Pre-Deployment depuis ".$remoteIp);
			
			//On lève le flag maintenance
			touch (dirname(__FILE__).DS.'maintenance.flag');

			echo "Pre-Deployment OK";
				
		} else if ($hook =='post' ) {
	
			_log("Post-Deployment depuis ".$remoteIp);
	
			//on vide le cache APC
			apc_clear_cache('user');
			apc_clear_cache('opcode');
			//on vide le cache File
			$cacheDir = dirname(__FILE__).DS.'var'.DS.'cache';
			emptyDir($cacheDir, false);
			
			//On baisse le flag maintenance
			unlink (dirname(__FILE__).DS.'maintenance.flag');
			
			echo "Post-Deployment OK";
					
		} else {
			_log("Tentative non conforme ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
			die("TOP SECRET");
		}	
	} else {
		_log("Tentative non conforme ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
		die("TOP SECRET");
		
	}

}
else
{
	_log("Tentative non autorisée ".@$_SERVER['REQUEST_URI']." depuis ".$remoteIp);
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