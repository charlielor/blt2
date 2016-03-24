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

        # Testing response code for /vendor/search
        $client->request('GET', '/vendor/search');
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Testing response code for /vendor/search
        $client->request('GET', '/receiver/search');
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Testing response code for /shipper/search
        $client->request('GET', '/shipper/search');
        $this->assertTrue($client->getResponse()->isSuccessful());

    # Testing response code for /user/search
        $client->request('GET', '/user/search');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}