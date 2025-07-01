
CONTAINER_EXECUTABLE = podman
ifeq ($(shell which podman),)
	CONTAINER_EXECUTABLE = docker
endif

CONTAINER_IMAGE = docker.io/srcoder/development-php:php81-fpm
CONTAINER_ARGUMENTS = run --rm -it -v $(PWD):/opt --workdir=/opt --user=root
CONTAINER_COMMAND =


.PHONY: all
all:

.PHONY: tests
tests: vendor/autoload.php
	$(MAKE) containerize CONTAINER_COMMAND='vendor/bin/phpunit --configuration=phpunit.xml'

shell:
	$(MAKE) containerize CONTAINER_ARGUMENTS='$(CONTAINER_ARGUMENTS) --entrypoint=/bin/bash' CONTAINER_COMMAND=''

.PHONY: containerize
containerize:
ifneq ($(container),)
	$(CONTAINER_COMMAND)
else
	$(CONTAINER_EXECUTABLE) $(CONTAINER_ARGUMENTS) $(CONTAINER_IMAGE) $(CONTAINER_COMMAND)
endif

vendor/autoload.php:
	$(MAKE) containerize CONTAINER_COMMAND='composer install --prefer-dist -o'

