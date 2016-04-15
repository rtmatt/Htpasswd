# HTPASSWD Generator

## Usage

### Install

``` bash 
composer require rtmatt/htpasswd
```

### Add Service Provider
Add the following to the 'providers' array in `config/app.php`
``` php 
 RTMatt\Htpasswd\Providers\HtpasswdServiceProvider::class,
```
### Run

To generate password guard

``` bash 
php artisan htpasswd:make <username>
```

This will generate your .htpasswd file and .htaccess entries.  Both the username and password will be the name you enter as an argument for the artisan command.

To remove password guard

``` bash 
php artisan htpasswd:delete
```
This will remove your .htpasswd file and remove the auth entries from .htaccess
