<?php


namespace Tests\AppBundle\Controller\API;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class UserControllerTest extends WebTestCase
{
    // Set up database with fixtures

    public function setUp() {
        $em = $this->getContainer()->get('doctrine')->getManager();

        if (!isset($metadatas)) {
            $metadatas = $em->getMetadataFactory()->getAllMetadata();
        }

        $schemaTool = new SchemaTool($em);

        $schemaTool->dropDatabase();

        if (!empty($metadatas)) {
            $schemaTool->createSchema($metadatas);
        }
        $this->postFixtureSetup();

        $this->loadFixtures(array(
            'AppBundle\DataFixtures\ORM\LoadVendor',
            'AppBundle\DataFixtures\ORM\LoadShipper',
            'AppBundle\DataFixtures\ORM\LoadReceiver',
            'AppBundle\DataFixtures\ORM\LoadPackage',
        ));
    }

    public function testGetAllUsersRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        $client->request('GET', '/users');

        # Testing response code for /users/all
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testSearchUserRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        $client->request('GET', '/users/search', array(
            "term" => "anon."
        ));

        // Testing response code for /users/search
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotEmpty($successResponse['object']);
        $this->assertCount(1, $successResponse['object']);
        $this->assertEquals("anon.", $successResponse['object'][0]['username']);

        // Assert error
        $client->request('GET', '/users/search', array(
            "term" => "stuffedchickenwings"
        ));

        $errorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertEquals('success', $errorResponse['result']);

        $this->assertArrayHasKey('message', $errorResponse);

        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertEmpty($errorResponse['object']);
    }

    public function testLikeUserRoute() {
        echo __METHOD__ . "\n";

        $client = static::createClient();

        $client->request('GET', '/users/like', array(
            "term" => "an"
        ));

        // Testing response code for /users/search
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $successResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $successResponse);
        $this->assertEquals('success', $successResponse['result']);

        $this->assertArrayHasKey('message', $successResponse);

        $this->assertArrayHasKey('object', $successResponse);
        $this->assertNotEmpty($successResponse['object']);
        $this->assertCount(4, $successResponse['object']);
        $this->assertEquals("anon.", $successResponse['object'][0]['username']);

        // Assert error
        $client->request('GET', '/users/like', array(
            "term" => "stuffedchickenwings"
        ));

        $errorResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $errorResponse);
        $this->assertEquals('success', $errorResponse['result']);

        $this->assertArrayHasKey('message', $errorResponse);

        $this->assertArrayHasKey('object', $errorResponse);
        $this->assertEmpty($errorResponse['object']);
    }
}