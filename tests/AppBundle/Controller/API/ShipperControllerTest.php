<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShipperControllerTest extends WebTestCase
{
    public function testNewShipperRoute() {
        $client = static::createClient();

        $client->request('POST', '/shipper/new', array(
            "name" => "testShipper"
        ));

        # Testing response code for /shipper/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testUpdateShipperRoute() {
        $client = static::createClient();

        $client->request('PUT', '/shipper/0/update', array(
            "name" => "updateShipper"
        ));

        # Testing response code for /shipper/update
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testEnableShipperRoute() {
        $client = static::createClient();

        $client->request('PUT', '/shipper/0/enable');

        # Testing response code for /shipper/0/enable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testDisableShipperRoute() {
        $client = static::createClient();

        $client->request('PUT', '/shipper/0/disable');

        # Testing response code for /shipper/0/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testSearchShipperRoute() {
        $client = static::createClient();

        $client->request('GET', '/shipper/search', array(
            "term" => "update"
        ));

        # Testing response code for /shipper/search
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }
    
    public function testDeleteShipperRoute() {
        $client = static::createClient();

        $client->request('PUT', '/shipper/1/delete');

        # Testing response code for /shipper/0/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }
}