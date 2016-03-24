<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PackageControllerTest extends WebTestCase
{
    public function testNewPackageRoute() {
        $client = static::createClient();

        $client->request('POST', '/package/new', array(
            "name" => "testPackage",
            "deliveryRoom" => 111
        ));

        # Testing response code for /package/new
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testUpdatePackageRoute() {
        $client = static::createClient();

        $client->request('PUT', '/package/0/update', array(
            "name" => "updatePackage",
            "deliveryRoom" => 1212
        ));

        # Testing response code for /package/update
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testEnablePackageRoute() {
        $client = static::createClient();

        $client->request('PUT', '/package/0/enable');

        # Testing response code for /package/0/enable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testDisablePackageRoute() {
        $client = static::createClient();

        $client->request('PUT', '/package/0/disable');

        # Testing response code for /package/0/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testDeletePackageRoute() {
        $client = static::createClient();

        $client->request('PUT', '/package/0/delete');

        # Testing response code for /package/0/disable
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testSearchPackageRoute() {
        $client = static::createClient();

        $client->request('GET', '/package/search', array(
            "term" => "test"
        ));

        # Testing response code for /package/search
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }
}