<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackingSlip;
use AppBundle\Entity\Vendor;
use AppBundle\Entity\Receiver;
use AppBundle\Entity\Shipper;

class PackageEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $package->addPackingSlip($packingSlip, "PackageTest");

        $this->assertEquals("12345", $package->getTrackingNumber());
        $this->assertEquals(4, $package->getNumberOfPackages());
        $this->assertEquals($vendor, $package->getVendor());
        $this->assertEquals($shipper, $package->getShipper());
        $this->assertEquals($receiver, $package->getReceiver());
        $this->assertEquals("PackageTest", $package->getUserWhoReceived());
        $this->assertNotEmpty($package->getDateReceived());
        $this->assertNotEmpty($package->getDateModified());

        $this->assertCount(1, $package->getPackingSlips());

        $this->assertNotTrue($package->getDelivered());
        $this->assertNull($package->getDateDelivered());
        $this->assertNull($package->getUserWhoDelivered());

        $this->assertNotTrue($package->getPickedUp());
        $this->assertNull($package->getDatePickedUp());
        $this->assertNull($package->getUserWhoPickedUp());
        $this->assertNull($package->getUserWhoAuthorizedPickUp());


        $this->assertInstanceOf(Package::class, $package);
    }

    public function testGetTrackingNumber() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertEquals("12345", $package->getTrackingNumber());
    }

    public function testSetTrackingNumber() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        $package->setTrackingNumber("54321", "PackageTest");

        $this->assertEquals("54321", $package->getTrackingNumber());
    }

    public function testGetNumberOfPackages() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertEquals(4, $package->getNumberOfPackages());
    }

    public function testSetNumberOfPackages() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        $package->setNumberOfPackages(1, "PackageTest");

        $this->assertEquals(1, $package->getNumberOfPackages());
    }

    public function testGetVendor() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertEquals($vendor, $package->getVendor());
    }

    public function testSetVendor() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $vendor2 = new Vendor("University of Wisconsin", "PackageTest");
        $package->setVendor($vendor2);

        $this->assertEquals($vendor2, $package->getVendor());
    }

    public function testGetShipper() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertEquals($shipper, $package->getShipper());
    }

    public function testSetShipper() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        
        $shipper2 = $shipper = new Shipper("FedEx Ground", "PackageTest");
        $package->setShipper($shipper2);

        $this->assertEquals($shipper2, $package->getShipper());
    }

    public function testGetReceiver() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertEquals($receiver, $package->getReceiver());
    }

    public function testSetReceiver() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $receiver2 = $receiver = new Receiver("IT", 1212, "PackageTest");
        $package->setReceiver($receiver2);

        $this->assertEquals($receiver2, $package->getReceiver());
    }

    public function testGetUserWhoReceived() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertEquals("PackageTest", $package->getUserWhoReceived());
    }

    public function testSetUserWhoReceived() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $package->setUserWhoReceived("PackageTest2", "PackageTest");

        $this->assertEquals("PackageTest2", $package->getUserWhoReceived());
    }

    public function testGetDateReceived() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertNotEmpty($package->getDateReceived());
    }

    public function testSetDateReceived() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $testDate = new \DateTime("2016-03-22");
        $package->setDateReceived($testDate, "PackageTest");

        $this->assertEquals($testDate, $package->getDateReceived());
    }

    public function testGetPackingSlips() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        $package->addPackingSlip($packingSlip, "PackageTest");

        $this->assertCount(1, $package->getPackingSlips());
    }

    public function testAddPackingSlip() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");
        $packingSlip2 = new PackingSlip("ps2", "jpg", "fakepath/upload", md5("22222222222222222222222222222222"), "PackingSlipTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        $package->addPackingSlip($packingSlip, "PackageTest");
        $package->addPackingSlip($packingSlip2, "PackageTest");

        $this->assertCount(2, $package->getPackingSlips());
    }

    public function testRemovePackingSlips() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");
        $packingSlip2 = new PackingSlip("ps2", "jpg", "fakepath/upload", md5("22222222222222222222222222222222"), "PackingSlipTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        $package->addPackingSlip($packingSlip, "PackageTest");
        $package->addPackingSlip($packingSlip2, "PackageTest");

        $package->removePackingSlips($packingSlip, "PackageTest");

        $this->assertCount(1, $package->getPackingSlips());

        $package->removePackingSlips($packingSlip2, "PackageTest");

        $this->assertCount(0, $package->getPackingSlips());
    }

    public function testGetDelivered() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertNotTrue($package->getDelivered());
    }

    public function testSetDelivered() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        $package->setDelivered(true);

        $this->assertTrue($package->getDelivered());
    }

    public function testGetUserWhoDelivered() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertNull($package->getUserWhoDelivered());
    }

    public function testSetUserWhoDelivered() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $package->setUserWhoDelivered("PackageTest", "PackageTest");

        $this->assertEquals("PackageTest", $package->getUserWhoDelivered());
    }

    public function testGetDateDelivered() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertNull($package->getDateDelivered());
    }

    public function testSetDateDelivered() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $testDate = new \DateTime("2016-03-22");
        $package->setDateDelivered($testDate, "PackageTest");

        $this->assertEquals($testDate, $package->getDateDelivered());
    }

    public function testGetPickedUp() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertNotTrue($package->getPickedUp());
    }

    public function testSetPickedUp() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        $package->setPickedUp(true, "PackageTest");

        $this->assertTrue($package->getPickedUp());
    }

    public function testGetUserWhoPickedUp() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertNull($package->getUserWhoPickedUp());
    }

    public function testSetUserWhoPickedUp() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        $package->setUserWhoPickedUp("UserWhoPickedUpPackage", "PackageTest");

        $this->assertEquals("UserWhoPickedUpPackage", $package->getUserWhoPickedUp());
    }

    public function testGetDatePickedUp() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertNull($package->getDatePickedUp());
    }

    public function testSetDatePickedUp() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $testDate = new \DateTime("2016-03-22");
        $package->setDatePickedUp($testDate, "PackageTest");

        $this->assertEquals($testDate, $package->getDatePickedUp());
    }

    public function testGetUserWhoAuthorizedPickUp() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");

        $this->assertNull($package->getUserWhoAuthorizedPickUp());
    }

    public function testSetUserWhoAuthorizedPickUp() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
        $package->setUserWhoAuthorizedPickup("UserWhoAuthorizedPickUp", "PackageTest");

        $this->assertEquals("UserWhoAuthorizedPickUp", $package->getUserWhoAuthorizedPickUp());
    }
}