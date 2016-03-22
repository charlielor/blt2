<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Vendor;

class VendorEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor() {
        $vendor = new Vendor("University of Wisconsin - Madison");

        $this->assertEquals("University of Wisconsin - Madison", $vendor->getName());
        $this->assertEquals(true, $vendor->getEnabled());

        $this->assertInstanceOf(Vendor::class, $vendor);
    }

    public function testGetName() {
        $vendor = new Vendor("University of Wisconsin - Madison");

        $this->assertEquals("University of Wisconsin - Madison", $vendor->getName());
    }

    public function testSetName() {
        $vendor = new Vendor("University of Wisconsin - Madison");
        $vendor->setName("University of Wisconsin");

        $this->assertEquals("University of Wisconsin", $vendor->getName());
    }

    public function testGetEnabled() {
        $vendor = new Vendor("University of Wisconsin - Madison");

        $this->assertEquals(true, $vendor->getEnabled());
    }

    public function testSetEnabled()  {
        $vendor = new Vendor("University of Wisconsin - Madison");
        $vendor->setEnabled(false);

        $this->assertEquals(false, $vendor->getEnabled());
    }
}