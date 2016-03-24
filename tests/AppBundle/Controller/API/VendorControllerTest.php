<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VendorControllerTest extends WebTestCase
{
    public function testNewVendorRoute() {
        $client = static::createClient();

        $client->request('POST', '/vendor/new', array(
            "name" => "testVendor"
        ));

        # Testing response code for /vendor/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testUpdateVendorRoute() {
        $client = static::createClient();

        $client->request('PUT', '/vendor/0/update', array(
            "name" => "updateVendor"
        ));

        # Testing response code for /vendor/update
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testEnableVendorRoute() {
        $client = static::createClient();

        $client->request('PUT', '/vendor/0/enable');

        # Testing response code for /vendor/0/enable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testDisableVendorRoute() {
        $client = static::createClient();

        $client->request('PUT', '/vendor/0/disable');

        # Testing response code for /vendor/0/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testDeleteVendorRoute() {
        $client = static::createClient();

        $client->request('PUT', '/vendor/0/delete');

        # Testing response code for /vendor/0/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testSearchVendorRoute() {
        $client = static::createClient();

        $client->request('GET', '/vendor/search', array(
            "term" => "update"
        ));

        # Testing response code for /vendor/search
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }
}