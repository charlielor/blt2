Update Guide
============

## Introduction
There are two updates that can be done for BLT2: BLT2 specific updates or Symfony (and related vendors) updates. You can either do one or the other but updates are typically aligned so you update them at the same time.

## BLT2 Specific Update
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

## Symfony (and related vendors) Update
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