<?php


namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MaintenanceControllerTest extends WebTestCase
{
    public function testMaintenancePage() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/maintenance');
        var_dump($client->getResponse());
        # Testing response code for /maintenance
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Back to Menu link
        $client->click($crawler->selectLink("Back to Menu")->link());
//        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/', $client->getRequest()->getRequestUri());
        $client->back();
    }
}