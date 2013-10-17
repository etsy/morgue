#
# make it so
#

PHPUNIT:=phpunit --include-path=phplib --log-junit results.xml
VERSION := $(shell git rev-parse --short HEAD)

# targets

all: deploy_version

deploy_version:
	@echo "Setting MORGUE_VERSION to $(VERSION) in phplib/deploy_version.php..."
	@sed 's/{{ VERSION }}/$(VERSION)/' <phplib/deploy_version.php.in >phplib/deploy_version.php

unittests:
	${PHPUNIT} tests/unit/
