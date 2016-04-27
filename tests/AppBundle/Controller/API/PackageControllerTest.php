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

        $receiverResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertNotNull($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertNotNull($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertNotNull($vendorResponse['object']);

        $vendor = $shipperResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numOfPackages" => 4,
            "shipperId" => $shipper['id'],
            "receiverId" => $receiver['id'],
            "vendorId" => $vendor['id']
        ));

        # Testing response code for /package/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $newPackage = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertArrayHasKey('message', $newPackage);
        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotNull($newPackage['object']);

        $this->assertEquals('success', $newPackage['result']);

        # Testing against duplicates
        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numOfPackages" => 4,
            "shipperId" => $shipper['id'],
            "receiverId" => $receiver['id'],
            "vendorId" => $vendor['id']
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $duplicatePackage = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $duplicatePackage);
        $this->assertArrayHasKey('message', $duplicatePackage);
        $this->assertArrayHasKey('object', $duplicatePackage);
        $this->assertNull($duplicatePackage['object']);
        $this->assertEquals('error', $duplicatePackage['result']);

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

    public function testDeletePackageRoute() {
        $client = static::createClient();

        $client->request('GET', '/package/search', array(
            "term" => "testPackage"
        ));

        $packageResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        var_dump($packageResponse['object']);

        $this->assertNotNull($packageResponse['object']);

        $package = $packageResponse['object'];

        # Testing response code for /package/{id}/delete
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $deletePackageResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        var_dump($deletePackageResponse);

        // Search database for existing entities and delete them
        $client->request('GET', '/receiver/search', array(
            "term" => "testPackageReceiver"
        ));

        $receiverResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertNotNull($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('GET', '/shipper/search', array(
            "term" => "testPackageShipper"
        ));

        $shipperResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertNotNull($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('GET', '/vendor/search', array(
            "term" => "testPackageVendor"
        ));

        $vendorResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertNotNull($vendorResponse['object']);

        $vendor = $shipperResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}