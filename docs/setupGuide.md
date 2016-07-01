Setup Guide
===========

## Introduction
BLT2 was built on [Symfony's PHP Framework](http://symfony.com/) and has the [same requirements](http://symfony.com/doc/current/reference/requirements.html) for hosting Symfony. Please make sure the server that you'll be hosting BLT2 meets or exceeds Symfony minimum requirements. 

The same instructions for setting up BLT2 are located in the README.md

## *AMP
### Before installing
* Make sure that the web server has met all requirements needed by Symfony
* Enable apache2 mod_rewrite
* Make sure to have git installed
* Have php-xml module installed
* Ensure that the MySQL user that BLT2 is using to insert/select/update is granted the following: SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, LOCK TABLES, INDEX
* Have the database already created in MySQL (tables will be updated with schema update)

### How to install
1. Navigate to web root directory

    ```
    $ cd /var/www/html
    ```
2. Clone the repository into its own folder

    ```
    $ git clone https://github.com/charlielor/blt2.git
    ```
3. Navigate into the folder

    ```
    $ cd blt2
    ```
4. Set the Symfony environment to prod

    ```
    export SYMFONY_ENV=prod
    ```
5. Download and install [composer](https://getcomposer.org/download/)
6. Run composer

    ```
    $ php composer.phar install --no-dev --optimize-autoloader
    ```
7. Add parameters for database when asked
8. Update database using Doctrine

    ```
    $ php bin/console doctrine:schema:update --force
    ```
9. Allow _www|www to [write](http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup) to upload and var folders
10. Install/dump assets

    ```
    $ php bin/console assets:install --env=prod
    $ php bin/console assetic:dump --env=prod
    ```

### After installation
* Make sure the DocumentRoot in your apache config file points to ```/var/www/html/blt2/web```
* Make sure the .htaccess rewrites are [enabled](http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html)

## Issues after installation (running composer)
* With no authentication, going to the root of blt2 in a browser(blt2/web if apache config is configured correctly) should point you to the main page
* If you run into a DataTables issue, chances are the connection between the database and the application is broken (improper settings, 