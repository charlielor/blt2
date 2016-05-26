<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VendorControllerTest extends WebTestCase
{
    public function testNewVendorRoute() {
        $client = static::createClient();

        $client->request('POST', '/vendor/new', array(
            "name" => "test"
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

        // Assert that entity was unsuccessfully created, duplicate
        $client->request('POST', '/vendor/new', array(
            "name" => "test"
        ));

        $duplicateResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('result', $duplicateResponse);
        $this->assertEquals('error', $duplicateResponse['result']);

        $this->assertArrayHasKey('message', $duplicateResponse);

        $this->assertArrayHasKey('object', $duplicateResponse);
        $this->assertEmpty($duplicateResponse['object']);

        // Assert that given entity is disabled, display error
        $client->request('PUT', '/vendor/' . $successResponse["object"][0]["id"] . '/disable');

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
        $this->assertNotEmpty($disabledResponse['object']);

        // Assert that vendor was unsuccessfully created, entity disabled
        $client->request('POST', '/vendor/new', array(
            "name" => "test"
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

        // Re-enable vendor
        // Assert that given entity is disabled, display error
        $client->request('PUT', '/vendor/' . $successResponse["object"][0]["id"] . '/enable');

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

        // Assert that vendor was successfully deleted
        $client->request('DELETE', '/vendor/' . $successResponse['object'][0]['id'] . '/delete');

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
    }

    public function testUpdateVendorRoute() {
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/vendor/new', array(
            "name" => "test"
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

        // Assert that the entity was successfully updated
        $client->request('PUT', '/vendor/' . $successResponse['object'][0]['id'] . '/update', array(
            "name" => "testUpdated"
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

        $this->assertEquals('testUpdated', $successResponse['object'][0]['name']);

        // Assert that a entity that gets updated to another entity with the same name is an error
        $client->request('PUT', '/vendor/' . $successResponse['object'][0]['id'] . '/update', array(
            "name" => "testUpdated"
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
        $client->request('PUT', '/vendor/stuffedchickenwings/update', array(
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

        // Assert that vendor was successfully deleted
        $client->request('DELETE', '/vendor/' . $successResponse['object'][0]['id'] . '/delete');

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
    }

    public function testEnableVendorRoute() {
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/vendor/new', array(
            "name" => "test",
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

        // Assert that entity is successfully enabled
        $client->request('PUT', '/vendor/'. $successResponse['object'][0]['id']. '/enable');

        // Testing response code for /vendor/{id}/enable
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
        $client->request('PUT', '/vendor/stuffedchickenwings/enable');

        // Testing response code for /vendor/{id}/enable
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

        // Assert that vendor was successfully deleted
        $client->request('DELETE', '/vendor/' . $successResponse['object'][0]['id'] . '/delete');

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
        $this->assertNotEmpty($deletedResponse['object']);
    }

    public function testDisableVendorRoute() {
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/vendor/new', array(
            "name" => "test"
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

        // Assert that entity is successfully disabled
        $client->request('PUT', '/vendor/'. $successResponse['object'][0]['id']. '/disable');

        // Testing response code for /vendor/{id}/disable
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
        $client->request('PUT', '/vendor/stuffedchickenwings/disable');

        // Testing response code for /vendor/{id}/enable
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

        // Assert that vendor was successfully deleted
        $client->request('DELETE', '/vendor/' . $successResponse['object'][0]['id'] . '/delete');

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
        $this->assertNotEmpty($deletedResponse['object']);
    }

    public function testSearchVendorRoute() {
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/vendor/new', array(
            "name" => "test"
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

        // Assert that the entity was successfully found
        $client->request('GET', '/vendor/search', array(
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
        $this->assertNotEmpty($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that given entity wasn't found
        $client->request('GET', '/vendor/search', array(
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

        // Assert that vendor was successfully deleted
        $client->request('DELETE', '/vendor/' . $successResponse['object'][0]['id'] . '/delete');

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
    }

    public function testLikeVendorRoute() {
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/vendor/new', array(
            "name" => "test"
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

        // Assert that the entity was successfully found
        $client->request('GET', '/vendor/like', array(
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
        $this->assertNotEmpty($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);

        // Assert that given entity wasn't found
        $client->request('GET', '/vendor/like', array(
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

        // Assert that vendor was successfully deleted
        $client->request('DELETE', '/vendor/' . $successResponse['object'][0]['id'] . '/delete');

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
    }

    public function testDeleteVendorRoute() {
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/vendor/new', array(
            "name" => "test"
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

        // Assert that entity was successfully deleted
        $client->request('DELETE', '/vendor/' . $successResponse['object'][0]['id'] . '/delete');

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
        $client->request('DELETE', '/vendor/stuffedchickenwings/delete');

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

    public function testAllVendorsRoute() {
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
        $client = static::createClient();

        // Assert that entity was successfully created
        $client->request('POST', '/vendor/new', array(
            "name" => "test"
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

        // Assert that going to the entity's page is successful
        $client->request('GET', '/vendor/' . $successResponse['object'][0]['id']);

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Assert that entity was successfully deleted
        $client->request('DELETE', '/vendor/' . $successResponse['object'][0]['id'] . '/delete');

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
    }
}