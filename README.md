# `This part is for fresh start up only`
Create project command : `composer create-project laravel/laravel example-app`


After create run this command to generate the key : `php artisan key:generate --ansi`
# `This part is after cloning the project`
```
create the .env file first by copy the .env.example to .env and config your DB
```

## `then change the user permission and group please refer the` [note.md](note.md) `in the root directory`
 
## Create DB on docker
```
docker run --name step_up -e MYSQL_ROOT_PASSWORD=#Thepassword -p 33063:3306 -d mysql:latest
```


Migrate Database command (Optional if want to use the server db instead of local) : `php artisan migrate`

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


```

 
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
`HTTPS Version`
```
docker run -d -p 9001:9001 --name=portainer_agent --restart=unless-stopped -v /var/run/docker.sock:/var/run/docker.sock -v /var/lib/docker/volumes:/var/lib/docker/volumes portainer/agent
```
`HTTP Version`
```
docker run -d \
    -p 9001:9001 \
    --name=portainer_agent \
    --restart=unless-stopped \
    -v /var/run/docker.sock:/var/run/docker.sock \
    -v /var/lib/docker/volumes:/var/lib/docker/volumes -e AGENT_PORT=9001 -e AGENT_SECURE=false portainer/agent
```
 

Here’s what this command does1:

-d: This option tells Docker to run the container in detached mode, so it runs in the background.
-p 9001:9001: This option tells Docker to map port 9001 of the container to port 9001 of the host.
--name=portainer_agent: This option gives the container a name for easier reference.
--restart=always: This option ensures that the Portainer Agent container always restarts if it stops for any reason.
-v /var/run/docker.sock:/var/run/docker.sock: This option mounts the Docker socket from the host into the container. This allows the Portainer Agent to communicate with the Docker API.
-v /var/lib/docker/volumes:/var/lib/docker/volumes: This option mounts the Docker volumes directory from the host into the container. This allows the Portainer Agent to access your Docker volumes1.
```
 