<?php


namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ViewControllerTest extends WebTestCase
{

    public function testViewPage() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/view');

        # Testing response code for /
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Back to Menu link
        $client->click($crawler->selectLink("Back to Menu")->link());
//        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/', $client->getRequest()->getRequestUri());
        $client->back();
    }
}