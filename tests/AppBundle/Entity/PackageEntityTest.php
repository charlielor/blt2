<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\Vendor;
use AppBundle\Entity\Receiver;
use AppBundle\Entity\Shipper;

class PackageEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor() {
        $vendor = new Vendor("University of Wisconsin - Madison");
        $shipper = new Shipper("USPS");
        $receiver = new Receiver("Office", 111);

        $package = new Package("12345", 4, $shipper, $receiver, $vendor, "PackageTest");
    }
}