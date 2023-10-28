build:
    docker-compose build  --no-cache --force-rm
stop:
    docker-compose stop
up:
    docker-compose up -d
composer-install:
    docker exec step_up_app bash -c "composer install"
composer-update:
    docker exec step_up_app bash -c "composer update"
