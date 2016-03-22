<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuControllerTest extends WebTestCase {

    
    public function testMenu() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        # Testing response code for /
        $this->assertTrue($client->getResponse()->isSuccessful());

        # View link
        $client->click($crawler->selectLink("View Packages")->link());
//        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/view', $client->getRequest()->getRequestUri());
        $client->back();

        # Maintenance link
        $client->click($crawler->selectLink("Maintenance")->link());
//        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/maintenance', $client->getRequest()->getRequestUri());
        $client->back();

        # Reporting link
        $client->click($crawler->selectLink("Reporting")->link());
//        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/reporting', $client->getRequest()->getRequestUri());
        $client->back();

        # Receiving link
        $client->click($crawler->selectLink("Receiving")->link());
//        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/receiving', $client->getRequest()->getRequestUri());
        $client->back();

        # Delivering link
        $client->click($crawler->selectLink("Delivering")->link());
//        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/delivering', $client->getRequest()->getRequestUri());
    }
}