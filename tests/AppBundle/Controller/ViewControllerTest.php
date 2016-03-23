<?php


namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ViewControllerTest extends WebTestCase
{
    public function testRoute() {
        $client = static::createClient();

        $client->request('GET', '/view');

        # Testing response code for /view
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRenderTemplateAction() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/view');

        # Back to Menu link
        $client->click($crawler->selectLink("Back to Menu")->link());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/', $client->getRequest()->getRequestUri());
        $client->back();
    }
}