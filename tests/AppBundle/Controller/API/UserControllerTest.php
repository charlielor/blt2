<?php


namespace Tests\AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetAllUsersRoute() {
        $client = static::createClient();

        $client->request('GET', '/users');

        # Testing response code for /user/all
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testSearchUserRoute() {
        $client = static::createClient();

        $client->request('GET', '/user/search', array(
            "term" => "anon."
        ));

        // Testing response code for /user/search
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
        $client->request('GET', '/user/search', array(
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
        $client = static::createClient();

        $client->request('GET', '/user/like', array(
            "term" => "an"
        ));

        // Testing response code for /user/search
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
        $this->assertCount(2, $successResponse['object']);
        $this->assertEquals("anon.", $successResponse['object'][0]['username']);

        // Assert error
        $client->request('GET', '/user/like', array(
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