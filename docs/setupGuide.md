Setup Guide
===========

## Introduction
BLT2 was built on [Symfony's PHP Framework](http://symfony.com/) and has the [same requirements](http://symfony.com/doc/current/reference/requirements.html) for hosting Symfony. Please make sure the server that you'll be hosting BLT2 meets or exceeds Symfony minimum requirements. 

## *AMP
### Before installing
* Make sure that the web server has met all requirements needed by Symfony
* Enable apache2 mod_rewrite
* Have git and related dependencies installed
* Have php-xml module installed
* Ensure that the MySQL user that BLT2 will be using to insert/select/update is granted the following: SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, LOCK TABLES, INDEX
* Have the database already created in MySQL (tables will be added during installation)

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
8. Add tables to database using Doctrine

    ```
    $ php bin/console doctrine:schema:update --force
    ```
9. Allow _www|www-data to [write](http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup) to upload and var folders

    ```
    rm -rf var/cache/* var/logs/* var/sessions/*
    ```
    ##### For Linux
    ```
    $ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
    $ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX upload var
    $ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX upload var
    ```
    ##### For OS X
    ```
    $ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
    $ sudo chmod -R +a "$HTTPDUSER allow delete,write,append,file_inherit,directory_inherit" upload var
    $ sudo chmod -R +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" upload var
    ```
    
10. Install/dump assets

    ```
    $ php bin/console assets:install --env=prod
    $ php bin/console assetic:dump --env=prod
    ```

### After installation
* Make sure the DocumentRoot in your apache config file points to ```/var/www/html/blt2/web```
* Make sure the .htaccess rewrites are [enabled](http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html)
* With no authentication, going to the root of blt2 in a browser should point you to the main page

## Issues after installation (after running composer)
* If you run into a DataTables issue, chances are the connection between the database and the application is broken --> Go to app/config/parameters.yml and update the settings
* If you run into a blank page, make sure permissions for _www|www-data are correctly set for the var folder
* If you go to a page and it doesn't look like CSS was applied to the page, make sure you installed/dumped assets
    * You may have to clear cache first before installing/dumping assets
    
    ```
    $ php bin/console cache:clear --env=prod
    ```
* If file uploads are not being saved, make sure permissions for _www|www-data are correctly set for the upload folder
* var/logs/{environment}.log (most likely prod) will most likely have logged problems you are experiencing/have experienced