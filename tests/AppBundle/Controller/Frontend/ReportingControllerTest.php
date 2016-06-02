<?php


namespace Tests\AppBundle\Controller\FrontEnd;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReportingControllerTest extends WebTestCase
{
    public function testRoute() {
        $client = static::createClient();

        $client->request('GET', '/reporting');

        # Testing response code for /reporting
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRenderTemplateAction() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/reporting');

        # Back to Menu link
        $client->click($crawler->selectLink("Back to Menu")->link());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/', $client->getRequest()->getRequestUri());
        $client->back();

        # Run a Report h3 header
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Run a Report")')->count());
    }

    public function testEntitySearchRoute() {
        $client = static::createClient();

        # Testing response code for /vendor/like
        $client->request('GET', '/vendor/like');
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Testing response code for /vendor/like
        $client->request('GET', '/receiver/like');
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Testing response code for /shipper/like
        $client->request('GET', '/shipper/like');
        $this->assertTrue($client->getResponse()->isSuccessful());

    # Testing response code for /user/like
        $client->request('GET', '/user/like');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}