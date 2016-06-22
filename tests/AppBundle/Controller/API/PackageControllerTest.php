<?php


namespace Tests\AppBundle\Controller\API;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class PackageControllerTest extends WebTestCase
{
    // Set up database with fixtures
    public function setUp() {

        $em = $this->getContainer()->get('doctrine')->getManager();

        if (!isset($metadatas)) {
            $metadatas = $em->getMetadataFactory()->getAllMetadata();
        }

        $schemaTool = new SchemaTool($em);

        $schemaTool->dropDatabase();

        if (!empty($metadatas)) {
            $schemaTool->createSchema($metadatas);
        }

        $this->postFixtureSetup();

        $this->loadFixtures(array(
            'AppBundle\DataFixtures\ORM\LoadVendor',
            'AppBundle\DataFixtures\ORM\LoadShipper',
            'AppBundle\DataFixtures\ORM\LoadReceiver',
            'AppBundle\DataFixtures\ORM\LoadPackage',
        ));
    }
    
    public function testNewPackageRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        $client->request('POST', '/packages/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
            "shipperId" => 1,
            "receiverId" => 1,
            "vendorId" => 1
        ));

        // Testing response code for /packages/new
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
        $this->assertEquals('fixtureShipper', $newPackage['object']['shipper']['name']);
        $this->assertEquals('fixtureReceiver', $newPackage['object']['receiver']['name']);
        $this->assertEquals('fixtureVendor', $newPackage['object']['vendor']['name']);

        // Testing against duplicates
        $client->request('POST', '/packages/new', array(
            "trackingNumber" => "testPackage",
            "numberOfPackages" => 4,
            "shipperId" => 1,
            "receiverId" => 1,
            "vendorId" => 1
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

        // Testing with errors
        $client->request('POST', '/packages/new', array(
            "trackingNumber" => "testPackage",
            "receiverId" => 1,
            "vendorId" => 1
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
    }

    public function testUpdatePackageRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Test for update
        $client->request('POST', '/packages/fixturePackage/update', array(
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
        $client->request('POST', '/packages/test/update', array(
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
    }

    public function testLikePackageRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Test search like term
        $client->request('GET', '/packages/like', array(
            "term" => "fixture"
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
        $this->assertCount(2, $searchPackage['object']);

        // Test for errors
        $client->request('GET', '/packages/search', array(
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
        $this->assertEquals('success', $errorResponse['result']);
    }
    
    public function testDeliverPackageRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that package is delivered
        $client->request('PUT', '/packages/fixturePackage/deliver');

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
        $this->assertEquals(7, $deliveredPackage['object']['numberOfPackages']);
        $this->assertEquals('fixturePackage', $deliveredPackage['object']['trackingNumber']);
        $this->assertEquals(1, $deliveredPackage['object']['delivered']);
    }

    public function testPickUpPackageRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();


        // Assert that package is picked up
        $client->request('PUT', '/packages/fixturePackage/pickup', array(
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
        $this->assertEquals('fixturePackage', $pickedUpPackage['object']['trackingNumber']);
        $this->assertEquals(1, $pickedUpPackage['object']['pickedUp']);
        $this->assertEquals('stuffedchickenwings', $pickedUpPackage['object']['userWhoPickedUp']);
    }

    public function testSearchPackageRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        $client->request('GET', '/packages/search', array(
            "term" => "fixturePackage"
        ));

        // Testing response code for /packages/search
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
        $this->assertEquals('fixturePackage', $searchPackage['object'][0]['trackingNumber']);

        // Test for errors
        $client->request('GET', '/packages/search', array(
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
        $this->assertEquals('success', $errorResponse['result']);
    }

    public function testDeletePackageRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Testing response code for /packages/{id}/delete
        $client->request('DELETE', '/packages/fixturePackage/delete');

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
        $client->request('DELETE', '/packages/fixturePackage/delete');

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
    }

    public function testGetPackagesRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        $client->request('GET', '/packages/fixturePackage');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}