<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetAllUsersRoute() {
        $client = static::createClient();

        $client->request('GET', '/users');

        # Testing response code for /user/all
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testSearchUserRoute() {
        $client = static::createClient();

        $client->request('GET', '/user/search', array(
            "term" => ""
        ));

        # Testing response code for /user/search
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        // Assert error
        $client->request('GET', '/user/search', array(
            "term" => "stuffedchickenwings"
        ));
    }
}