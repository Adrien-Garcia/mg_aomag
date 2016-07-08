#!/bin/sh

echo "Composer install "
composer install

echo "Add magento coding standard to PHPCS path"
./vendor/bin/phpcs --config-set installed_paths ../../magento-ecg/coding-standard
