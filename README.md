Biochemistry Logistics Tool 2 (BLT2)
====================================

## Summary
This web application is a simple logistics tool to help employees in the receiving and shipping unit of the Department of Biochemistry at the University of Wisconsin - Madison.

BLT2 allows users to submit important information about packages such as tracking number, date received and packing slip images to a database, allow users in the main office to view package information submitted on a given date, and provides a list of packages for the users for when they are out delivering packages to the receivers.

Given the amount of packages delivered daily to the Biochemistry department, this web application serves as a 'middleman', along with the employees in the shipping and receiving unit, between the packages delivered and their receivers. BLT2 also works along side with the Biochemistry Purchasing System (BPS) as it stores the location of packing slips in the database so that employees in the office can verify purchase orders to packages delivered.

## Changes from BLT1
* Symfony2 2.* --> Symfony 3
* One template for each page, all compatible with desktop and mobile devices
* Select2 3.* --> Select2 4.0+
* Backend Rewritten
    * Backend RestFUL API calls to entities (Package, Receiver, Shipper, Vendor)
    * Frontend now has its own controller
* Tests for Backend, Entity and Frontend (PHPUnit)
* Using LESS with Bootstrap breakpoints to help with templates for desktop/mobile devices

## How to install
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
4. Download and install [composer](https://getcomposer.org/download/)
5. Run composer

    ```
    $ php composer.phar install
    ```
6. Add parameters to database when asked
7. Create database using Doctrine

    ```
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:schema:update --force
    ```
8. Allow _www|www to write to upload and var folders

## How to update
1. Navigate to blt2 directory and pull from repository

    ```
    $ git pull
    ```
2. Clear caches and install/dump assets

    ```
    $ php bin/console cache:clear --env=prod --no-debug
    $ php bin/console assets:install --env=prod --no-debug
    $ php bin/console assetic:dump --env=prod  --no-debug
    ```

## For anyone who wants to use some sort of authentication/authorization system
1. Fork project
2. Update security.yml to fit your authentication/authorization needs
3. Create User/UserProvider classes to help facilitate if needed
4. See the deployment/shipping.biochem.wisc.edu branch for an example