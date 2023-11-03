
Create project command : `composer create-project laravel/laravel example-app`

After create run this command to generate the key : `php artisan key:generate --ansi`

Migrate Database command : `php artisan migrate`

To use file first need to link storage with this command : `php artisan storage:link`

Laravel command to install AWS-SDK to connect to DynamoDB:  `composer require aws/aws-sdk-php`

Install Laravel Passport or Sanctum using Composer. For Passport, use `composer require laravel/passport`.


To use Tinker `php artisan vendor:publish --provider="Laravel/Tinker/TinkerServiceProvider"`

`php artisan vendor:publish` command, Laravel will provide you with a list of providers that have assets available for publishing.

For Sanctum, use `composer require laravel/sanctum`.

# To Detach screen 
```
In Linux, you can use the `screen` command to run different processes in separate sessions and detach from them as needed. Here's how you can do it:

1. **Start a new screen session**: You can start a new screen session by simply typing `screen` at the command prompt⁴.

2. **Run your process**: After starting a screen session, you can run your desired process in the terminal.

3. **Detach from the screen session**: To detach from the screen session and keep it running in the background, you can use the key combination `Ctrl + a` followed by `d`¹⁴⁵. This will allow your process to continue running even if you close the terminal.

4. **Reattach to a screen session**: If you want to reattach to a detached screen session, you can use the command `screen -r`. If there are multiple detached sessions, you'll need to specify the session ID¹.

Remember, when you run a process in a screen session, even if you close the terminal or get disconnected, the process will continue to run in the background. This is particularly useful for long-running processes or when running processes over an unstable network connection.

Source: Conversation with Bing, 10/27/2023
(1) How To Detach From A Screen Session On Centos Linux. https://www.systranbox.com/how-to-detach-from-a-screen-session-on-centos-linux/.
(2) How do I detach a screen session from a terminal?. https://askubuntu.com/questions/124897/how-do-i-detach-a-screen-session-from-a-terminal.
(3) How do I detach a screen in Linux? – Mexico-insights.com. https://www.mexico-insights.com/how-do-i-detach-a-screen-in-linux/.
(4) How to Use Screen in Linux [List, Attach, Detach, Close] - GoLinuxCloud. https://www.golinuxcloud.com/command-screen-linux/.
(5) How To Detach a Screen Session in Linux | DeviceTests. https://devicetests.com/detach-screen-session-linux.
```


 
# Docker Compose
```
Sure, here's a step-by-step guide on how to use Docker Compose to set up a Laravel project:

1. **Install Docker and Docker Compose**: First, you need to install Docker and Docker Compose on your machine. You can find the installation instructions on the official Docker and Docker Compose websites.

2. **Clone Your Laravel Project**: Clone your Laravel project from your Git repository to your local machine.

3. **Create a Dockerfile**: In the root directory of your Laravel project, create a file named `Dockerfile`. This file will define how to build a Docker image for your Laravel application. Here's an example of what your Dockerfile might look like:
```Dockerfile
FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --fix-missing \
    libonig-dev \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install exif
RUN docker-php-ext-install pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY . /var/www/html

#COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www:www . /var/www/html

USER root
# RUN chown -R www:www /var/www/html
USER www

EXPOSE 9000

CMD ["php-fpm","composer update"]

```
This Dockerfile starts from the `php:7.4-fpm` image, installs some dependencies, and copies your Laravel project files into the image.

4. **Create a docker-compose.yml File**: In the same directory as your Dockerfile, create a `docker-compose.yml` file. This file will define services for your Laravel application and any other services it needs (like MySQL or Redis). Here's an example:
```yaml
version: '3'
services:
  step_up_app:
    build:
      context: .
      dockerfile: Dockerfile
    image: step_up_app
    container_name: step_up_app
    restart: unless-stopped
    tty: true
    environment:
      SERVICE_NAME: app
      SERVICE_TAGS: dev
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
    networks:
      - laravel

  nginx:
    image: nginx:latest
    container_name: step_up_nginx
    ports:
      - "168:80"
    volumes:
      - ./:/usr/share/nginx/html  # Changed this line
      - ./nginx/conf.d/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - laravel

  php:
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - ./:/var/www/html
    networks:
      - laravel

  laravel:
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - ./:/var/www/html
    networks:
      - laravel
networks:
  laravel:
    driver: bridge

```
This `docker-compose.yml` file defines two services, `app` for the Laravel application and `webserver` for Nginx. It also defines a network for these two services to communicate and a volume to persist data.

5. **Build and Run Your Docker Containers**: Finally, you can use the `docker-compose up` command to start your services:
```bash
docker-compose up -d --build
```
The `-d` flag tells Docker to run the containers in detached mode (in the background), and the `--build` flag tells Docker to build the images before starting the containers.

Now, you should be able to access your Laravel application by navigating to `http://localhost` in your web browser.

To update your Laravel project in a Docker container, you can use a combination of `git pull` to fetch the latest changes from your repository and `docker-compose up --build -d` to rebuild your Docker containers. This will ensure that your containers are always running with the latest version of your Laravel project.

Remember, when you run a process in a Docker container, even if you close the terminal or get disconnected, the process will continue to run in the background. This is particularly useful for long-running processes or when running processes over an unstable network connection.
 
```

# Install portainer agent to remote 
```
Please note that these commands should be run in your terminal. user’ how to install portain agent on my cloud server assistant’ To install the Portainer Agent on your cloud server, you can follow these steps:

Pull the Portainer Agent Image: First, pull the latest Portainer Agent image from the Docker Hub using this command1:
docker pull portainer/agent

Run the Portainer Agent Container: Next, run a new Docker container using the Portainer Agent image1. This command will also start the Portainer Agent:
```
docker run -d -p 9001:9001 --name=portainer_agent --restart=always -v /var/run/docker.sock:/var/run/docker.sock -v /var/lib/docker/volumes:/var/lib/docker/volumes portainer/agent
```
## return :
```
2f2286daf8ac19f1ec6e12a29389325dc6cf5cb2e8e2de03b125c211c555693f
```


Here’s what this command does1:

-d: This option tells Docker to run the container in detached mode, so it runs in the background.
-p 9001:9001: This option tells Docker to map port 9001 of the container to port 9001 of the host.
--name=portainer_agent: This option gives the container a name for easier reference.
--restart=always: This option ensures that the Portainer Agent container always restarts if it stops for any reason.
-v /var/run/docker.sock:/var/run/docker.sock: This option mounts the Docker socket from the host into the container. This allows the Portainer Agent to communicate with the Docker API.
-v /var/lib/docker/volumes:/var/lib/docker/volumes: This option mounts the Docker volumes directory from the host into the container. This allows the Portainer Agent to access your Docker volumes1.
```
```


# The end to fix the package need to run this 
```
docker exec step_up_app composer update
--->>> step_up_app the name defined in the docker-compose file it might be other name
```

# if the project laravel is changed  and to update the docker :
```
docker-compose up --build -d
-------then-------
docker exec -it <container_name> php artisan route:clear

```


# GitHub CI/CD to manage work flow
Setting up a CI/CD service with GitHub Actions involves several steps¹²³:

1. **Create a GitHub Actions Workflow File**: In your GitHub repository, create a new file in the `.github/workflows` directory. This file will define your CI/CD pipeline¹. You can name it something like `ci.yml`.

2. **Define the Workflow**: In the workflow file, you'll define the steps that should be taken when the workflow is triggered¹. This typically includes steps to set up the environment, install dependencies, run tests, and deploy your application¹.

Here's an example of what a basic Laravel CI/CD workflow file might look like:

```yaml
name: Laravel CI

on:
  push:
    branches: [ master ]

jobs:
  tests:
    runs-on: ubuntu-latest

    steps:
    - uses: actions/checkout@v2

    - name: Copy .env
      run: php -r "file_exists('.env') || copy('.env.example', '.env');"

    - name: Install Dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

    - name: Generate key
      run: php artisan key:generate

    - name: Directory Permissions
      run: chmod -R 777 storage bootstrap/cache

    - name: Run Tests
      run: vendor/bin/phpunit
```

This workflow is triggered whenever you push to the `master` branch. It sets up an Ubuntu environment, checks out your code, installs dependencies, generates an app key, sets directory permissions, and runs your tests¹.

1. **Commit and Push Your Workflow File**: Once you've defined your workflow, commit the workflow file and push it to your GitHub repository³. GitHub Actions will automatically recognize the workflow file and start running it whenever it's triggered³.

2. **Monitor Your Workflow**: You can monitor the status of your workflows in the "Actions" tab of your GitHub repository³. Here you'll see a list of all workflow runs, and you can click on a run to see more details about it³.

Remember that this is just a basic example. Depending on your application, you might need to add more steps to your workflow, such as compiling assets or running database migrations¹²³.
 
