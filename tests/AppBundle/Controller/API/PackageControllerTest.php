<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\PackingSlip;
use AppBundle\Entity\Vendor;
use AppBundle\Entity\Receiver;
use AppBundle\Entity\Shipper;

class PackageControllerTest extends WebTestCase
{
    public function testNewPackageRoute() {
        $vendor = new Vendor("University of Wisconsin - Madison", "PackageTest");
        $shipper = new Shipper("USPS", "PackageTest");
        $receiver = new Receiver("Office", 111, "PackageTest");

        $client = static::createClient();

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "1Z2345",
            "numOfPackages" => 4,
            "shipper" => json_encode($shipper),
            "vendor" => json_encode($vendor),
            "receiver" => json_encode($receiver)
        ));

        # Testing response code for /package/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testUpdatePackageRoute() {
        $client = static::createClient();

        $client->request('PUT', '/package/0/update', array(
            "name" => "updatePackage"
        ));

        # Testing response code for /package/update
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testDeletePackageRoute() {
        $client = static::createClient();

        $client->request('PUT', '/package/0/delete');

        # Testing response code for /package/0/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testSearchPackageRoute() {
        $client = static::createClient();

        $client->request('GET', '/package/search', array(
            "term" => "test"
        ));

        # Testing response code for /package/search
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }
}