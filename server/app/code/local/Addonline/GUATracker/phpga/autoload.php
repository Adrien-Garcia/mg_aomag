<?php

function advancedAnalysticLoader($className) {	
	if(strpos($className, 'UnitedPrototype\GoogleAnalytics') !== 0){
	    return;
	}
	
	$classPath = Mage::getModuleDir('', 'Addonline_GUATracker') . DS .'phpga'.DS.str_replace('\\',DS,$className).'.php';
		
	if(file_exists($classPath)) {
	    echo '<br/> require : '.$classPath;
		include_once($classPath);
	}
}

spl_autoload_register('advancedAnalysticLoader');
?>
