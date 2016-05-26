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
        $receiverResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotEmpty($receiverResponse['object']);
        $this->assertCount(1, $receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $shipperResponse);
        $this->assertEquals('success', $shipperResponse['result']);

        $this->assertArrayHasKey('message', $shipperResponse);

        $this->assertArrayHasKey('object', $shipperResponse);
        $this->assertNotEmpty($shipperResponse['object']);
        $this->assertCount(1, $shipperResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $vendorResponse);
        $this->assertEquals('success', $vendorResponse['result']);

        $this->assertArrayHasKey('message', $vendorResponse);

        $this->assertArrayHasKey('object', $vendorResponse);
        $this->assertNotEmpty($vendorResponse['object']);
        $this->assertCount(1, $vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
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

        $newPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertEquals('success', $newPackage['result']);

        $this->assertArrayHasKey('message', $newPackage);

        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotEmpty($newPackage['object']);
        $this->assertEquals(4, $newPackage['object']['numberOfPackages']);
        $this->assertEquals('testPackage', $newPackage['object']['trackingNumber']);
        $this->assertEquals('testPackageShipper', $newPackage['object']['shipper']['name']);
        $this->assertEquals('testPackageReceiver', $newPackage['object']['receiver']['name']);
        $this->assertEquals('testPackageVendor', $newPackage['object']['vendor']['name']);

        # Testing against duplicates
        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
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

        $duplicatePackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $duplicatePackage);
        $this->assertArrayHasKey('message', $duplicatePackage);
        $this->assertArrayHasKey('object', $duplicatePackage);
        $this->assertEmpty($duplicatePackage['object']);
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

        $errorParamsResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $errorParamsResponse);
        $this->assertArrayHasKey('message', $errorParamsResponse);
        $this->assertArrayHasKey('object', $errorParamsResponse);
        $this->assertEmpty($errorParamsResponse['object']);
        $this->assertEquals('error', $errorParamsResponse['result']);

        $package = $newPackage['object'];

        # Testing response code for /package/{id}/delete
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $packageDeleted = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $packageDeleted);
        $this->assertArrayHasKey('message', $packageDeleted);
        $this->assertArrayHasKey('object', $packageDeleted);
        $this->assertNotEmpty($packageDeleted['object']);
        $this->assertEquals('success', $packageDeleted['result']);

        // Search database for existing entities and delete them
        $client->request('GET', '/receiver/search', array(
            "term" => "testPackageReceiver"
        ));

        $receiverResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('GET', '/shipper/search', array(
            "term" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('GET', '/vendor/search', array(
            "term" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testUpdatePackageRoute() {
        $client = static::createClient();

        // Setting up the database with fake entities
        $client->request('POST', '/receiver/new', array(
            "name" => "testPackageReceiver",
            "deliveryRoom" => 112
        ));

        // Assert that receiver was successfully created
        $receiverResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotEmpty($receiverResponse['object']);
        $this->assertCount(1, $receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $shipperResponse);
        $this->assertEquals('success', $shipperResponse['result']);

        $this->assertArrayHasKey('message', $shipperResponse);

        $this->assertArrayHasKey('object', $shipperResponse);
        $this->assertNotEmpty($shipperResponse['object']);
        $this->assertCount(1, $shipperResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $vendorResponse);
        $this->assertEquals('success', $vendorResponse['result']);

        $this->assertArrayHasKey('message', $vendorResponse);

        $this->assertArrayHasKey('object', $vendorResponse);
        $this->assertNotEmpty($vendorResponse['object']);
        $this->assertCount(1, $vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
            "shipperId" => $shipper[0]['id'],
            "receiverId" => $receiver[0]['id'],
            "vendorId" => $vendor[0]['id']
        ));

        // Testing response code for /package/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $newPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertEquals('success', $newPackage['result']);

        $this->assertArrayHasKey('message', $newPackage);

        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotEmpty($newPackage['object']);
        $this->assertEquals(4, $newPackage['object']['numberOfPackages']);
        $this->assertEquals('testPackage', $newPackage['object']['trackingNumber']);
        $this->assertEquals('testPackageShipper', $newPackage['object']['shipper']['name']);
        $this->assertEquals('testPackageReceiver', $newPackage['object']['receiver']['name']);
        $this->assertEquals('testPackageVendor', $newPackage['object']['vendor']['name']);

        // Test for update
        $client->request('PUT', '/package/testPackage/update', array(
            "numberOfPackages" => 1,
            "deletePackingSlipIds" => array(
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

        $updatedPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $updatedPackage);
        $this->assertArrayHasKey('message', $updatedPackage);
        $this->assertArrayHasKey('object', $updatedPackage);
        $this->assertNotEmpty($updatedPackage['object']);
        $this->assertEquals('success', $updatedPackage['result']);
        $this->assertEquals(1, $updatedPackage['object']['numberOfPackages']);

        // Test for errors
        $client->request('PUT', '/package/test/update', array(
            "numberOfPackages" => 1,
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

        $errorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertArrayHasKey('message', $errorResponse);
        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertEmpty($errorResponse['object']);
        $this->assertEquals('error', $errorResponse['result']);

        $package = $newPackage['object'];

        # Testing response code for /package/{id}/delete
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $packageDeleted = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $packageDeleted);
        $this->assertArrayHasKey('message', $packageDeleted);
        $this->assertArrayHasKey('object', $packageDeleted);
        $this->assertNotEmpty($packageDeleted['object']);
        $this->assertEquals('success', $packageDeleted['result']);

        // Search database for existing entities and delete them
        $client->request('GET', '/receiver/search', array(
            "term" => "testPackageReceiver"
        ));

        $receiverResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('GET', '/shipper/search', array(
            "term" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('GET', '/vendor/search', array(
            "term" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testLikePackageRoute() {
        $client = static::createClient();

        // Setting up the database with fake entities
        $client->request('POST', '/receiver/new', array(
            "name" => "testPackageReceiver",
            "deliveryRoom" => 112
        ));

        // Assert that receiver was successfully created
        $receiverResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotEmpty($receiverResponse['object']);
        $this->assertCount(1, $receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $shipperResponse);
        $this->assertEquals('success', $shipperResponse['result']);

        $this->assertArrayHasKey('message', $shipperResponse);

        $this->assertArrayHasKey('object', $shipperResponse);
        $this->assertNotEmpty($shipperResponse['object']);
        $this->assertCount(1, $shipperResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $vendorResponse);
        $this->assertEquals('success', $vendorResponse['result']);

        $this->assertArrayHasKey('message', $vendorResponse);

        $this->assertArrayHasKey('object', $vendorResponse);
        $this->assertNotEmpty($vendorResponse['object']);
        $this->assertCount(1, $vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
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

        $newPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertEquals('success', $newPackage['result']);

        $this->assertArrayHasKey('message', $newPackage);

        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotEmpty($newPackage['object']);
        $this->assertEquals(4, $newPackage['object']['numberOfPackages']);
        $this->assertEquals('testPackage', $newPackage['object']['trackingNumber']);
        $this->assertEquals('testPackageShipper', $newPackage['object']['shipper']['name']);
        $this->assertEquals('testPackageReceiver', $newPackage['object']['receiver']['name']);
        $this->assertEquals('testPackageVendor', $newPackage['object']['vendor']['name']);

        // Test search like term
        $client->request('GET', '/package/like', array(
            "term" => "test"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $searchPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $searchPackage);
        $this->assertArrayHasKey('message', $searchPackage);
        $this->assertArrayHasKey('object', $searchPackage);
        $this->assertNotEmpty($searchPackage['object']);
        $this->assertEquals('success', $searchPackage['result']);
        $this->assertEquals('testPackage', $searchPackage['object'][0]['trackingNumber']);

        // Test for errors
        $client->request('GET', '/package/search', array(
            "term" => "stuffedchickenwings"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $errorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertArrayHasKey('message', $errorResponse);
        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertEmpty($errorResponse['object']);
        $this->assertEquals('error', $errorResponse['result']);

        $client->request('GET', '/package/search', array(
            "term" => "testPackage"
        ));

        $packageResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($packageResponse['object']);

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

        $packageDeleted = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $packageDeleted);
        $this->assertArrayHasKey('message', $packageDeleted);
        $this->assertArrayHasKey('object', $packageDeleted);
        $this->assertNotEmpty($packageDeleted['object']);
        $this->assertEquals('success', $packageDeleted['result']);

        // Search database for existing entities and delete them
        $client->request('GET', '/receiver/search', array(
            "term" => "testPackageReceiver"
        ));

        $receiverResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('GET', '/shipper/search', array(
            "term" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('GET', '/vendor/search', array(
            "term" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    public function testDeliverPackageRoute() {
        $client = static::createClient();

        // Setting up the database with fake entities
        $client->request('POST', '/receiver/new', array(
            "name" => "testPackageReceiver",
            "deliveryRoom" => 112
        ));

        // Assert that receiver was successfully created
        $receiverResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotEmpty($receiverResponse['object']);
        $this->assertCount(1, $receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $shipperResponse);
        $this->assertEquals('success', $shipperResponse['result']);

        $this->assertArrayHasKey('message', $shipperResponse);

        $this->assertArrayHasKey('object', $shipperResponse);
        $this->assertNotEmpty($shipperResponse['object']);
        $this->assertCount(1, $shipperResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $vendorResponse);
        $this->assertEquals('success', $vendorResponse['result']);

        $this->assertArrayHasKey('message', $vendorResponse);

        $this->assertArrayHasKey('object', $vendorResponse);
        $this->assertNotEmpty($vendorResponse['object']);
        $this->assertCount(1, $vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
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

        $newPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertEquals('success', $newPackage['result']);

        $this->assertArrayHasKey('message', $newPackage);

        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotEmpty($newPackage['object']);
        $this->assertEquals(4, $newPackage['object']['numberOfPackages']);
        $this->assertEquals('testPackage', $newPackage['object']['trackingNumber']);
        $this->assertEquals('testPackageShipper', $newPackage['object']['shipper']['name']);
        $this->assertEquals('testPackageReceiver', $newPackage['object']['receiver']['name']);
        $this->assertEquals('testPackageVendor', $newPackage['object']['vendor']['name']);

        // Assert that package is delivered
        $client->request('PUT', '/package/' . $newPackage['object']['trackingNumber'] . '/deliver');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $deliveredPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $deliveredPackage);
        $this->assertEquals('success', $deliveredPackage['result']);

        $this->assertArrayHasKey('message', $deliveredPackage);

        $this->assertArrayHasKey('object', $deliveredPackage);
        $this->assertNotEmpty($deliveredPackage['object']);
        $this->assertEquals(4, $deliveredPackage['object']['numberOfPackages']);
        $this->assertEquals('testPackage', $deliveredPackage['object']['trackingNumber']);
        $this->assertEquals(1, $deliveredPackage['object']['delivered']);


        $package = $newPackage['object'];

        # Testing response code for /package/{id}/delete
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $packageDeleted = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $packageDeleted);
        $this->assertArrayHasKey('message', $packageDeleted);
        $this->assertArrayHasKey('object', $packageDeleted);
        $this->assertNotEmpty($packageDeleted['object']);
        $this->assertEquals('success', $packageDeleted['result']);

        // Search database for existing entities and delete them
        $client->request('GET', '/receiver/search', array(
            "term" => "testPackageReceiver"
        ));

        $receiverResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('GET', '/shipper/search', array(
            "term" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('GET', '/vendor/search', array(
            "term" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testPickUpPackageRoute() {
        $client = static::createClient();

        // Setting up the database with fake entities
        $client->request('POST', '/receiver/new', array(
            "name" => "testPackageReceiver",
            "deliveryRoom" => 112
        ));

        // Assert that receiver was successfully created
        $receiverResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotEmpty($receiverResponse['object']);
        $this->assertCount(1, $receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $shipperResponse);
        $this->assertEquals('success', $shipperResponse['result']);

        $this->assertArrayHasKey('message', $shipperResponse);

        $this->assertArrayHasKey('object', $shipperResponse);
        $this->assertNotEmpty($shipperResponse['object']);
        $this->assertCount(1, $shipperResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $vendorResponse);
        $this->assertEquals('success', $vendorResponse['result']);

        $this->assertArrayHasKey('message', $vendorResponse);

        $this->assertArrayHasKey('object', $vendorResponse);
        $this->assertNotEmpty($vendorResponse['object']);
        $this->assertCount(1, $vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
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

        $newPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertEquals('success', $newPackage['result']);

        $this->assertArrayHasKey('message', $newPackage);

        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotEmpty($newPackage['object']);
        $this->assertEquals(4, $newPackage['object']['numberOfPackages']);
        $this->assertEquals('testPackage', $newPackage['object']['trackingNumber']);
        $this->assertEquals('testPackageShipper', $newPackage['object']['shipper']['name']);
        $this->assertEquals('testPackageReceiver', $newPackage['object']['receiver']['name']);
        $this->assertEquals('testPackageVendor', $newPackage['object']['vendor']['name']);


        // Assert that package is picked up
        $client->request('PUT', '/package/' . $newPackage['object']['trackingNumber'] . '/pickup', array(
            "userWhoPickedUp"=> "stuffedchickenwings"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $pickedUpPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $pickedUpPackage);
        $this->assertEquals('success', $pickedUpPackage['result']);

        $this->assertArrayHasKey('message', $pickedUpPackage);

        $this->assertArrayHasKey('object', $pickedUpPackage);
        $this->assertNotEmpty($pickedUpPackage['object']);
        $this->assertEquals('testPackage', $pickedUpPackage['object']['trackingNumber']);
        $this->assertEquals(1, $pickedUpPackage['object']['pickedUp']);
        $this->assertEquals('stuffedchickenwings', $pickedUpPackage['object']['userWhoPickedUp']);


        $package = $newPackage['object'];

        # Testing response code for /package/{id}/delete
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $packageDeleted = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $packageDeleted);
        $this->assertArrayHasKey('message', $packageDeleted);
        $this->assertArrayHasKey('object', $packageDeleted);
        $this->assertNotEmpty($packageDeleted['object']);
        $this->assertEquals('success', $packageDeleted['result']);

        // Search database for existing entities and delete them
        $client->request('GET', '/receiver/search', array(
            "term" => "testPackageReceiver"
        ));

        $receiverResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('GET', '/shipper/search', array(
            "term" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('GET', '/vendor/search', array(
            "term" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testSearchPackageRoute() {
        $client = static::createClient();

        // Setting up the database with fake entities
        $client->request('POST', '/receiver/new', array(
            "name" => "testPackageReceiver",
            "deliveryRoom" => 112
        ));

        // Assert that receiver was successfully created
        $receiverResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotEmpty($receiverResponse['object']);
        $this->assertCount(1, $receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $shipperResponse);
        $this->assertEquals('success', $shipperResponse['result']);

        $this->assertArrayHasKey('message', $shipperResponse);

        $this->assertArrayHasKey('object', $shipperResponse);
        $this->assertNotEmpty($shipperResponse['object']);
        $this->assertCount(1, $shipperResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $vendorResponse);
        $this->assertEquals('success', $vendorResponse['result']);

        $this->assertArrayHasKey('message', $vendorResponse);

        $this->assertArrayHasKey('object', $vendorResponse);
        $this->assertNotEmpty($vendorResponse['object']);
        $this->assertCount(1, $vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
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

        $newPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertEquals('success', $newPackage['result']);

        $this->assertArrayHasKey('message', $newPackage);

        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotEmpty($newPackage['object']);
        $this->assertEquals(4, $newPackage['object']['numberOfPackages']);
        $this->assertEquals('testPackage', $newPackage['object']['trackingNumber']);
        $this->assertEquals('testPackageShipper', $newPackage['object']['shipper']['name']);
        $this->assertEquals('testPackageReceiver', $newPackage['object']['receiver']['name']);
        $this->assertEquals('testPackageVendor', $newPackage['object']['vendor']['name']);

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

        $searchPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $searchPackage);
        $this->assertArrayHasKey('message', $searchPackage);
        $this->assertArrayHasKey('object', $searchPackage);
        $this->assertNotEmpty($searchPackage['object']);
        $this->assertEquals('success', $searchPackage['result']);
        $this->assertEquals('testPackage', $searchPackage['object'][0]['trackingNumber']);

        // Test for errors
        $client->request('GET', '/package/search', array(
            "term" => "stuffedchickenwings"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $errorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertArrayHasKey('message', $errorResponse);
        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertEmpty($errorResponse['object']);
        $this->assertEquals('error', $errorResponse['result']);

        $package = $newPackage['object'];

        # Testing response code for /package/{id}/delete
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $packageDeleted = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $packageDeleted);
        $this->assertArrayHasKey('message', $packageDeleted);
        $this->assertArrayHasKey('object', $packageDeleted);
        $this->assertNotEmpty($packageDeleted['object']);
        $this->assertEquals('success', $packageDeleted['result']);

        // Search database for existing entities and delete them
        $client->request('GET', '/receiver/search', array(
            "term" => "testPackageReceiver"
        ));

        $receiverResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('GET', '/shipper/search', array(
            "term" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('GET', '/vendor/search', array(
            "term" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testDeletePackageRoute() {
        $client = static::createClient();

        // Setting up the database with fake entities
        $client->request('POST', '/receiver/new', array(
            "name" => "testPackageReceiver",
            "deliveryRoom" => 112
        ));

        // Assert that receiver was successfully created
        $receiverResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotEmpty($receiverResponse['object']);
        $this->assertCount(1, $receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $shipperResponse);
        $this->assertEquals('success', $shipperResponse['result']);

        $this->assertArrayHasKey('message', $shipperResponse);

        $this->assertArrayHasKey('object', $shipperResponse);
        $this->assertNotEmpty($shipperResponse['object']);
        $this->assertCount(1, $shipperResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $vendorResponse);
        $this->assertEquals('success', $vendorResponse['result']);

        $this->assertArrayHasKey('message', $vendorResponse);

        $this->assertArrayHasKey('object', $vendorResponse);
        $this->assertNotEmpty($vendorResponse['object']);
        $this->assertCount(1, $vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
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

        $newPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertEquals('success', $newPackage['result']);

        $this->assertArrayHasKey('message', $newPackage);

        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotEmpty($newPackage['object']);
        $this->assertEquals(4, $newPackage['object']['numberOfPackages']);
        $this->assertEquals('testPackage', $newPackage['object']['trackingNumber']);
        $this->assertEquals('testPackageShipper', $newPackage['object']['shipper']['name']);
        $this->assertEquals('testPackageReceiver', $newPackage['object']['receiver']['name']);
        $this->assertEquals('testPackageVendor', $newPackage['object']['vendor']['name']);

        $package = $newPackage['object'];

        # Testing response code for /package/{id}/delete
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $packageDeleted = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $packageDeleted);
        $this->assertArrayHasKey('message', $packageDeleted);
        $this->assertArrayHasKey('object', $packageDeleted);
        $this->assertNotEmpty($packageDeleted['object']);
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

        $errorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertArrayHasKey('message', $errorResponse);
        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertEmpty($errorResponse['object']);
        $this->assertEquals('error', $errorResponse['result']);

        // Search database for existing entities and delete them
        $client->request('GET', '/receiver/search', array(
            "term" => "testPackageReceiver"
        ));

        $receiverResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('GET', '/shipper/search', array(
            "term" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('GET', '/vendor/search', array(
            "term" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testGetPackagesRoute() {
        $client = static::createClient();

        // Setting up the database with fake entities
        $client->request('POST', '/receiver/new', array(
            "name" => "testPackageReceiver",
            "deliveryRoom" => 112
        ));

        // Assert that receiver was successfully created
        $receiverResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotEmpty($receiverResponse['object']);
        $this->assertCount(1, $receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('POST', '/shipper/new', array(
            "name" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $shipperResponse);
        $this->assertEquals('success', $shipperResponse['result']);

        $this->assertArrayHasKey('message', $shipperResponse);

        $this->assertArrayHasKey('object', $shipperResponse);
        $this->assertNotEmpty($shipperResponse['object']);
        $this->assertCount(1, $shipperResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('POST', '/vendor/new', array(
            "name" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $vendorResponse);
        $this->assertEquals('success', $vendorResponse['result']);

        $this->assertArrayHasKey('message', $vendorResponse);

        $this->assertArrayHasKey('object', $vendorResponse);
        $this->assertNotEmpty($vendorResponse['object']);
        $this->assertCount(1, $vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('POST', '/package/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
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

        $newPackage = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $newPackage);
        $this->assertEquals('success', $newPackage['result']);

        $this->assertArrayHasKey('message', $newPackage);

        $this->assertArrayHasKey('object', $newPackage);
        $this->assertNotEmpty($newPackage['object']);
        $this->assertEquals(4, $newPackage['object']['numberOfPackages']);
        $this->assertEquals('testPackage', $newPackage['object']['trackingNumber']);
        $this->assertEquals('testPackageShipper', $newPackage['object']['shipper']['name']);
        $this->assertEquals('testPackageReceiver', $newPackage['object']['receiver']['name']);
        $this->assertEquals('testPackageVendor', $newPackage['object']['vendor']['name']);

        $client->request('GET', '/package/' . $newPackage['object']['trackingNumber']);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $package = $newPackage['object'];

        # Testing response code for /package/{id}/delete
        $client->request('DELETE', '/package/' . $package['trackingNumber'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $packageDeleted = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $packageDeleted);
        $this->assertArrayHasKey('message', $packageDeleted);
        $this->assertArrayHasKey('object', $packageDeleted);
        $this->assertNotEmpty($packageDeleted['object']);
        $this->assertEquals('success', $packageDeleted['result']);

        // Search database for existing entities and delete them
        $client->request('GET', '/receiver/search', array(
            "term" => "testPackageReceiver"
        ));

        $receiverResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $receiver = $receiverResponse['object'];

        $client->request('GET', '/shipper/search', array(
            "term" => "testPackageShipper"
        ));

        $shipperResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($receiverResponse['object']);

        $shipper = $shipperResponse['object'];

        $client->request('GET', '/vendor/search', array(
            "term" => "testPackageVendor"
        ));

        $vendorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($vendorResponse['object']);

        $vendor = $vendorResponse['object'];

        $client->request('DELETE', '/receiver/' . $receiver[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/shipper/' . $shipper[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('DELETE', '/vendor/' . $vendor[0]['id'] . '/delete');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}