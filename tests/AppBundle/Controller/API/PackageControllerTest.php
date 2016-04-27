<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\PackingSlip;

class PackageControllerTest extends WebTestCase
{
    public function testNewPackageRoute() {
        $client = static::createClient();

        // Setting up the database with fake entities
        $client->request('POST', '/receiver/new', array(
            "name" => "testPackageReceiver",
            "deliveryRoom" => 112
        ));

        $receiver = json_decode($client->getResponse()->getContent(), true);

        var_dump($receiver);

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipper = $client->getResponse()->getContent();

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendor = $client->getResponse()->getContent();

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

        $newPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertArrayHasKey('message', $newPackage);
        $this->assertArrayHasKey('object', $newPackage);

        $this->assertEquals('success', $newPackage['results']);

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

        $newPackage = json_decode($client->getResponse()->getContent());

        var_dump($newPackage);

        $this->assertArrayHasKey('results', $newPackage);
        $this->assertArrayHasKey('message', $newPackage);
        $this->assertArrayHasKey('object', $newPackage);

        $this->assertEqual('success', $newPackage['results']);
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

//    public function testDeletePackageRoute() {
//        $client = static::createClient();
//
//        $client->request('GET', '/package/search', array(
//            "term" => "testPackage"
//        ));
//
//        $testPackageJSON = json_decode($client->getResponse()->getContent());
//
//        var_dump($testPackageJSON);
//
//
//        $client->request('DELETE', '/package/testPackage/delete');
//
//        # Testing response code for /package/{id}/disable
//        $this->assertTrue($client->getResponse()->isSuccessful());
//
//        $this->assertTrue(
//            $client->getResponse()->headers->contains(
//                'Content-Type',
//                'application/json'
//            )
//        );
//    }
}