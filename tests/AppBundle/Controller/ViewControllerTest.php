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
    }
}