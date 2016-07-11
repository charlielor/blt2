User's Guide
============

## At a glance
### Main menu
![Main menu](usersGuide/menu.png)
#### Receiving
![Receiving](usersGuide/receiving.png)
#### Delivering
![Delivering](usersGuide/delivering.png)
#### View
![View](usersGuide/view.png)
#### Reporting
![Reporting](usersGuide/reporting.png)
#### Maintenance
![Maintenance](usersGuide/maintenance.png)

## Getting started
#### Introduction
After getting BLT2 up and running on your server, you'll have to add Receivers, Shippers, and Vendors to the database. You can either do this as Packages arrive or create a MySQL/MariaDB .sql file and import it into the database.

## Basics
### Creation of entities
The creation of entities are through the receiving page (/receiving).

#### Creating a new Shipper
Going to the receiving page (/receiving) will automatically prompt you to select a Shipper. From here, you can click on "Add new Shipper" to create a new Shipper.

##### Requirements for creating a new Shipper
- Name must be unique

![Select a Shipper](usersGuide/entityCreation/shipper.png) 

![Create a new Shipper](usersGuide/entityCreation/shipper2.png)

#### Creating a new Vendor
By scanning in a tracking number (or entering in text) into the tracking number input field and opening up the form by clicking on "Enter in details", you'll have the chance to create a new Vendor by clicking on "New" next to the Vendor search box.

##### Requirements for creating a new Vendor
- Name must be unique

![Receiving - New entity creation](usersGuide/entityCreation/textInput.png) 

![Receiving - Form](usersGuide/entityCreation/form.png)

![Receiving - New Vendor](usersGuide/entityCreation/vendor.png)

#### Creating a new Receiver
By scanning in a tracking number (or entering in text) into the tracking number input field and opening up the form by clicking on "Enter in details", you'll have the chance to create a new Receiver by clicking on "New" next to the Receiver search box.

##### Requirements for creating a new Receiver
- Name must be unique

![Receiving - New entity creation](usersGuide/entityCreation/textInput.png) 

![Receiving - Form](usersGuide/entityCreation/form.png)

![Receiving - New Receiver](usersGuide/entityCreation/receiver.png)

### Receiving
#### Submitting a new Package entity