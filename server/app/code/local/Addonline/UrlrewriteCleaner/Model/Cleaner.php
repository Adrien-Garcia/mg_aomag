<?php


/**
 * Purge Model
 *
 * @category   Purge
 * @package    Addonline_PurgeUrlrewrite
 * @author     spras@addonline.fr
 */
class Addonline_UrlrewriteCleaner_Model_Cleaner
{
    const XML_PURGE_CLEAN_DAYS    = 'catalog/seo/clean_urlrewrite_day';

    /**
     * Clean Urlrewrite
     *
     */
    public function clean()
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $tableName = $resource->getTableName('core_url_rewrite');

        $allStores = Mage::app()->getStores();
        foreach ($allStores as $_eachStoreId => $val) {
            $cleanDays = Mage::getStoreConfig(self::XML_PURGE_CLEAN_DAYS, $_eachStoreId);
    
            $limitTimestamp = time() - 24*60*60*$cleanDays;
            
            $startTime = time();
            $stmt = $writeConnection->query("DELETE FROM `$tableName` WHERE store_id=$_eachStoreId AND is_system=0 AND SUBSTRING_INDEX(id_path, '_', -1) < $limitTimestamp");
            $count = $stmt->rowCount();
            $duration = time() - $startTime;
            Mage::log('[URLREWRITECLEANER] Cleaning old urlrewrite on store '.$_eachStoreId.' (duration: '.$duration.', row count: '.$count.')');
        }
    }
}
