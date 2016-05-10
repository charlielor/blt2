<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReceiverControllerTest extends WebTestCase
{
    public function testNewReceiverRoute() {
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/receiver/new', array(
            "name" => "test",
            "deliveryRoom" => 112
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);

        // Assert that entity was unsuccessfully created, duplicate
        $client->request('POST', '/receiver/new', array(
            "name" => "test",
            "deliveryRoom" => 112
        ));

        $duplicateResponse = json_decode(json_decode($client->getResponse()->getContent()), true);
        $this->assertArrayHasKey('result', $duplicateResponse);
        $this->assertEquals('error', $duplicateResponse['result']);

        $this->assertArrayHasKey('message', $duplicateResponse);

        $this->assertArrayHasKey('object', $duplicateResponse);
        $this->assertNull($duplicateResponse['object']);

        // Assert that given entity is disabled, display error
        $client->request('PUT', '/receiver/' . $successResponse["object"]["id"] . '/disable');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $disabledResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $disabledResponse);
        $this->assertEquals('success', $disabledResponse['result']);

        $this->assertArrayHasKey('message', $disabledResponse);

        $this->assertArrayHasKey('object', $disabledResponse);
        $this->assertNotNull($disabledResponse['object']);

        // Assert that receiver was unsuccessfully created, entity disabled
        $client->request('POST', '/receiver/new', array(
            "name" => "test",
            "deliveryRoom" => 112
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $dupDisabledResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $dupDisabledResponse);
        $this->assertEquals('error', $dupDisabledResponse['result']);

        $this->assertArrayHasKey('message', $dupDisabledResponse);

        $this->assertArrayHasKey('object', $dupDisabledResponse);
        $this->assertNull($dupDisabledResponse['object']);

        // Search for entity
        $client->request('GET', '/receiver/search', array(
            "term" => $successResponse['object']['name']
        ));

        $entity = json_decode(json_decode($client->getResponse()->getContent()), true);


        // Assert that receiver was successfully deleted
        $client->request('DELETE', '/receiver/' . $entity['object']['id'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $deletedResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $deletedResponse);
        $this->assertEquals('success', $deletedResponse['result']);

        $this->assertArrayHasKey('message', $deletedResponse);

        $this->assertArrayHasKey('object', $deletedResponse);
        $this->assertNotNull($deletedResponse['object']);
    }

    public function testUpdateReceiverRoute() {
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/receiver/new', array(
            "name" => "test",
            "deliveryRoom" => 112
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);

        // Assert that the entity was successfully updated
        $client->request('PUT', '/receiver/' . $successResponse['object']['id'] . '/update', array(
            "name" => "testUpdated",
            "deliveryRoom" => 1212
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);

        $this->assertEquals('testUpdated', $successResponse['object']['name']);
        $this->assertEquals('1212', $successResponse['object']['deliveryRoom']);

        // Assert that a entity that does not exist is not updated
        // Assert that the entity was successfully updated
        $client->request('PUT', '/receiver/stuffedchickenwings/update', array(
            "name" => "testUpdated",
            "deliveryRoom" => 1212
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('error', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNull($successResponse['object']);

        // Assert that receiver was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object']['id'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $deletedResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $deletedResponse);
        $this->assertEquals('success', $deletedResponse['result']);

        $this->assertArrayHasKey('message', $deletedResponse);

        $this->assertArrayHasKey('object', $deletedResponse);
        $this->assertNotNull($deletedResponse['object']);
    }

    public function testEnableReceiverRoute() {
        $client = static::createClient();

        $client->request('PUT', '/receiver/0/enable');

        # Testing response code for /receiver/1/enable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testDisableReceiverRoute() {
        $client = static::createClient();

        $client->request('PUT', '/receiver/1/disable');

        # Testing response code for /receiver/1/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testSearchReceiverRoute() {
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/receiver/new', array(
            "name" => "test",
            "deliveryRoom" => 112
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);

        // Assert that the entity was successfully found
        $client->request('GET', '/receiver/search', array(
            "term" => "test"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that given entity wasn't found
        $client->request('GET', '/receiver/search', array(
            "term" => "stuffedchickenwings"
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $receiverErrorResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $receiverErrorResponse);
        $this->assertEquals('error', $receiverErrorResponse['result']);

        $this->assertArrayHasKey('message', $receiverErrorResponse);

        $this->assertArrayHasKey('object', $receiverErrorResponse);
        $this->assertNull($successResponse['object']);

        // Assert that receiver was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object']['id'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $deletedResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $deletedResponse);
        $this->assertEquals('success', $deletedResponse['result']);

        $this->assertArrayHasKey('message', $deletedResponse);

        $this->assertArrayHasKey('object', $deletedResponse);
        $this->assertNotNull($deletedResponse['object']);
    }

    public function testDeleteReceiverRoute() {
        $client = static::createClient();

        $client->request('PUT', '/receiver/1/delete');

        # Testing response code for /receiver/1/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }
}