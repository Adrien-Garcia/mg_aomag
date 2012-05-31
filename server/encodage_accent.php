<?php

//Permet de tester le code sans créer tout le module
require 'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app()->setCurrentStore(1);
set_time_limit(0);

//Script à lancer lors d'un import de produits fait depuis PrestaShop,
//permet de remplacer tous les caractères, souvent des accents, par leur 
//équivalent 

//Peut durer quelques temps si le nombre de produits est conséquent

function remplaceAccent($phrase){
	$remplacements = array('Ã¡'=> 'á','Ã�'=> 'Á','Ã¢'=> 'â','Ã‚'=> 'â','Ã '=> 'à',
			'Ã€'=> 'À','Ã¥'=> 'å','Ã…'=> 'Å;','Ã£'=> 'ã','Ãƒ'=> 'Ã',
			'Ã¤'=> 'ä','Ã„'=> 'Ä','Ã¦'=> 'æ','Ã†'=> 'Æ','Ã§'=> 'ç',
			'Ã‡'=> 'Ç','Ã©'=> 'é','Ã‰'=> 'É','Ãª'=> 'ê','ÃŠ'=> 'Ê',
			'Ã¨'=> 'è','Ãˆ'=> 'È','Ã«'=> 'ë','Ã‹'=> 'Ë','Ã­'=> 'í',
			'Ã�'=> 'Í','Ã®'=> 'î','ÃŽ'=> 'Î','Ã¬'=> 'ì','ÃŒ'=> 'Ì',
			'Ã¯'=> 'ï','Ã�'=> 'Ï','Ã±'=> 'ñ',
			'Ã‘'=> 'Ñ',
			'Ã³'=> 'ó',
			'Ã“'=> 'Ó',
			'Ã´'=> 'ô',
			'Ã”'=> 'Ô',
			'Ã²'=> 'ò',
			'Ã’'=> 'Ò',
			'Ã¸'=> 'ø',
			'Ã˜'=> 'Ø',
			'Ãµ'=> 'õ',
			'Ã•'=> 'Õ',
			'Ã¶'=> 'ö',
			'Ã–'=> 'Ö',
			'Å“'=> 'œ',
			'Å’'=> 'Œ',
			'Å¡'=> '&scaron;',
			'Å '=> '&Scaron;',
			'ÃŸ'=> '&szlig;',
			'Ã°'=> 'ð',
			'Ã�'=> 'Ð',
			'Ã¾'=> 'þ',
			'Ãž'=> 'Þ',
			'Ãº'=> 'ú',
			'Ãš'=> 'Ú',
			'Ã»'=> 'û',
			'Ã›'=> 'Û',
			'Ã¹'=> 'ù',
			'Ã™'=> 'Ù',
			'Ã¼'=> 'ü',
			'Ãœ'=> 'Ü',
			'Ã½'=> 'ý',
			'Ã�'=> 'Ý',
			'Ã¿'=> 'ÿ',
			'Â°'=> '°',
			'â€™'=> '’',
			'â€¦'=> ' &hellip;',
			'Â«'=> '«',
			'Â»'=> '»',
			'â€¹'=> '‹',
			'â€º'=> '›',
			'â€œ'=> '“',
			'â€�'=> '”',
			'Â´'=> 'á',
			'Ã™'=> 'Ù',
			'Ã¼'=> 'ü',
			'Ãœ'=> 'Ü',
			'Ã½'=> 'ý',
			'Ã�'=> 'Ý',
			'Ã¿'=> 'ÿ',
			'Â°'=> '°',
			'â€™'=> '&rsquo;',
			'â€¦'=> '&hellip;',
			'Â¶'=> ' ',
			'Å¸'=> '¨Y'
	);
	return strtr($phrase, $remplacements);
    }

    //Permet de passer dans l'admin pour mettre à jour les produits
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	//Charge tous les produits
	$productCollection = Mage::getModel('catalog/product')->getCollection()->load();
	
	foreach($productCollection as $product) {
		//On récupère chaque produit
		$_product = Mage::getModel('catalog/product')->load($product->getEntityId());
		//On prend le nom
		$name = $_product->getName();
		//On le fait passer dans la fonction du dessus
		$new_name = remplaceAccent($name);
		//On assigne le nouveau nom au produit
		$_product->setName($new_name);
		//On enregistre le produit avec le nouveau nom
		$_product->save();
	}
	



