Biochemistry Logistics Tool 2 (BLT2)
====================================
## Testing
master:  
[![master](https://travis-ci.org/charlielor/blt2.svg?branch=master)](https://travis-ci.org/charlielor/blt2)

dev:  
[![dev](https://travis-ci.org/charlielor/blt2.svg?branch=dev)](https://travis-ci.org/charlielor/blt2)

## Browser compatibility
- All modern browsers (Chrome, Edge, Firefox, IE 11+, Opera, Safari) support BLT2 at its most basic functionality
- Only Chrome 51+ is compatible with the camera functionality (to take pictures of packing slips) with a high video resolution
- All browsers with WebRTC's MediaDevice API can use the camera functionality (to take pictures of packing slips) but video resolution might be limited
- All other browsers can use the upload functionality to upload packing slips, if needed

## Summary
This web application is a simple logistics tool to help employees in the receiving and shipping unit of the Department of Biochemistry at the University of Wisconsin - Madison.

BLT2 allows users to submit important information about packages such as tracking number, date received and packing slip images to a database, allow users in the main office to view package information submitted on a given date, and provides a list of packages for the users for when they are out delivering packages to the receivers.

Given the amount of packages delivered daily to the Biochemistry department, this web application serves as a 'middleman', along with the employees in the shipping and receiving unit, between the packages delivered and their receivers. BLT2 also works along side with the Biochemistry Purchasing System (BPS) as it stores the location of packing slips in the database so that employees in the office can verify purchase orders to packages delivered.

BLT2 is not tied to the Department of Biochemistry at the University of Wisconsin - Madison. Anyone can use the web application on their own web server using their own database to store their own information about incoming packages to their own department. BLT2 is released under the MIT license and can be modified to whatever fits your department.


## Changes from [BLT1](https://bitbucket.org/lorcharlie/uwbiochemistrylogisticstool)
* Symfony2 2.* --> Symfony 3
* One template for each page, all compatible with desktop and mobile devices
* Select2 3.* --> Select2 4.0+
* Backend Rewritten
    * REST API calls to entities (Package, Receiver, Shipper, Vendor)
    * Frontend now has its own controllers
* Tests for Backend, Entity and Frontend Controllers (PHPUnit)
* Using LESS with Bootstrap breakpoints to help with templates for desktop/mobile devices

## Wiki
Almost everything about BLT2 can be located in the [wiki](https://github.com/charlielor/blt2/wiki)

## For anyone who wants to use some sort of authentication/authorization system
1. Fork project
2. Update security.yml to fit your authentication/authorization needs
3. Create User/UserProvider classes to help facilitate if needed
4. See the deployment/shipping.biochem.wisc.edu branch for an example

## License
BLT2 is released under the MIT license