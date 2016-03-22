<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Vendor;

class VendorEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor() {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");

        $this->assertEquals("University of Wisconsin - Madison", $vendor->getName());
        $this->assertEquals(true, $vendor->getEnabled());

        $this->assertInstanceOf(Vendor::class, $vendor);
    }

    public function testGetName() {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");

        $this->assertEquals("University of Wisconsin - Madison", $vendor->getName());
    }

    public function testSetName() {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");
        $vendor->setName("University of Wisconsin");

        $this->assertEquals("University of Wisconsin", $vendor->getName());
    }

    public function testGetEnabled() {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");

        $this->assertEquals(true, $vendor->getEnabled());
    }

    public function testSetEnabled()  {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");
        $vendor->setEnabled(false);

        $this->assertEquals(false, $vendor->getEnabled());
    }

    public function testDateCreated() {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");

        $this->assertNotNull($vendor->getDateCreated());
    }

    public function testGetDateModified() {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");

        $this->assertNotNull($vendor->getDateModified());
    }

    public function testSetDateModified() {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");
        $testDate = new \DateTime("2016-03-22");

        $vendor->setDateModified($testDate);

        $this->assertEquals($testDate, $vendor->getDateModified());
    }

    public function testGetUserLastModified() {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");

        $this->assertEquals("VendorTest", $vendor->getUserLastModified());
    }

    public function testSetUserLastModified() {
        $vendor = new Vendor("University of Wisconsin - Madison", "VendorTest");
        $vendor->setUserLastModified("VendorTest2");

        $this->assertEquals("VendorTest2", $vendor->getUserLastModified());
    }
}