When running commands or tests, please follow these guidelines:

All commands should be run within docker; nothing is installed locally.

PHP is available in the 'app' container.

To ensure the correct state of migrations for tests, run:
`docker-compose exec app php ./tests/bin/yii migrate/fresh --interactive=0`
