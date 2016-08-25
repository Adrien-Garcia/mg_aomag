#!/bin/sh

##
#
#  This script is called by composer post-install-cmd
#  (Composer install phpcs and magento-ecg coding standard, and this script add magento-ecg to phpcs path)
#
##
echo "Add magento coding standard to PHPCS path"
./vendor/bin/phpcs --config-set installed_paths ../../magento-ecg/coding-standard
