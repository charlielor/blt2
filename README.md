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
1) Navigate to www root
2) Clone branch into folder

    ```
    $ git clone -b deployment/shipping.biochem.wisc.edu https://github.com/charlielor/blt2.git shipping.biochem.wisc.edu/
    ```
3) Download composer --> https://getcomposer.org/download/
4) Install Symfony and related project files/libraries

    ```
    $ php composer.phar install (make sure php is version 5.6.* and up)
    ```
5) Fill in parameters when asked
6) Set up database:

    ```
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:schema:update --force
    ```

7) Set permissions to allow apache (_www or www) to write to:

    ```
    var/
    upload/
    ```

## To Update
1) Navigate to blt2 folder
2) Do a pull from the repository

    ```
    $ git pull
    ```