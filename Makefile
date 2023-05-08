##################
# Variables
##################

DOCKER_COMPOSE = docker-compose -f ./deployment/docker-compose.yml --env-file ./deployment/.env
DOCKER_COMPOSE_PHP_FPM_EXEC = ${DOCKER_COMPOSE} exec -u www-data php-8.1-blablaarticle

##################
# Docker compose
##################

dc_build:
	${DOCKER_COMPOSE} build

dc_start:
	${DOCKER_COMPOSE} start

dc_stop:
	${DOCKER_COMPOSE} stop

dc_up:
	${DOCKER_COMPOSE} up -d --remove-orphans

dc_ps:
	${DOCKER_COMPOSE} ps

dc_logs:
	${DOCKER_COMPOSE} logs -f

dc_down:
	${DOCKER_COMPOSE} down -v --rmi=all --remove-orphans

dc_restart:
	make dc_stop dc_start



##################
# App
##################

app_bash:
	${DOCKER_COMPOSE} exec -u www-data php-8.1-blablaarticle bash
php:
	${DOCKER_COMPOSE} exec -u www-data php-8.1-blablaarticle bash
test:
	${DOCKER_COMPOSE} exec -u www-data php-8.1-blablaarticle bin/phpunit
jwt:
	${DOCKER_COMPOSE} exec -u www-data php-8.1-blablaarticle bin/console lexik:jwt:generate-keypair
cache:
	docker-compose -f ./docker/docker-compose.yml exec -u www-data php-fpm bin/console cache:clear
	docker-compose -f ./docker/docker-compose.yml exec -u www-data php-fpm bin/console cache:clear --env=test
