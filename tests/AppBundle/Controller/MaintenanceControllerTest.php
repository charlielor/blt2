<?php


namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MaintenanceControllerTest extends WebTestCase
{
    public function testRoute() {
        $client = static::createClient();

        $client->request('GET', '/maintenance');

        # Testing response code for /maintenance
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRenderTemplateAction() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/maintenance');
        
        # Testing response code for /maintenance
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Back to Menu link
        $client->click($crawler->selectLink("Back to Menu")->link());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/', $client->getRequest()->getRequestUri());
        $client->back();
    }

    public function testSwitchRoute() {
        $client = static::createClient();

        $client->request('GET', '/maintenance/switch');

        # Testing response code for /maintenance
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testSearchRoute() {
        $client = static::createClient();

        $client->request('GET', '/maintenance/search');

        # Testing response code for /maintenance
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}