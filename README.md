
Create project command : `composer create-project laravel/laravel example-app`

After create run this command to generate the key : `php artisan key:generate --ansi`

Migrate Database command : `php artisan migrate`

To use file first need to link storage with this command : `php artisan storage:link`

Laravel command to install AWS-SDK to connect to DynamoDB:  `composer require aws/aws-sdk-php`

Install Laravel Passport or Sanctum using Composer. For Passport, use `composer require laravel/passport`.


To use Tinker `php artisan vendor:publish --provider="Laravel/Tinker/TinkerServiceProvider"`

`php artisan vendor:publish` command, Laravel will provide you with a list of providers that have assets available for publishing.

For Sanctum, use `composer require laravel/sanctum`.
