<?php


namespace Tests\AppBundle\Controller\API;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class VendorControllerTest extends WebTestCase
{
    // 1 - fixtureVendor  -- enabled
    // 2 - fixtureVendor2 -- disabled
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
        ));
    }

    public function testNewVendorRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        $client->request('POST', '/vendors/new', array(
            "name" => "newVendor"
        ));

        // Assert that creating a new entity is successful
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotEmpty($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);
        $this->assertEquals("newVendor", $successResponse['object'][0]['name']);

        // Assert that creating an entity is unsuccessful, duplicate
        $client->request('POST', '/vendors/new', array(
            "name" => "fixtureVendor"
        ));

        $duplicateResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $duplicateResponse);
        $this->assertEquals('error', $duplicateResponse['result']);

        $this->assertArrayHasKey('message', $duplicateResponse);

        $this->assertArrayHasKey('object', $duplicateResponse);
        $this->assertEmpty($duplicateResponse['object']);

        // Assert that creating an entity is unsuccessful, entity disabled
        $client->request('POST', '/vendors/new', array(
            "name" => "fixtureVendor2"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $dupDisabledResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $dupDisabledResponse);
        $this->assertEquals('error', $dupDisabledResponse['result']);

        $this->assertArrayHasKey('message', $dupDisabledResponse);

        $this->assertArrayHasKey('object', $dupDisabledResponse);
        $this->assertEmpty($dupDisabledResponse['object']);
    }

    public function testSearchVendorRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that the entity was successfully found
        $client->request('GET', '/vendors/search', array(
            "term" => "fixtureVendor"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotEmpty($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);
        $this->assertEquals("fixtureVendor", $successResponse['object'][0]['name']);

        // Assert that given entity wasn't found
        $client->request('GET', '/vendors/search', array(
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
        $this->assertEquals('error', $errorResponse['result']);

        $this->assertArrayHasKey('message', $errorResponse);

        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertEmpty($errorResponse['object']);
    }

    public function testUpdateVendorRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that the entity was successfully updated
        $client->request('PUT', '/vendors/1/update', array(
            "name" => "fixtureVendorUpdated"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);
        $this->assertArrayHasKey('message', $successResponse);
        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotEmpty($successResponse['object']);
        $this->assertEquals('fixtureVendorUpdated', $successResponse['object'][0]['name']);

        // Assert that a entity that gets updated to another entity with the same name is an error
        $client->request('PUT', '/vendors/1/update', array(
            "name" => "fixtureVendor2"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $sameNameResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $sameNameResponse);
        $this->assertEquals('error', $sameNameResponse['result']);
        $this->assertArrayHasKey('message', $sameNameResponse);
        $this->assertArrayHasKey('object', $sameNameResponse);
        $this->assertEmpty($sameNameResponse['object']);

        // Assert that a entity that does not exist is not updated
        $client->request('PUT', '/vendors/stuffedchickenwings/update', array(
            "name" => "testUpdated"
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
        $this->assertEquals('error', $errorResponse['result']);

        $this->assertArrayHasKey('message', $errorResponse);

        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertEmpty($errorResponse['object']);
    }

    public function testEnableVendorRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that entity is successfully enabled
        $client->request('PUT', '/vendors/2/enable');

        // Testing response code for /vendors/{id}/enable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $enabledResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $enabledResponse);
        $this->assertEquals('success', $enabledResponse['result']);
        $this->assertArrayHasKey('message', $enabledResponse);
        $this->assertArrayHasKey('object', $enabledResponse);
        $this->assertNotEmpty($enabledResponse['object']);

        // Assert that enabling a entity with no id gives errors
        $client->request('PUT', '/vendors/stuffedchickenwings/enable');

        // Testing response code for /vendors/{id}/enable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $noIdErrorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $noIdErrorResponse);
        $this->assertEquals('error', $noIdErrorResponse['result']);

        $this->assertArrayHasKey('message', $noIdErrorResponse);

        $this->assertArrayHasKey('object', $noIdErrorResponse);
        $this->assertEmpty($noIdErrorResponse['object']);
    }

    public function testDisableVendorRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that entity is successfully disabled
        $client->request('PUT', '/vendors/2/disable');

        // Testing response code for /vendors/{id}/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $enabledResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $enabledResponse);
        $this->assertEquals('success', $enabledResponse['result']);

        $this->assertArrayHasKey('message', $enabledResponse);

        $this->assertArrayHasKey('object', $enabledResponse);
        $this->assertNotEmpty($enabledResponse['object']);

        // Assert that disabling a entity with no id gives errors
        $client->request('PUT', '/vendors/stuffedchickenwings/disable');

        // Testing response code for /vendors/{id}/enable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $noIdErrorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $noIdErrorResponse);
        $this->assertEquals('error', $noIdErrorResponse['result']);

        $this->assertArrayHasKey('message', $noIdErrorResponse);

        $this->assertArrayHasKey('object', $noIdErrorResponse);
        $this->assertEmpty($noIdErrorResponse['object']);
    }

    public function testLikeVendorRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('GET', '/vendors/like', array(
            "name" => "fixture"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);
        $this->assertArrayHasKey('message', $successResponse);
        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotEmpty($successResponse['object']);
        $this->assertCount(2, $successResponse['object']);

        // Assert that given entity wasn't found
        $client->request('GET', '/vendors/like', array(
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
        $this->assertEquals('error', $errorResponse['result']);

        $this->assertArrayHasKey('message', $errorResponse);

        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertEmpty($errorResponse['object']);
    }

//    public function testDeleteVendorRoute() {
//        echo __METHOD__ . "\n";
//
//        $client = static::createClient();
//
//        // Assert that entity was successfully deleted
//        $client->request('DELETE', '/vendors/2/delete');
//
//        $this->assertTrue($client->getResponse()->isSuccessful());
//
//        $this->assertTrue(
//            $client->getResponse()->headers->contains(
//                'Content-Type',
//                'application/json'
//            )
//        );
//
//        $deletedResponse = json_decode($client->getResponse()->getContent(), true);
//
//        $this->assertArrayHasKey('result', $deletedResponse);
//        $this->assertEquals('success', $deletedResponse['result']);
//
//        $this->assertArrayHasKey('message', $deletedResponse);
//
//        $this->assertArrayHasKey('object', $deletedResponse);
//        $this->assertNotEmpty($deletedResponse['object']);
//
//        // Assert that route with invalid id gives errors
//        $client->request('DELETE', '/vendors/stuffedchickenwings/delete');
//
//        $this->assertTrue($client->getResponse()->isSuccessful());
//
//        $this->assertTrue(
//            $client->getResponse()->headers->contains(
//                'Content-Type',
//                'application/json'
//            )
//        );
//
//        $errorResponse = json_decode($client->getResponse()->getContent(), true);
//
//        $this->assertArrayHasKey('result', $errorResponse);
//        $this->assertEquals('error', $errorResponse['result']);
//
//        $this->assertArrayHasKey('message', $errorResponse);
//
//        $this->assertArrayHasKey('object', $errorResponse);
//        $this->assertEmpty($errorResponse['object']);
//    }

    public function testAllVendorsRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert searching for entity returns something
        $client->request('GET', '/vendors');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testVendorRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that going to the entity's page is successful
        $client->request('GET', '/vendors/1');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}