
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


# To Create docker Nginx

```
To install Nginx on Docker, you can follow these steps:

1. **Pull the Nginx Docker Image**: You can download Nginx from a pre-built Docker image, with a default Nginx configuration, by running the following command²:
```bash
docker pull nginx
```
This command downloads all the necessary components for the container².

2. **Run the Nginx Docker Container**: To start your Nginx Docker container, run this command²:
```bash
docker run --name docker-nginx -p 80:80 nginx
```
Here's a quick rundown of what's happening with this command²:
- `run` is the command to create a new container.
- The `--name` flag is how you specify the name of the container.
- `-p 80:80` maps port 80 of the container to port 80 of the host machine.
- `nginx` is the name of the image to use to create the container.

3. **Verify Installation**: You can verify that Nginx is running by opening your web browser and visiting `http://localhost`. You should see a welcome page for Nginx, which confirms that Nginx is running correctly².

Remember, when you run a process in a Docker container, even if you close the terminal or get disconnected, the process will continue to run in the background. This is particularly useful for long-running processes or when running processes over an unstable network connection.

Please note that these commands should be run in your project's root directory where your `docker-compose.yml` file is located. Also, make sure to replace `"docker-nginx"` with the actual container name if it's different.

 
```
# Host on docker nginx 
```
To clone a Laravel project into an Nginx Docker container, you can follow these steps:

1. **Pull the Nginx Docker Image**: You can download Nginx from a pre-built Docker image, with a default Nginx configuration, by running the following command¹:
```bash
docker pull nginx
```

2. **Clone the Project**: Clone your project from GitHub or any other version control system. For example¹:
```bash
git clone https://github.com/yourusername/your-repo.git
```
Replace `"https://github.com/yourusername/your-repo.git"` with the actual URL of your repository.

3. **Create a Dockerfile**: In the root directory of your project, create a Dockerfile³. Here's an example of what your Dockerfile might look like for an Angular application³:
```Dockerfile
# Stage 1: Compile and Build angular codebase
# Use official node image as the base image
FROM node:latest as build

# Set the working directory
WORKDIR /usr/local/app

# Add the source code to app
COPY ./ /usr/local/app/

# Install all the dependencies
RUN npm install

# Generate the build of the application
RUN npm run build

# Stage 2: Serve app with nginx server
# Use official nginx image as the base image
FROM nginx:latest

# Copy the build output to replace the default nginx contents.
COPY --from=build /usr/local/app/dist /usr/share/nginx/html
```
This Dockerfile first builds your Angular application and then copies the build output (the `dist` directory) to replace the default Nginx contents³.

4. **Build Your Docker Image**: You can now build your Docker image using the `docker build` command¹:
```bash
docker build -t my-nginx-image .
```
Replace `"my-nginx-image"` with whatever name you want to give your Docker image¹.

5. **Run Your Docker Container**: Finally, you can run your Docker container using the `docker run` command¹:
```bash
docker run --name my-nginx-container -p 80:80 -d my-nginx-image
```
Replace `"my-nginx-container"` with whatever name you want to give your Docker container, and replace `"my-nginx-image"` with the name of your Docker image¹.

Now, you should be able to access your project by navigating to `http://localhost` in your web browser¹.

To update your Laravel project in a Docker container, you can use a combination of `git pull` to fetch the latest changes from your repository and `docker-compose up --build -d` to rebuild your Docker containers¹³. This will ensure that your containers are always running with the latest version of your Laravel project.

Remember, when you run a process in a Docker container, even if you close the terminal or get disconnected, the process will continue to run in the background. This is particularly useful for long-running processes or when running processes over an unstable network connection.

 
```

# Docker Compose
```
Sure, here's a step-by-step guide on how to use Docker Compose to set up a Laravel project:

1. **Install Docker and Docker Compose**: First, you need to install Docker and Docker Compose on your machine. You can find the installation instructions on the official Docker and Docker Compose websites.

2. **Clone Your Laravel Project**: Clone your Laravel project from your Git repository to your local machine.

3. **Create a Dockerfile**: In the root directory of your Laravel project, create a file named `Dockerfile`. This file will define how to build a Docker image for your Laravel application. Here's an example of what your Dockerfile might look like:
```Dockerfile
FROM php:7.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    libonig-dev \
    zip \
    jpegoptim optipng pngquant gifsicle

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/
RUN docker-php-ext-install gd

WORKDIR /var/www

RUN rm -rf /var/www/html
COPY . /var/www
RUN chown -R www-data:www-data /var/www
```
This Dockerfile starts from the `php:7.4-fpm` image, installs some dependencies, and copies your Laravel project files into the image.

4. **Create a docker-compose.yml File**: In the same directory as your Dockerfile, create a `docker-compose.yml` file. This file will define services for your Laravel application and any other services it needs (like MySQL or Redis). Here's an example:
```yaml
version: '3'
services:

  #PHP Service
  app:
    build:
      context: .
      dockerfile: Dockerfile
    image: digitalocean.com/php
    container_name: app
    restart: unless-stopped
    tty: true
    environment:
      SERVICE_NAME: app
      SERVICE_TAGS: dev
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - app-network

  #Nginx Service
  webserver:
    image: nginx:alpine
    container_name: webserver
    restart: unless-stopped
    tty: true
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./nginx/conf.d/:/etc/nginx/conf.d/
    networks:
      - app-network

#Docker Networks
networks:
  app-network:
    driver: bridge

#Volumes
volumes:
  dbdata:
    driver: local
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
