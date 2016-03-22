<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Shipper;

class ShipperEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor() {
        $shipper = new Shipper("USPS", "ShipperTest");

        $this->assertEquals("USPS", $shipper->getName());
        $this->assertTrue($shipper->getEnabled());

        $this->assertInstanceOf(Shipper::class, $shipper);
    }

    public function testGetName() {
        $shipper = new Shipper("USPS", "ShipperTest");

        $this->assertEquals("USPS", $shipper->getName());
    }

    public function testSetName() {
        $shipper = new Shipper("USPS", "ShipperTest");
        $shipper->setName("FedEx Ground");

        $this->assertEquals("FedEx Ground", $shipper->getName());
    }

    public function testGetEnabled() {
        $shipper = new Shipper("USPS", "ShipperTest");

        $this->assertTrue($shipper->getEnabled());
    }

    public function testSetEnabled()  {
        $shipper = new Shipper("USPS", "ShipperTest");
        $shipper->setEnabled(false);

        $this->assertNotTrue($shipper->getEnabled());
    }

    public function testDateCreated() {
        $shipper = new Shipper("USPS", "ShipperTest");

        $this->assertNotNull($shipper->getDateCreated());
    }

    public function testGetDateModified() {
        $shipper = new Shipper("USPS", "ShipperTest");

        $this->assertNotNull($shipper->getDateModified());
    }

    public function testSetDateModified() {
        $shipper = new Shipper("USPS", "ShipperTest");
        $testDate = new \DateTime("2016-03-22");

        $shipper->setDateModified($testDate);

        $this->assertEquals($testDate, $shipper->getDateModified());
    }

    public function testGetUserLastModified() {
        $shipper = new Shipper("USPS", "ShipperTest");

        $this->assertEquals("ShipperTest", $shipper->getUserLastModified());
    }

    public function testSetUserLastModified() {
        $shipper = new Shipper("USPS", "ShipperTest");
        $shipper->setUserLastModified("ShipperTest2");

        $this->assertEquals("ShipperTest2", $shipper->getUserLastModified());
    }
}