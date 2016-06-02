<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppAvailabilityFunctionalTest extends WebTestCase
{

    /**
     * @dataProvider getURLProvider
     *
     * Test GET routes
     */
    public function testGETRouteIsSuccessful($url) {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function getURLProvider() {
        return array(
            array('/'),
            array('/receiving'),
            array('/delivering'),
            array('/view'),
            array('/maintenance'),
            array('/reporting'),
//            array('/dashboard'),
        );
    }
}