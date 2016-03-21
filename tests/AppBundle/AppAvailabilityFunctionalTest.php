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

            array('/download'),
            array('/preview'),

            array('/package/new'),
            array('/shipper/new'),
            array('/receiver/new'),
            array('/vendor/new'),

            array('/package/update'),
            array('/shipper/update'),
            array('/receiver/update'),
            array('/vendor/update'),

            array('/package/like'),
            array('/shipper/like'),
            array('/receiver/like'),
            array('/vendor/like'),
            array('/user/like'),

            array('/package/search'),
            array('/shipper/search'),
            array('/receiver/search'),
            array('/vendor/search'),
            array('/user/search'),

            array('/delivering'),
            array('/delivering/barcode'),
//            array('/dashboard'),
        );
    }
}