#!/bin/sh

echo "Copy pre-commit"
cp contrib/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit

echo "Copy pre-push"
cp contrib/pre-push .git/hooks/pre-push
chmod +x .git/hooks/pre-push

echo "Add magento coding standard to PHPCS path"
./vendor/bin/phpcs --config-set installed_paths ../../magento-ecg/coding-standard
