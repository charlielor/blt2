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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that entity was unsuccessfully created, duplicate
        $client->request('POST', '/receiver/new', array(
            "name" => "test",
            "deliveryRoom" => 112
        ));

        $duplicateResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $duplicateResponse);
        $this->assertEquals('error', $duplicateResponse['result']);

        $this->assertArrayHasKey('message', $duplicateResponse);

        $this->assertArrayHasKey('object', $duplicateResponse);
        $this->assertNull($duplicateResponse['object']);

        // Assert that given entity is disabled, display error
        $client->request('PUT', '/receiver/' . $successResponse["object"][0]["id"] . '/disable');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $disabledResponse = json_decode($client->getResponse()->getContent(), true);

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

        $dupDisabledResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $dupDisabledResponse);
        $this->assertEquals('error', $dupDisabledResponse['result']);

        $this->assertArrayHasKey('message', $dupDisabledResponse);

        $this->assertArrayHasKey('object', $dupDisabledResponse);
        $this->assertNull($dupDisabledResponse['object']);

        // Re-enable receiver
        // Assert that given entity is disabled, display error
        $client->request('PUT', '/receiver/' . $successResponse["object"][0]["id"] . '/enable');

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
        $this->assertNotNull($enabledResponse['object']);


        // Assert that receiver was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object'][0]['id'] . '/delete');

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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that the entity was successfully updated
        $client->request('PUT', '/receiver/' . $successResponse['object'][0]['id'] . '/update', array(
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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);

        $this->assertEquals('testUpdated', $successResponse['object'][0]['name']);
        $this->assertEquals('1212', $successResponse['object'][0]['deliveryRoom']);

        // Assert that a entity that gets updated to another entity with the same name is an error
        $client->request('PUT', '/receiver/' . $successResponse['object'][0]['id'] . '/update', array(
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

        $sameNameResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $sameNameResponse);
        $this->assertEquals('error', $sameNameResponse['result']);

        $this->assertArrayHasKey('message', $sameNameResponse);

        $this->assertArrayHasKey('object', $sameNameResponse);
        $this->assertNull($sameNameResponse['object']);

        // Assert that a entity that does not exist is not updated
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

        $errorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertEquals('error', $errorResponse['result']);

        $this->assertArrayHasKey('message', $errorResponse);

        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertNull($errorResponse['object']);

        // Assert that receiver was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object'][0]['id'] . '/delete');

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
        $this->assertNotNull($deletedResponse['object']);
    }


    public function testEnableReceiverRoute() {
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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that entity is successfully enabled
        $client->request('PUT', '/receiver/'. $successResponse['object'][0]['id']. '/enable');

        // Testing response code for /receiver/{id}/enable
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
        $this->assertNotNull($enabledResponse['object']);

        // Assert that enabling a entity with no id gives errors
        $client->request('PUT', '/receiver/stuffedchickenwings/enable');

        // Testing response code for /receiver/{id}/enable
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
        $this->assertNull($noIdErrorResponse['object']);

        // Assert that receiver was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object'][0]['id'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertCount(1, $successResponse['object']);

        $deletedResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $deletedResponse);
        $this->assertEquals('success', $deletedResponse['result']);

        $this->assertArrayHasKey('message', $deletedResponse);

        $this->assertArrayHasKey('object', $deletedResponse);
        $this->assertNotNull($deletedResponse['object']);
    }

    public function testDisableReceiverRoute() {
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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that entity is successfully disabled
        $client->request('PUT', '/receiver/'. $successResponse['object'][0]['id']. '/disable');

        // Testing response code for /receiver/{id}/disable
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
        $this->assertNotNull($enabledResponse['object']);

        // Assert that disabling a entity with no id gives errors
        $client->request('PUT', '/receiver/stuffedchickenwings/disable');

        // Testing response code for /receiver/{id}/enable
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
        $this->assertNull($noIdErrorResponse['object']);

        // Assert that receiver was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object'][0]['id'] . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertCount(1, $successResponse['object']);

        $deletedResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $deletedResponse);
        $this->assertEquals('success', $deletedResponse['result']);

        $this->assertArrayHasKey('message', $deletedResponse);

        $this->assertArrayHasKey('object', $deletedResponse);
        $this->assertNotNull($deletedResponse['object']);
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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);
        $this->assertEquals("test", $successResponse['object'][0]['name']);

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

        $errorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertEquals('error', $errorResponse['result']);

        $this->assertArrayHasKey('message', $errorResponse);

        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertNull($errorResponse['object']);

        // Assert that receiver was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object'][0]['id'] . '/delete');

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
        $this->assertNotNull($deletedResponse['object']);
    }

    public function testLikeReceiverRoute() {
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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that the entity was successfully found
        $client->request('GET', '/receiver/like', array(
            "term" => "te"
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
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);
        $this->assertEquals("test", $successResponse['object'][0]['name']);

        // Assert that given entity wasn't found
        $client->request('GET', '/receiver/like', array(
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
        $this->assertNull($errorResponse['object']);

        // Assert that receiver was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object'][0]['id'] . '/delete');

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
        $this->assertNotNull($deletedResponse['object']);
    }

    public function testDeleteReceiverRoute() {
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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that entity was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object'][0]['id'] . '/delete');

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
        $this->assertNotNull($deletedResponse['object']);
        
        // Assert that route with invalid id gives errors
        $client->request('DELETE', '/receiver/stuffedchickenwings/delete');

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
        $this->assertNull($errorResponse['object']);
    }

    public function testAllReceiversRoute() {
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

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotNull($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that going to the entity's page is successful
        $client->request('GET', '/receiver/' . $successResponse['object'][0]['id']);

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Assert that entity was successfully deleted
        $client->request('DELETE', '/receiver/' . $successResponse['object'][0]['id'] . '/delete');

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
        $this->assertNotNull($deletedResponse['object']);
    }

}