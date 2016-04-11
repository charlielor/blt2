<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReceiverControllerTest extends WebTestCase
{
    public function testNewReceiverRoute() {
        $client = static::createClient();

        $client->request('POST', '/receiver/new', array(
            "name" => "testReceiver",
            "deliveryRoom" => 111
        ));

        # Testing response code for /receiver/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $client->request('POST', '/receiver/new', array(
            "name" => "testReceiver",
            "deliveryRoom" => 111
        ));

        # Testing response code for /receiver/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
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

//    public function testDeleteReceiverRoute() {
//        $client = static::createClient();
//
//        $client->request('PUT', '/receiver/1/delete');
//
//        # Testing response code for /receiver/1/disable
//        $this->assertTrue($client->getResponse()->isSuccessful());
//
//        $this->assertTrue(
//            $client->getResponse()->headers->contains(
//                'Content-Type',
//                'application/json'
//            )
//        );
//    }
}