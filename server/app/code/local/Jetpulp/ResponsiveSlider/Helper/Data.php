<?php

class Jetpulp_ResponsiveSlider_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getImageUrl($imageBaseFilename, $width = null) {

        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        if ($width == null) {
            return $url . $imageBaseFilename;
        } else {
            //TODO : générate image in asked width, in media/responsiveslider/width/

            $model = Mage::getModel('responsiveslider/image');
            $model->setWidth($width);
            $model->setBaseFile($imageBaseFilename);
            if(is_null($model->getNewFile()) || !file_exists($model->getNewFile())) {
                $model->setKeepFrame(false);
                $model->resize();
                $model->saveFile();
            }

            return $model->getUrl();
        }


    }

}