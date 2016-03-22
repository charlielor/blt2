<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Shipper;

class ShipperEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor() {
        $shipper = new Shipper("USPS");

        $this->assertEquals("USPS", $shipper->getName());
        $this->assertEquals(true, $shipper->getEnabled());

        $this->assertInstanceOf(Shipper::class, $shipper);
    }

    public function testGetName() {
        $shipper = new Shipper("USPS");

        $this->assertEquals("USPS", $shipper->getName());
    }

    public function testSetName() {
        $shipper = new Shipper("USPS");
        $shipper->setName("FedEx Ground");

        $this->assertEquals("FedEx Ground", $shipper->getName());
    }

    public function testGetEnabled() {
        $shipper = new Shipper("USPS");

        $this->assertEquals(true, $shipper->getEnabled());
    }

    public function testSetEnabled()  {
        $shipper = new Shipper("USPS");
        $shipper->setEnabled(false);

        $this->assertEquals(false, $shipper->getEnabled());
    }
}