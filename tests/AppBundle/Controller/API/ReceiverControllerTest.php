<?php


namespace Tests\AppBundle\Controller\API;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class ReceiverControllerTest extends WebTestCase
{
    // 1 - fixtureReceiver  -- enabled
    // 2 - fixtureReceiver2 -- disabled
    // 3 - fixtureReceiver3 -- no packages
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
            'AppBundle\DataFixtures\ORM\LoadReceiver',
        ));
    }

    public function testNewReceiverRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        $client->request('POST', '/receivers/new', array(
            "name" => "newReceiver",
            "deliveryRoom" => 1212
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
        $this->assertEquals("newReceiver", $successResponse['object'][0]['name']);
        $this->assertEquals(1212, $successResponse['object'][0]['deliveryRoom']);

        // Assert that creating an entity is unsuccessful, duplicate
        $client->request('POST', '/receivers/new', array(
            "name" => "fixtureReceiver"
        ));

        $duplicateResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $duplicateResponse);
        $this->assertEquals('error', $duplicateResponse['result']);

        $this->assertArrayHasKey('message', $duplicateResponse);

        $this->assertArrayHasKey('object', $duplicateResponse);
        $this->assertEmpty($duplicateResponse['object']);

        // Assert that creating an entity is unsuccessful, entity disabled
        $client->request('POST', '/receivers/new', array(
            "name" => "fixtureReceiver2",
            "deliveryRoom" => 1212
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

    public function testSearchReceiverRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that the entity was successfully found
        $client->request('GET', '/receivers/search', array(
            "term" => "fixtureReceiver"
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
        $this->assertEquals("fixtureReceiver", $successResponse['object'][0]['name']);

        // Assert that given entity wasn't found
        $client->request('GET', '/receivers/search', array(
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

    public function testUpdateReceiverRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that the entity was successfully updated
        $client->request('PUT', '/receivers/1/update', array(
            "name" => "fixtureReceiverUpdated",
            "deliveryRoom" => 1212
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
        $this->assertEquals('fixtureReceiverUpdated', $successResponse['object'][0]['name']);
        $this->assertEquals(1212, $successResponse['object'][0]['deliveryRoom']);

        // Assert that a entity that gets updated to another entity with the same name is an error
        $client->request('PUT', '/receivers/1/update', array(
            "name" => "fixtureReceiver2"
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
        $client->request('PUT', '/receivers/stuffedchickenwings/update', array(
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

    public function testEnableReceiverRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that entity is successfully enabled
        $client->request('PUT', '/receivers/2/enable');

        // Testing response code for /receivers/{id}/enable
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
        $client->request('PUT', '/receivers/stuffedchickenwings/enable');

        // Testing response code for /receivers/{id}/enable
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

    public function testDisableReceiverRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that entity is successfully disabled
        $client->request('PUT', '/receivers/2/disable');

        // Testing response code for /receivers/{id}/disable
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
        $client->request('PUT', '/receivers/stuffedchickenwings/disable');

        // Testing response code for /receivers/{id}/enable
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

    public function testLikeReceiverRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('GET', '/receivers/like', array(
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
        $client->request('GET', '/receivers/like', array(
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

    public function testDeleteReceiverRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that entity was successfully deleted
        $client->request('DELETE', '/receivers/2/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $deletedResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $deletedResponse);
        $this->assertEquals('success', $deletedResponse['result']);

        $this->assertArrayHasKey('message', $deletedResponse);

        $this->assertArrayHasKey('object', $deletedResponse);
        $this->assertNotEmpty($deletedResponse['object']);

        // Assert that route with invalid id gives errors
        $client->request('DELETE', '/receivers/stuffedchickenwings/delete');

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

    public function testAllReceiversRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert searching for entity returns something
        $client->request('GET', '/receivers');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testReceiverRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        // Assert that going to the entity's page is successful
        $client->request('GET', '/receivers/1');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testReceiverPackagesRoute() {
        echo __METHOD__ . "\n";

        // Override setUp()
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

        $client = static::createClient();

        // Assert that getting fixtureReceiver's packages gets one package
        $client->request('GET', '/receivers/packages', [
            "name" => "fixtureReceiver"
        ]);

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
        
        // Assert that getting fixtureReceiver3's packages return zero packages
        $client->request('GET', '/receivers/packages', [
            "name" => "fixtureReceiver3"
        ]);

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $emptyResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $emptyResponse);
        $this->assertEquals('success', $emptyResponse['result']);
        $this->assertArrayHasKey('message', $emptyResponse);
        $this->assertArrayHasKey('object', $emptyResponse);
        $this->assertEmpty($emptyResponse['object']);

        // Assert that getting fixtureReceiver2's packages returns disabled
        $client->request('GET', '/receivers/packages', [
            "name" => "fixtureReceiver2"
        ]);

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $disabledResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $disabledResponse);
        $this->assertEquals('error', $disabledResponse['result']);
        $this->assertArrayHasKey('message', $disabledResponse);
        $this->assertArrayHasKey('object', $disabledResponse);
        $this->assertEmpty($disabledResponse['object']);

        // Assert that getting a receiver that does not exist returns error
        $client->request('GET', '/receivers/packages', [
            "name" => "stuffedchickenwings"
        ]);

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

        // Assert that calling for receivers' packages without the right parameters returns error
        // Assert that getting a receiver that does not exist returns error
        $client->request('GET', '/receivers/packages');

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
}