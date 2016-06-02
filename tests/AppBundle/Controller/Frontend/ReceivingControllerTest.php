<?php


namespace Tests\AppBundle\Controller\FrontEnd;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReceivingControllerTest extends WebTestCase {
    public function testRoute() {
        $client = static::createClient();

        $client->request('GET', '/receiving');

        # Testing response code for /maintenance
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRenderTemplateAction() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/receiving');

        # Testing response code for /maintenance
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Back to Menu link
        $client->click($crawler->selectLink("Back to Menu")->link());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/', $client->getRequest()->getRequestUri());
        $client->back();

        # Shipper Modal
        $client->click($crawler->selectLink("Select Shipper")->link());
    }
}