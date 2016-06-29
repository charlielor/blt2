<?php

namespace Tests\AppBundle\Controller\API;


use Liip\FunctionalTestBundle\Test\WebTestCase;

class BaseControllerTest extends WebTestCase {
    public function testLogout() {
        $client = static::createClient();
        $client->request("GET", "logout");

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}