<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReceiverControllerTest extends WebTestCase
{
    public function testNewReceiverRoute() {
        $client = static::createClient();

        $client->request('POST', '/receiver/new', array(
            "name" => "testReceiver",
            "deliveryRoom" => 112
        ));

        # Testing response code for /receiver/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        // Assert that receiver was successfully created
        $receiverResponse = json_decode(json_decode($client->getResponse()->getContent()), true);

        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotNull($receiverResponse['object']);

        $client->request('POST', '/receiver/new', array(
            "name" => "testReceiver",
            "deliveryRoom" => 112
        ));

        // Assert that receiver was unsuccessfully created
        $receiverResponse = json_decode(json_decode($client->getResponse()->getContent()), true);
        $this->assertArrayHasKey('result', $receiverResponse);
        $this->assertEquals('success', $receiverResponse['result']);

        $this->assertArrayHasKey('message', $receiverResponse);

        $this->assertArrayHasKey('object', $receiverResponse);
        $this->assertNotNull($receiverResponse['object']);
    }

    public function testUpdateReceiverRoute() {
        $client = static::createClient();

        $client->request('PUT', '/receiver/0/update', array(
            "name" => "updateReceiver",
            "deliveryRoom" => 1212
        ));

        # Testing response code for /receiver/update
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
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

        $client->request('GET', '/receiver/search', array(
            "term" => "test"
        ));

        # Testing response code for /receiver/search
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
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