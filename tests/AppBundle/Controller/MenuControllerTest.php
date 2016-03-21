<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuControllerTest extends WebTestCase {

    
    public function testMenu() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        # Testing response code for menu
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        # Testing buttons in menu
        $this->assertContains('Receiving', $crawler->filter('#receiving')->text());
        $this->assertContains('Delivering', $crawler->filter('#delivering')->text());
        
    }
}