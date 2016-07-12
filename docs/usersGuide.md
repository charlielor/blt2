User's guide
============
## Table of contents
- Getting started
    - Creating a new Shipper
    - Creating a new Receiver/Vendor
    - Creating/Submitting a new Package
- Receiving
- Delivering
- Reporting
- View
- Maintenance

## Getting started
### Introduction
After getting BLT2 up and running on your server, you should see this when you go to the website (after any authentication methods).

![Main menu](usersGuide/main.png)

To get started, you'll have to add Receivers, Shippers, and Vendors to the database. You can either do this as Packages arrive or create a MySQL/MariaDB .sql file and import it into the database.

This guide will lead you through creating your first Receiver, Shipper and Vendor. From there, the guide will lead you through submitting a new Package and delivering a Package to a Receiver. Finally, the guide will go into detail about some of the options you have within each page.
 
### Creation of entities
The creation of entities are through the receiving page (/receiving). A notification bar at the top of the browser will let you know if the creation of an entity is successful or not.  

#### Creating a new Shipper
Going to the receiving page (/receiving) will automatically prompt you to select a Shipper. From here, you can click on "Add new Shipper" to create a new Shipper.

Once the new Shipper has been created, the list will automatically update to include the newly created Shipper.

##### Requirements for creating a new Shipper
- Name must be unique

##### Steps:

###### Go to /receiving and when the "Select a Shipper" dialog comes up, click on "Add new Shipper"
![Shipper creation 1](usersGuide/shipperCreation1.png) 

###### Enter in the name of the new Shipper and click on "Submit"
![Shipper creation 2](usersGuide/shipperCreation2.png)

###### If the creation is successful, you'll see the new Shipper along with a green bar notifying that it was successfully created
![Shipper creation 3](usersGuide/shipperCreation3.png)

#### Creating a new Receiver/Vendor
The creation of a new Receiver/Vendor requires that you have the form up either by scanning in a tracking number or entering in text and clicking on "Enter in details". From there, you can click on "New" next to the respective entity that you want to create.

Once the new Receiver/Vendor has been created, the new Receiver/Vendor input text field will automatically populate the field.
 
##### Requirements for creating a new Receiver/Vendor
- Name must be unique

##### Steps:

###### Either scan in a tracking number or type it in and click on "Enter in details"
![Receiver/Shipper creation 1](usersGuide/receiverVendorCreation1.png)   

###### (Vendor) Click on "New" next to the Vendor input text field
![Receiver/Shipper creation 2](usersGuide/receiverVendorCreation2.png) 

###### (Vendor) Enter in the name of the new Vendor and click on "Submit"
![Receiver/Shipper creation 3](usersGuide/receiverVendorCreation3.png)
 
###### (Vendor) If the creation is successful, you'll see the new Vendor populate the Vendor input text field along with a green bar notifying that it was successfully created 
![Receiver/Shipper creation 4](usersGuide/receiverVendorCreation4.png)

###### (Receiver) Click on "New" next to the Receiver input text field
![Receiver/Shipper creation 2](usersGuide/receiverVendorCreation2.png) 

###### (Receiver) Enter in the name and room number of the new Receiver and click on "Submit"
![Receiver/Shipper creation 3](usersGuide/receiverVendorCreation5.png)
 
###### (Receiver) If the creation is successful, you'll see the new Receiver populate the Receiver input text field along with a green bar notifying that it was successfully created 
![Receiver/Shipper creation 4](usersGuide/receiverVendorCreation6.png)

#### Creating/Submitting a new Package
To create and submit a new Package, first scan in (or enter in) the tracking number into the tracking number text field and click on "Enter in details". From there, you can enter in information about the Package such as its Vendor, Receiver, the number of packages with that tracking number and attach its packing slip. The Shipper was already selected when you loaded /receiving and each Package will use the same Shipper until a new Shipper is selected (by clicking on "Select Shipper"). 

When the form opens, the Vendor search input box should be opened, ready for you to start typing the name of the Vendor. Once the Vendor is selected, the Receiver search input box should open, ready for you to start typing the name of the Receiver.

If a Package has no attached packing slips (either through the file upload or with a picture taken with a camera), BLT2 will ask if it's okay to submit the Package without one. 

##### Requirements for creating a new Package
- Tracking number must be unique

##### Steps:

###### Either scan in a tracking number or type it in and click on "Enter in details"
![Package creation/submission 1](usersGuide/packageCreation1.png)

###### Search for the Vendor that shipped the Package
![Package creation/submission 2](usersGuide/packageCreation2.png)

###### Search for the Receiver that will receive the Package
![Package creation/submission 3](usersGuide/packageCreation3.png)

###### Browse for scanned packing slip(s) and attach them (can attach multiple files) AND/OR using an attached camera, take a picture of the packing slip(s)
![Package creation/submission 4](usersGuide/packageCreation4.png) 

###### (Using an attached camera) Click on "Take a picture" and when the video feed loads and once the camera is focused on the packing slip, click on the video to take a picture
![Package creation/submission 5](usersGuide/packageCreation5.png) 

###### (Using an attached camera) An image confirmation dialog will come up asking for confirmation of picture
![Package creation/submission 6](usersGuide/packageCreation6.png)
 
###### Once you're done attaching packing slips and are ready to submit the new Package, click on "Submit"
![Package creation/submission 7](usersGuide/packageCreation7.png)

###### If the creation is successful, you'll see the new Package along with its information in the "Items received for today" table underneath the "Enter in details" button along with a green bar notifying that it was successfully created
![Package creation/submission 8](usersGuide/packageCreation8.png)