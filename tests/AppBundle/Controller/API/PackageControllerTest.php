<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\PackingSlip;

class PackageControllerTest extends WebTestCase
{
    public function testNewPackageRoute() {
        $vendor = 1; // testVendor
        $shipper = 1; // testShipper
        $receiver = 1; // testReceiver

        $client = static::createClient();

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numOfPackages" => 4,
            "shipperId" => $shipper,
            "receiverId" => $receiver,
            "vendorId" => $vendor
        ));

        # Testing response code for /package/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        # Testing against duplicates
        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numOfPackages" => 4,
            "shipperId" => $shipper,
            "receiverId" => $receiver,
            "vendorId" => $vendor
        ));

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

        $client->request('PUT', '/package/testPackage/update', array(
            "numOfPackages" => 1,
            "removedPackingSlipIds" => array(
                "removePackingSlipOne", "removePackingSlipTwo"
            )
        ));

        # Testing response code for /package/{id}/update
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

        $client->request('DELETE', '/package/testPackage/delete');

        # Testing response code for /package/{id}/disable
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
            "term" => "testPackage"
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