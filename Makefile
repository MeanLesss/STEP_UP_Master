build:
    docker-compose build  --no-cache --force-rm
stop:
    docker-compose stop
up:
    docker-compose up -d
bash:
    docker exec -u root -it step_up_app bash
composer-install:
    docker exec step_up_app composer install
composer-update:
    docker exec step_up_app composer update
up2:
    docker run --add-host localnode:$(ifconfig en0 | grep inet | grep -v inet6 | awk '{print \$2}')
