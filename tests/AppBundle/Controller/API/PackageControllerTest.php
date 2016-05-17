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

        // Assert that receiver was successfully created
        $receiverResponse = json_decode(json_decode($client->getResponse()->getContent()), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotNull($receiverResponse['object']);
        $this->assertCount(1, $receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode(json_decode($client->getResponse()->getContent()), true);
        $this->assertArrayHasKey('result', $shipperResponse);
        $this->assertEquals('success', $shipperResponse['result']);

        $this->assertArrayHasKey('message', $shipperResponse);

        $this->assertArrayHasKey('object', $shipperResponse);
        $this->assertNotNull($shipperResponse['object']);
        $this->assertCount(1, $shipperResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode(json_decode($client->getResponse()->getContent()), true);
        $this->assertArrayHasKey('result', $vendorResponse);
        $this->assertEquals('success', $vendorResponse['result']);

        $this->assertArrayHasKey('message', $vendorResponse);

        $this->assertArrayHasKey('object', $vendorResponse);
        $this->assertNotNull($vendorResponse['object']);
        $this->assertCount(1, $vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numOfPackages" => 4,
            "shipperId" => $shipper[0]['id'],
            "receiverId" => $receiver[0]['id'],
            "vendorId" => $vendor[0]['id']
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
        $this->assertEquals('success', $newPackage['result']);

        $this->assertArrayHasKey('message', $newPackage);

        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotNull($newPackage['object']);


        # Testing against duplicates
        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numOfPackages" => 4,
            "shipperId" => $shipper[0]['id'],
            "receiverId" => $receiver[0]['id'],
            "vendorId" => $vendor[0]['id']
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

        # Testing with errors
        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "receiverId" => $receiver[0]['id'],
            "vendorId" => $vendor[0]['id']
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $errorParamsResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $errorParamsResponse);
        $this->assertArrayHasKey('message', $errorParamsResponse);
        $this->assertArrayHasKey('object', $errorParamsResponse);
        $this->assertNull($errorParamsResponse['object']);
        $this->assertEquals('error', $errorParamsResponse['result']);

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

        $updatedPackage = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $updatedPackage);
        $this->assertArrayHasKey('message', $updatedPackage);
        $this->assertArrayHasKey('object', $updatedPackage);
        $this->assertNotNull($updatedPackage['object']);
        $this->assertEquals('success', $updatedPackage['result']);
        $this->assertEquals(1, $updatedPackage['object'][0]['numberOfPackages']);

        // Test for errors
        $client->request('PUT', '/package/test/update', array(
            "numOfPackages" => 1,
            "removedPackingSlipIds" => array(
                "removePackingSlipOne", "removePackingSlipTwo"
            )
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $errorResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertArrayHasKey('message', $errorResponse);
        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertNull($errorResponse['object']);
        $this->assertEquals('error', $errorResponse['result']);
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

        $searchPackage = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $searchPackage);
        $this->assertArrayHasKey('message', $searchPackage);
        $this->assertArrayHasKey('object', $searchPackage);
        $this->assertNotNull($searchPackage['object']);
        $this->assertEquals('success', $searchPackage['result']);

        // Test for errors
        $client->request('GET', '/package/search', array(
            "term" => "12345"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $errorResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertArrayHasKey('message', $errorResponse);
        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertNull($errorResponse['object']);
        $this->assertEquals('error', $errorResponse['result']);
    }

    public function testDeletePackageRoute() {
        $client = static::createClient();

        $client->request('GET', '/package/search', array(
            "term" => "testPackage"
        ));

        $packageResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertNotNull($packageResponse['object']);

        $package = $packageResponse['object'][0];

        # Testing response code for /package/{id}/delete
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $packageDeleted = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $packageDeleted);
        $this->assertArrayHasKey('message', $packageDeleted);
        $this->assertArrayHasKey('object', $packageDeleted);
        $this->assertNotNull($packageDeleted['object']);
        $this->assertEquals('success', $packageDeleted['result']);

        // Test for errors
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $errorResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertArrayHasKey('message', $errorResponse);
        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertNull($errorResponse['object']);
        $this->assertEquals('error', $errorResponse['result']);

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

        $vendor = $vendorResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}