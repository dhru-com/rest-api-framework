.PHONY: help tests
.DEFAULT_GOAL := help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-12s\033[0m %s\n", $$1, $$2}'

tests: ## Execute test suite and create code coverage report
	./scripts/phpunit

update: ## Update composer packages
	./scripts/composer update

bootstrap: ## Install composer
	sh ./bin/install-composer.sh

lint: ## Lint all the code
	./scripts/phpcs --standard=PSR2 --encoding=utf-8 -p src

doc: ## Generate Postman documentation
	php ./bin/postman.php

endpoint: ## Create new Endpoint
	sh ./bin/create-endpoint.sh
