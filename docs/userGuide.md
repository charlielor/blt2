User guide
==========
## Table of contents
- [Introduction](#introduction)
- [Entity creation](#entityCreation)
    - [Creating a new Shipper](#newShipper)
    - [Creating a new Receiver/Vendor](#newReceiverVendor)
    - [Creating/Submitting a new Package](#newPackage)
- [Delivering a Package](#delivering)
- [Marking a Package for pickup](#pickup)
- [Editing a Package](#editingPackage)
- [View a list of packages for a given date](#view)
- [Enabling/disabling entities](#maintenance)
- [Reporting](#reporting)

## <a name="introduction"></a>Introduction
After getting BLT2 up and running on your server, you should see this when you go to the website (after any authentication methods).

![Main menu](usersGuide/main.png)

To get started, you'll have to add Receivers, Shippers, and Vendors to the database. You can either do this as Packages arrive or create a MySQL/MariaDB .sql file and import it into the database.

This guide will lead you through creating your first Receiver, Shipper and Vendor. From there, the guide will lead you through submitting a new Package and delivering a Package to a Receiver. Finally, the guide will go into detail about some of the options you have within each page.
 
## <a name="entityCreation"></a>Creation of entities
The creation of entities are through the receiving page (/receiving). A notification bar at the top of the browser will let you know if the creation of an entity is successful or not.  

![Receiving](usersGuide/receiving.png)

### <a name="newShipper"></a>Creating a new Shipper
Going to the receiving page (/receiving) will automatically prompt you to select a Shipper. From here, you can click "Add new Shipper" to create a new Shipper.

Once the new Shipper has been created, the list will automatically update to include the newly created Shipper.

#### Requirements for creating a new Shipper
- Name must be unique

#### Steps:

##### 1) Click on "Receiving" and when the "Select a Shipper" dialog comes up, click "Add new Shipper"
![Shipper creation 1](usersGuide/shipperCreation1.png) 

##### 2) Enter in the name of the new Shipper and click "Submit"
![Shipper creation 2](usersGuide/shipperCreation2.png)

##### 3) If the creation is successful, you'll see the new Shipper along with a green bar notifying that it was successfully created
![Shipper creation 3](usersGuide/shipperCreation3.png)

### <a name="newReceiverVendor"></a>Creating a new Receiver/Vendor
The creation of a new Receiver/Vendor requires that you have the form up either by scanning in a tracking number or entering in text and clicking on "Enter in details". From there, you can click "New" next to the respective entity that you want to create.

Once the new Receiver/Vendor has been created, the new Receiver/Vendor input text field will automatically populate the field.
 
#### Requirements for creating a new Receiver/Vendor
- Name must be unique

#### Steps:

##### 1) Either scan in or type in a tracking number and click "Enter in details"
![Receiver/Shipper creation 1](usersGuide/receiverVendorCreation1.png)   

#### Vendor creation
##### 2) (Vendor) Click on "New" next to the Vendor input text field
![Receiver/Vendor creation 2](usersGuide/receiverVendorCreation2.png) 

##### 3) (Vendor) Enter in the name of the new Vendor and click "Submit"
![Receiver/Vendor creation 3](usersGuide/receiverVendorCreation3.png)
 
##### 4) (Vendor) If the creation is successful, you'll see the new Vendor populate the Vendor input text field along with a green bar notifying that it was successfully created 
![Receiver/Vendor creation 4](usersGuide/receiverVendorCreation4.png)

#### Receiver creation
##### 2) (Receiver) Click on "New" next to the Receiver input text field
![Receiver/Vendor creation 5](usersGuide/receiverVendorCreation5.png) 

##### 3) (Receiver) Enter in the name and room number of the new Receiver and click "Submit"
![Receiver/Vendor creation 6](usersGuide/receiverVendorCreation6.png)
 
##### 4) (Receiver) If the creation is successful, you'll see the new Receiver populate the Receiver input text field along with a green bar notifying that it was successfully created 
![Receiver/Vendor creation 7](usersGuide/receiverVendorCreation7.png)

### <a name="newPackage"></a>Creating/Submitting a new Package
To create and submit a new Package, first scan in (or enter in) the tracking number into the tracking number text field then click "Enter in details". From there, you can enter in information about the Package such as its Vendor, Receiver, the number of packages for that Package and attach its packing slip(s). The Shipper is already selected when you loaded /receiving and each Package will use the same Shipper until a new Shipper is selected (by clicking on "Select Shipper"). 

When the form opens, the Vendor search input box should be opened, ready for you to start typing the name of the Vendor. Once the Vendor is selected, the Receiver search input box should open, ready for you to start typing the name of the Receiver.

If a Package has no attached packing slips (either through the file upload or with a picture taken with a camera), BLT2 will ask if it's okay to submit the Package without one. 

#### Requirements for creating a new Package
- Tracking number must be unique

#### Steps:

##### 1) Either scan in or type in a tracking number and click "Enter in details"
![Package creation/submission 1](usersGuide/packageCreation1.png)

##### 2) Search for the Vendor that shipped the Package
![Package creation/submission 2](usersGuide/packageCreation2.png)

##### 3) Search for the Receiver that will receive the Package
![Package creation/submission 3](usersGuide/packageCreation3.png)

##### 4) Browse for scanned packing slip(s) and attach them (can attach multiple files) AND/OR using an attached camera, take a picture of the packing slip(s)
##### Note: You don't need to attach packing slips but there'll be a prompt asking you if it is okay to submit the Package with no packing slips
![Package creation/submission 4](usersGuide/packageCreation4.png) 

##### 5) (Using an attached camera) Click on "Take a picture" and when the video feed loads and once the camera is focused on the packing slip, click the video to take a picture
![Package creation/submission 5](usersGuide/packageCreation5.png) 

##### 6) (Using an attached camera) An image confirmation dialog will come up asking for confirmation of picture
![Package creation/submission 6](usersGuide/packageCreation6.png)
 
##### 7) Once you're done attaching packing slips and are ready to submit the new Package, click "Submit"
![Package creation/submission 7](usersGuide/packageCreation7.png)

##### 8) If the creation is successful, you'll see the new Package along with its information in the "Items received for today" table underneath the "Enter in details" button along with a green bar notifying that it was successfully created
![Package creation/submission 8](usersGuide/packageCreation8.png)

## <a name="delivering"></a>Delivering a Package
The delivery of a Package is done under the delivering page (/delivering).

#### Steps:

##### 1) Click on "Delivering"
![Delivering 1](usersGuide/delivering1.png)

##### 2) Either scan in or type in the Receiver's name
![Delivering 2](usersGuide/delivering2.png)

##### 3) Either scan in or type in the tracking number and press enter/carriage return
###### Note: If there are more than one package for that tracking number, there'll be an alert telling you so
###### Note: Use a barcode scanner configured to add an enter key/carriage return at the end of each scan for maximum efficiency
![Delivering 3](usersGuide/delivering3.png)

##### 4) If the delivery is successful, the Package will be gone from the table underneath the input text field along with a green bar notifying that the delivery was successful
###### Note: If the Package that got delivered is the last package in the table, the page will cleared of its current Receiver
![Delivering 4](usersGuide/delivering4.png)

## <a name="pickup"></a>Marking a Package for pickup
When someone picks up a Package, the Package can be marked up as picked up instead of delivered. Marking a Package up for pickup is done under the receiving page (/receiving).

#### Steps:

##### 1) Click on "Receiving" and select a Shipper (Shipper doesn't matter in this case)

##### 2) Click on "Pickup" towards the upper right hand corner
![Pickup 1](usersGuide/pickup1.png)

##### 3) Either scan in or type in the tracking number for the Package being picked up and click "Submit"
![Pickup 2](usersGuide/pickup2.png)

##### 4) Validate the Package information for the Package being picked up and enter in the name of the person who is picking up the Package and click "Submit"
###### Note: If there are more than one package for that tracking number, there'll be an alert telling you so
![Pickup 3](usersGuide/pickup3.png)

##### 5) If marking the Package for pickup is successful, there'll be a green bar notifying that marking the Package for pickup 
![Pickup 4](usersGuide/pickup4.png)

## <a name="editingPackage"></a>Editing a Package
Sometimes you'll have to update an existing Package given additional information. Editing a Package is done under the receiving page (/receiving). The only thing you can not edit is the tracking number.

#### Steps:

##### 1) Click on "Receiving" and select a Shipper (Shipper doesn't matter in this case)

##### 2) Either scan in or type in the tracking number for that Package
![Editing a Package 1](usersGuide/editingPackage1.png)

##### 3) A warning will popup saying that the Package with tracking number already exists; click "Edit"
![Editing a Package 2](usersGuide/editingPackage2.png)

##### 4) Make changes to the Package and click "Submit"
###### Note: Even if there are existing packing slips, if there are no attached, new packing slips, there'll be a prompt asking if it is okay to submit the Package with no packing slips
###### Note: In this example, two packing slips that were attached to this Package got deleted
###### Note: When editing a Package requires removal of packing slips, they are not deleted from the server but instead renamed to reflect the status of the file
![Editing a Package 3](usersGuide/editingPackage3.png)

##### 5) If editing the Package is successful, there'll be a green bar notifying that the Package has been successfully updated
###### Note: If the Package is currently listed under the "Items received for today", the Package in that table will also update
![Editing a Package 4](usersGuide/editingPackage4.png)

## <a name="view"></a>View a list of packages for a given date
To view all Packages received for any selected date, go to the view page (/view). By default, the view page will load today's received Packages first. The table will automatically populate itself as Packages are entered in to the system through the receiving page (/receiving).
  
#### Steps:

##### 1) Click on "View Packages"
![View Packages 1](usersGuide/view1.png)

##### 2) Today's received Packages will show by default
![View Packages 2](usersGuide/view2.png)

##### 3) Click on the blue button with today's date on it to select a new date
![View Packages 3](usersGuide/view3.png)

## <a name="maintenance"></a>Enabling/disabling/editing entities
Entities cannot be deleted but Receivers, Shippers and Vendors can be disabled to have it not display in other parts of BLT2. For example, if a Receiver is no longer a part of the department, it can be disabled so that you wouldn't be able to search for it when adding a new Package entity. Entities can be edited to have a new name, in the case of Shipper and Vendor, or to have a new name and/or room number as in the case for Receiver.
    
#### Steps:

##### 1) Click on "Maintenance"
![Maintenance 1](usersGuide/maintenance1.png)

##### 2) From here, you can click on "Disable" next to the entity to disable it or "Enable" to enable it

###### Note: If you know the tracking number of a Package and want information about it but don't know when it was received, you can search for it here
###### Note: The reason why Packages and Vendors have a searchable textbox instead of a list is because there tends to be more of it than Shippers or Receivers
![Maintenance 2](usersGuide/maintenance2.png)

![Maintenance 3](usersGuide/maintenance3.png)

![Maintenance 4](usersGuide/maintenance4.png)

##### 3) To edit an entity, just click on "Edit" next to the entity and proceed to make the necessary changes, then click "Update"
![Maintenance 5](usersGuide/maintenance5.png)

![Maintenance 6](usersGuide/maintenance6.png)

![Maintenance 7](usersGuide/maintenance7.png)

## <a name="reporting"></a>Reporting
A simple reporting system has been implemented to help users of BLT2 gather data about Packages received and delivered to their department. It has the ability to call pre-made queries and have the data exported to a CSV file for further processing using a more advance application.

##### Note: Depending on how many days are in between date begin and date end, the graph will show either individual dates, weeks or months

#### Steps:

##### 1) Click on "Reporting"
![Reporting 1](usersGuide/reporting1.png)

##### 2) Select a query from the list of pre-made queries
![Reporting 2](usersGuide/reporting2.png)

##### 3) Search for a token
![Reporting 3](usersGuide/reporting3.png)

##### 4) Select a begin and end date and click "Go"
![Reporting 4](usersGuide/reporting4.png)

##### 5) Either click on a bar for a specific day, week or month to get a list of Packages for that selection or click "Graph to table" to list the entire graph 
![Reporting 5](usersGuide/reporting5.png)

![Reporting 6](usersGuide/reporting6.png)

##### 6) Click "Table to CSV" to download a CSV file of the table listed above
![Reporting 7](usersGuide/reporting7.png)