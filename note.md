# FPM cannot bind port

# =======> if facing the permission problem for AutoLoad

# run this command to change permission in the container
- <container-name> in this case step_up_app
```
docker exec -u root -it <container-name> bash
```
- then you should be in the root directory (var/www/html) then give permission to `start.sh` by running this command :

```
chmod 775 start.sh
```
- After giving the permission then run the script with this command : 

```
./start.sh
```
## `🚀refresh or clear caches on the browser the refresh and it done`


# This part is optional if the sheel script doesn't work 


---------------------------------------------------------------------

The error message indicates that Laravel is unable to write to the `storage/logs/laravel.log` file due to insufficient permissions. This is a common issue when running Laravel in a Docker container, as the user inside the Docker container may not have the necessary permissions to write to the `storage` directory.

Here are some steps you can take to resolve this issue:

1. **Change the ownership of the `storage` and `bootstrap/cache` directories**: You can do this by running the following commands in your project directory:

```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
```

2. **Change the permissions of the `storage` and `bootstrap/cache` directories**: You can do this by running the following commands in your project directory:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

3. **Clear the Laravel cache**: Sometimes, Laravel might cache the old permissions, so you need to clear the cache:

```bash
php artisan config:clear
php artisan cache:clear
```

Please replace `$USER` with your username, and `www-data` with the user that your web server runs as.

Remember to run these commands in your Laravel project directory, and make sure to replace `/var/www/html` with the actual path to your Laravel project.

 