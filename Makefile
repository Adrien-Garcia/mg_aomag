.PHONY: computec brander computec-lint brander-lint phpcs
.SILENT:help

JETPULP_ENV ?= dev
COMPOSER_ARGS =

ifeq ($(JETPULP_ENV), prod)
COMPOSER_ARGS =-prefer-dist -classmap-authoritative
endif

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m
COLOR_WARNING = \033[31m

## Help
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
        helpMessage = match(lastLine, /^## (.*)/); \
        if (helpMessage) { \
            helpCommand = substr($$1, 0, index($$1, ":")); \
            helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
            printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
        } \
    } \
    { lastLine = $$0 }' $(MAKEFILE_LIST)
# Liste les targets disponibles : mettre une ligne avec un double ## avant chaque taget 
# pour déterminer le titre
	
## Installation
install: vendor computec brander

## Composer install	
vendor: composer.lock
	composer install $(COMPOSER_ARGS)

## Composer update
composer.lock: composer.json
	composer update

## Build gulp theme computec
computec:	
	docker run -v $(shell pwd)/server:/app -e BUILD_PATH=skin/frontend/COMPUTEC/default/gulp -e USER_UID=${USER_UID} -e USER_GROUP=${USER_GROUP} --rm registry.jetpulp.fr:5000/dev/gulp-dev build
	# Run gulp-dev container (it links nodes_modules, faster than npm install) 

## Build gulp theme brander
brander:
	docker run -v $(shell pwd)/server:/app -e BUILD_PATH=skin/frontend/BRANDER/default/gulp -e USER_UID=${USER_UID} -e USER_GROUP=${USER_GROUP} --rm registry.jetpulp.fr:5000/dev/gulp-dev build
	# Run gulp-dev container (it links nodes_modules, faster than npm install) 

## Lint PHPCS
phpcs: vendor
	vendor/bin/phpcs

## Lint gulp theme computec
computec-lint:
	docker run -v $(shell pwd)/server:/app -e BUILD_PATH=skin/frontend/COMPUTEC/default/gulp -e USER_UID=${USER_UID} -e USER_GROUP=${USER_GROUP} registry.jetpulp.fr:5000/dev/gulp-dev lint
	# Run gulp-dev container (it links nodes_modules, faster than npm install)

## Lint gulp theme brander
brander-lint:
	docker run -v $(shell pwd)/server:/app -e BUILD_PATH=skin/frontend/BRANDER/default/gulp -e USER_UID=${USER_UID} -e USER_GROUP=${USER_GROUP} registry.jetpulp.fr:5000/dev/gulp-dev lint
	# Run gulp-dev container (it links nodes_modules, faster than npm install)

