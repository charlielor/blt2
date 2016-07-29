Update guide
============

## Introduction
There are two updates that can be done for BLT2: BLT2 specific updates or Symfony (and related vendors) updates. You can either do one or the other but updates are typically aligned so you update them at the same time.

## BLT2 specific update
1. Navigate to blt2 directory and pull from repository

    ```
    $ git pull
    ```
2. Clear caches and install/dump assets

    ```
    $ php bin/console cache:clear --env=prod
    $ php bin/console assets:install --env=prod
    $ php bin/console assetic:dump --env=prod
    ```

## Symfony (and related vendors) update
1. Set the Symfony environment to prod

    ```
    SYMFONY_ENV=prod
    ```
2. Navigate to blt2 directory
3. Update composer

    ```
    $ php composer.phar self-update
    ```
4. Run composer

    ```
    $ php composer.phar update
    ``` 

## composer.json
In the root of blt2, there is a file named composer.json that holds all vendors, their requirements and what versions they can update to.

    "require": {
            "php": ">=5.5.9",
            "symfony/symfony": "3.*,<3.4.*",
            ... 
     },

In this example, PHP 5.5.9 and beyond is required and Symfony 3.* to 3.4.* are required. As of this writing, Symfony is at 3.1.1 and will be updated until it reaches 3.4.*. Symfony 3.4 is a long-term release and [will have support for until 2021](http://symfony.com/doc/current/contributing/community/releases.html#long-term-support-versions).

By running ```$ php composer.phar update```, you are invoking each vendor to update itself to its latest version specified in the composer.json file. Every vendor under "require" and "require-dev" (if under dev environment) except php will be updated until it has reached its maximum version.