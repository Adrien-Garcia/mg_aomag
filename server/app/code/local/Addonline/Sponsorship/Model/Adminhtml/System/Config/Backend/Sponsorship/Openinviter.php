<?php
/**
 * @category   Addonline
 * @package    Addonline_Sponsorship
 * @author     Addonline
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Addonline_Sponsorship_Model_Adminhtml_System_Config_Backend_Sponsorship_Openinviter extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
    	$data = $this->getData();
    	$username = $data['groups']['open_inviter']['fields']['username']['value'];
    	$private_key = $data['groups']['open_inviter']['fields']['private_key']['value'];
		$configFilePath = Mage::getModuleDir('', 'Addonline_Sponsorship').'/Lib/OpenInviter/config.php';
		$configFile = fopen($configFilePath, 'w');
		$stringData =
'<?php

$openinviter_settings=array(
"username"=>"'.$username.'",
"private_key"=>"'.$private_key.'",
"cookie_path"=>"/tmp",
"transport"=>"curl",
"local_debug"=>"on_error",
"remote_debug"=>"",
"hosted"=>"",
"proxies"=>array(),
"stats"=>"",
"plugins_cache_time"=>"1800",
"plugins_cache_file"=>"oi_plugins.php",
"update_files"=>true,
"stats_user"=>"",
"stats_password"=>"");
?>';
		fwrite($configFile, $stringData);
		fclose($configFile);			
        return $this;
    }
}