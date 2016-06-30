Setup Guide
===========

## Introduction
BLT2 was built on [Symfony's PHP Framework](http://symfony.com/) and has the [same requirements](http://symfony.com/doc/current/reference/requirements.html) for hosting Symfony. Please make sure the server that you'll be hosting BLT2 meets or exceeds Symfony minimum requirements. 

The same instructions for setting up BLT2 are located in the README.md

## How to install (LAMP)
* By default, there's no authentication system; everyone is assumed to be anon.
* Server must meet [Symfony 3 requirements](http://symfony.com/doc/current/reference/requirements.html)
* Must have git installed for git clone

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
4. Add parameters to database when asked
5. Create database using Doctrine

    ```
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:schema:update --force
    ```
6. Allow _www|www to [write](http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup) to upload and var folders
7. Set the Symfony environment to prod

    ```
    SYMFONY_ENV=prod
    ```
8. Download and install [composer](https://getcomposer.org/download/)
9. Run composer

    ```
    $ php composer.phar install --no-dev --optimize-autoloader
    ```

## Tips before/after installation
### Before
* Make sure that the web server has met all requirements needed by Symfony
* Have php-xml module installed
* Ensure that the MySQL user that BLT2 is using to insert/select/update is granted the following: SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, LOCK TABLES, INDEX 

### After
* Make sure the DocumentRoot in your apache config file points to ```/var/www/html/blt2/web```
* Make sure the .htaccess rewrites are [enabled](http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html)
* 

## Issues after installation (running composer)
* With no authentication, going to the root of blt2 (blt2/web if apache config is configured correctly) should point you to the main page
* If you run into a DataTables issue, chances are the connection between the database and the application is broken (improper settings, 