Biochemistry Logistics Tool 2 (BLT2)
====================================
This web application is to help facilitate employees of the shipping and receiving department within the Biochemistry department at the University of Wisconsin - Madison. BLT allows users to submit important information about packages received at the shipping dock to a database, allows users in the main office to view package information submitted on a given date, and provides a list for of packages for the users for when they are out delivering packages to the labs.
Given the amount of packages delivered daily to the Biochemistry department, this web application serves as a 'middleman', along with the employees in the shipping and receiving department, between the packages delivered and the labs it's suppose to go to. BLT also works along side with the Biochemistry Purchasing System (BPS) as it stores the location of packing slips in the database so that employees in the office can verify purchase orders to packages delivered.

BLT2 using Symfony3 as its PHP framework for the University of Wisconsin - Madison, Biochemistry Department.

1) Navigate to www root
2) 
```
$ git clone -b deployment/shipping.biochem.wisc.edu https://github.com/charlielor/blt2.git shipping.biochem.wisc.edu/
```
3) Download composer --> https://getcomposer.org/download/
4)
```
$ php composer.phar install (make sure php is version 5.6.* and up)
```
5) Fill in parameters when asked
6) Set up database:
```
$ php bin/console doctrine:database:create
$ php bin/console doctrine:schema:update --force
```

7) Set permissions to allow apache (or _www or www) to write to:
```
var/
upload/
```