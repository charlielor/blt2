<?php

namespace Tests\AppBundle\Controller\FrontEnd;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeliveringControllerTest extends WebTestCase {

    public function testRoute() {
        $client = static::createClient();

        $client->request('GET', '/delivering');

        # Testing response code for /
        $this->assertTrue($client->getResponse()->isSuccessful());
    }


    public function testRenderTemplateAction() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/delivering');

        # Back to Menu link
        $client->click($crawler->selectLink("Back to menu")->link());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/', $client->getRequest()->getRequestUri());
        $client->back();
    }
}