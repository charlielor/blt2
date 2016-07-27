<?php


namespace Tests\AppBundle\Controller\FrontEnd;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class ReportingControllerTest extends WebTestCase
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
        $client->click($crawler->selectLink("Back to menu")->link());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/', $client->getRequest()->getRequestUri());
        $client->back();

        # Run a Report h3 header
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Run a report")')->count());
    }

    public function testEntitySearchRoute() {
        $client = static::createClient();

        # Testing response code for /vendor/like
        $client->request('GET', '/vendors/like');
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Testing response code for /vendor/like
        $client->request('GET', '/receivers/like');
        $this->assertTrue($client->getResponse()->isSuccessful());

        # Testing response code for /shipper/like
        $client->request('GET', '/shippers/like');
        $this->assertTrue($client->getResponse()->isSuccessful());

    # Testing response code for /user/like
        $client->request('GET', '/users/like');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testReportingQueryRoute() {
        $client = static::createClient();

        // Test r-0-vendor-graph
        $client->request("GET", "reporting/query", [
            "request" => "r-0-vendor",
            "tokenId" => "1",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r0VendorGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r0VendorGraph);
        $this->assertEquals('success', $r0VendorGraph['result']);
        $this->assertArrayHasKey('message', $r0VendorGraph);
        $this->assertEquals('Successfully queried database', $r0VendorGraph['message']);
        $this->assertArrayHasKey('object', $r0VendorGraph);
        $this->assertNotEmpty($r0VendorGraph['object']);
        $this->assertEquals("r-0-vendor", $r0VendorGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r0VendorGraph['type']);

        // Test r-0-vendor-table
        $client->request("GET", "query", [
            "request" => "r-0-vendor",
            "tokenId" => "1",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r0VendorTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r0VendorTable);
        $this->assertEquals('success', $r0VendorTable['result']);
        $this->assertArrayHasKey('message', $r0VendorTable);
        $this->assertEquals('Successfully queried database', $r0VendorTable['message']);
        $this->assertArrayHasKey('object', $r0VendorTable);
        $this->assertNotEmpty($r0VendorTable['object']);
        $this->assertEquals("r-0-vendor", $r0VendorTable['requestedQuery']);
        $this->assertEquals("t-table", $r0VendorTable['type']);

        // Test r-0-vendor-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-0-vendor",
            "tokenId" => "1",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test r-1-user-graph
        $client->request("GET", "query", [
            "request" => "r-1-user",
            "tokenId" => "anon.",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r1UserGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r1UserGraph);
        $this->assertEquals('success', $r1UserGraph['result']);
        $this->assertArrayHasKey('message', $r1UserGraph);
        $this->assertEquals('Successfully queried database', $r1UserGraph['message']);
        $this->assertArrayHasKey('object', $r1UserGraph);
        $this->assertNotEmpty($r1UserGraph['object']);
        $this->assertEquals("r-1-user", $r1UserGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r1UserGraph['type']);

        // Test r-1-user-table
        $client->request("GET", "query", [
            "request" => "r-1-user",
            "tokenId" => "anon.",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r1UserTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r1UserTable);
        $this->assertEquals('success', $r1UserTable['result']);
        $this->assertArrayHasKey('message', $r1UserTable);
        $this->assertEquals('Successfully queried database', $r1UserTable['message']);
        $this->assertArrayHasKey('object', $r1UserTable);
        $this->assertNotEmpty($r1UserTable['object']);
        $this->assertEquals("r-1-user", $r1UserTable['requestedQuery']);
        $this->assertEquals("t-table", $r1UserTable['type']);

        // Test r-1-user-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-1-user",
            "tokenId" => "anon.",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test r-2-shipper-graph
        $client->request("GET", "query", [
            "request" => "r-2-shipper",
            "tokenId" => "1",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r2ReceiverGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r2ReceiverGraph);
        $this->assertEquals('success', $r2ReceiverGraph['result']);
        $this->assertArrayHasKey('message', $r2ReceiverGraph);
        $this->assertEquals('Successfully queried database', $r2ReceiverGraph['message']);
        $this->assertArrayHasKey('object', $r2ReceiverGraph);
        $this->assertNotEmpty($r2ReceiverGraph['object']);
        $this->assertEquals("r-2-shipper", $r2ReceiverGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r2ReceiverGraph['type']);

        // Test r-2-shipper-table
        $client->request("GET", "query", [
            "request" => "r-2-shipper",
            "tokenId" => "1",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r2ReceiverTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r2ReceiverTable);
        $this->assertEquals('success', $r2ReceiverTable['result']);
        $this->assertArrayHasKey('message', $r2ReceiverTable);
        $this->assertEquals('Successfully queried database', $r2ReceiverTable['message']);
        $this->assertArrayHasKey('object', $r2ReceiverTable);
        $this->assertNotEmpty($r2ReceiverTable['object']);
        $this->assertEquals("r-2-shipper", $r2ReceiverTable['requestedQuery']);
        $this->assertEquals("t-table", $r2ReceiverTable['type']);

        // Test r-2-shipper-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-2-shipper",
            "tokenId" => "1",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test d-0-receiver-graph
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "1",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d0ReceiverGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d0ReceiverGraph);
        $this->assertEquals('success', $d0ReceiverGraph['result']);
        $this->assertArrayHasKey('message', $d0ReceiverGraph);
        $this->assertEquals('Successfully queried database', $d0ReceiverGraph['message']);
        $this->assertArrayHasKey('object', $d0ReceiverGraph);
        $this->assertNotEmpty($d0ReceiverGraph['object']);
        $this->assertEquals("d-0-receiver", $d0ReceiverGraph['requestedQuery']);
        $this->assertEquals("g-graph", $d0ReceiverGraph['type']);

        // Test d-0-receiver-table
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "1",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d0ReceiverTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d0ReceiverTable);
        $this->assertEquals('success', $d0ReceiverTable['result']);
        $this->assertArrayHasKey('message', $d0ReceiverTable);
        $this->assertEquals('Successfully queried database', $d0ReceiverTable['message']);
        $this->assertArrayHasKey('object', $d0ReceiverTable);
        $this->assertNotEmpty($d0ReceiverTable['object']);
        $this->assertEquals("d-0-receiver", $d0ReceiverTable['requestedQuery']);
        $this->assertEquals("t-table", $d0ReceiverTable['type']);

        // Test d-0-receiver-downloadCSV
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "1",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test r-3-none-graph
        $client->request("GET", "query", [
            "request" => "r-3-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r3NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r3NoneGraph);
        $this->assertEquals('success', $r3NoneGraph['result']);
        $this->assertArrayHasKey('message', $r3NoneGraph);
        $this->assertEquals('Successfully queried database', $r3NoneGraph['message']);
        $this->assertArrayHasKey('object', $r3NoneGraph);
        $this->assertNotEmpty($r3NoneGraph['object']);
        $this->assertEquals("r-3-none", $r3NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r3NoneGraph['type']);

        // Test r-3-none-table
        $client->request("GET", "query", [
            "request" => "r-3-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r3NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r3NoneTable);
        $this->assertEquals('success', $r3NoneTable['result']);
        $this->assertArrayHasKey('message', $r3NoneTable);
        $this->assertEquals('Successfully queried database', $r3NoneTable['message']);
        $this->assertArrayHasKey('object', $r3NoneTable);
        $this->assertNotEmpty($r3NoneTable['object']);
        $this->assertEquals("r-3-none", $r3NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $r3NoneTable['type']);

        // Test r-3-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-3-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test d-1-none-graph
        $client->request("GET", "query", [
            "request" => "d-1-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d1NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d1NoneGraph);
        $this->assertEquals('success', $d1NoneGraph['result']);
        $this->assertArrayHasKey('message', $d1NoneGraph);
        $this->assertEquals('Successfully queried database', $d1NoneGraph['message']);
        $this->assertArrayHasKey('object', $d1NoneGraph);
        $this->assertNotEmpty($d1NoneGraph['object']);
        $this->assertEquals("d-1-none", $d1NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $d1NoneGraph['type']);

        // Test d-1-none-table
        $client->request("GET", "query", [
            "request" => "d-1-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d1NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d1NoneTable);
        $this->assertEquals('success', $d1NoneTable['result']);
        $this->assertArrayHasKey('message', $d1NoneTable);
        $this->assertEquals('Successfully queried database', $d1NoneTable['message']);
        $this->assertArrayHasKey('object', $d1NoneTable);
        $this->assertNotEmpty($d1NoneTable['object']);
        $this->assertEquals("d-1-none", $d1NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $d1NoneTable['type']);

        // Test d-1-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "d-1-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test p-0-none-graph
        $client->request("GET", "query", [
            "request" => "p-0-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $p0NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $p0NoneGraph);
        $this->assertEquals('success', $p0NoneGraph['result']);
        $this->assertArrayHasKey('message', $p0NoneGraph);
        $this->assertEquals('Successfully queried database', $p0NoneGraph['message']);
        $this->assertArrayHasKey('object', $p0NoneGraph);
        $this->assertNotEmpty($p0NoneGraph['object']);
        $this->assertEquals("p-0-none", $p0NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $p0NoneGraph['type']);

        // Test p-0-none-table
        $client->request("GET", "query", [
            "request" => "p-0-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $p0NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $p0NoneTable);
        $this->assertEquals('success', $p0NoneTable['result']);
        $this->assertArrayHasKey('message', $p0NoneTable);
        $this->assertEquals('Successfully queried database', $p0NoneTable['message']);
        $this->assertArrayHasKey('object', $p0NoneTable);
        $this->assertNotEmpty($p0NoneTable['object']);
        $this->assertEquals("p-0-none", $p0NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $p0NoneTable['type']);

        // Test p-0-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "p-0-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test o-0-none-graph
        $client->request("GET", "query", [
            "request" => "o-0-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $o0NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $o0NoneGraph);
        $this->assertEquals('success', $o0NoneGraph['result']);
        $this->assertArrayHasKey('message', $o0NoneGraph);
        $this->assertEquals('Successfully queried database', $o0NoneGraph['message']);
        $this->assertArrayHasKey('object', $o0NoneGraph);
        $this->assertNotEmpty($o0NoneGraph['object']);
        $this->assertEquals("o-0-none", $o0NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $o0NoneGraph['type']);

        // Test o-0-none-table
        $client->request("GET", "query", [
            "request" => "o-0-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $o0NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $o0NoneTable);
        $this->assertEquals('success', $o0NoneTable['result']);
        $this->assertArrayHasKey('message', $o0NoneTable);
        $this->assertEquals('Successfully queried database', $o0NoneTable['message']);
        $this->assertArrayHasKey('object', $o0NoneTable);
        $this->assertNotEmpty($o0NoneTable['object']);
        $this->assertEquals("o-0-none", $o0NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $o0NoneTable['type']);

        // Test o-0-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "o-0-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );
        
        ////// More than 22 days apart //////

        $morethan150DaysTimestamp = strtotime("-30 day");
        $morethan150Days = new \DateTime();
        $morethan150Days->setTimestamp($morethan150DaysTimestamp);
        
        // Test r-0-vendor-graph
        $client->request("GET", "query", [
            "request" => "r-0-vendor",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);
        
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r0VendorGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r0VendorGraph);
        $this->assertEquals('success', $r0VendorGraph['result']);
        $this->assertArrayHasKey('message', $r0VendorGraph);
        $this->assertEquals('Successfully queried database', $r0VendorGraph['message']);
        $this->assertArrayHasKey('object', $r0VendorGraph);
        $this->assertNotEmpty($r0VendorGraph['object']);
        $this->assertEquals("r-0-vendor", $r0VendorGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r0VendorGraph['type']);

        // Test r-0-vendor-table
        $client->request("GET", "query", [
            "request" => "r-0-vendor",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r0VendorTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r0VendorTable);
        $this->assertEquals('success', $r0VendorTable['result']);
        $this->assertArrayHasKey('message', $r0VendorTable);
        $this->assertEquals('Successfully queried database', $r0VendorTable['message']);
        $this->assertArrayHasKey('object', $r0VendorTable);
        $this->assertNotEmpty($r0VendorTable['object']);
        $this->assertEquals("r-0-vendor", $r0VendorTable['requestedQuery']);
        $this->assertEquals("t-table", $r0VendorTable['type']);

        // Test r-0-vendor-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-0-vendor",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test r-1-user-graph
        $client->request("GET", "query", [
            "request" => "r-1-user",
            "tokenId" => "anon.",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r1UserGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r1UserGraph);
        $this->assertEquals('success', $r1UserGraph['result']);
        $this->assertArrayHasKey('message', $r1UserGraph);
        $this->assertEquals('Successfully queried database', $r1UserGraph['message']);
        $this->assertArrayHasKey('object', $r1UserGraph);
        $this->assertNotEmpty($r1UserGraph['object']);
        $this->assertEquals("r-1-user", $r1UserGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r1UserGraph['type']);

        // Test r-1-user-table
        $client->request("GET", "query", [
            "request" => "r-1-user",
            "tokenId" => "anon.",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r1UserTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r1UserTable);
        $this->assertEquals('success', $r1UserTable['result']);
        $this->assertArrayHasKey('message', $r1UserTable);
        $this->assertEquals('Successfully queried database', $r1UserTable['message']);
        $this->assertArrayHasKey('object', $r1UserTable);
        $this->assertNotEmpty($r1UserTable['object']);
        $this->assertEquals("r-1-user", $r1UserTable['requestedQuery']);
        $this->assertEquals("t-table", $r1UserTable['type']);

        // Test r-1-user-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-1-user",
            "tokenId" => "anon.",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test r-2-shipper-graph
        $client->request("GET", "query", [
            "request" => "r-2-shipper",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r2ReceiverGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r2ReceiverGraph);
        $this->assertEquals('success', $r2ReceiverGraph['result']);
        $this->assertArrayHasKey('message', $r2ReceiverGraph);
        $this->assertEquals('Successfully queried database', $r2ReceiverGraph['message']);
        $this->assertArrayHasKey('object', $r2ReceiverGraph);
        $this->assertNotEmpty($r2ReceiverGraph['object']);
        $this->assertEquals("r-2-shipper", $r2ReceiverGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r2ReceiverGraph['type']);

        // Test r-2-shipper-table
        $client->request("GET", "query", [
            "request" => "r-2-shipper",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r2ReceiverTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r2ReceiverTable);
        $this->assertEquals('success', $r2ReceiverTable['result']);
        $this->assertArrayHasKey('message', $r2ReceiverTable);
        $this->assertEquals('Successfully queried database', $r2ReceiverTable['message']);
        $this->assertArrayHasKey('object', $r2ReceiverTable);
        $this->assertNotEmpty($r2ReceiverTable['object']);
        $this->assertEquals("r-2-shipper", $r2ReceiverTable['requestedQuery']);
        $this->assertEquals("t-table", $r2ReceiverTable['type']);

        // Test r-2-shipper-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-2-shipper",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test d-0-receiver-graph
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d0ReceiverGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d0ReceiverGraph);
        $this->assertEquals('success', $d0ReceiverGraph['result']);
        $this->assertArrayHasKey('message', $d0ReceiverGraph);
        $this->assertEquals('Successfully queried database', $d0ReceiverGraph['message']);
        $this->assertArrayHasKey('object', $d0ReceiverGraph);
        $this->assertNotEmpty($d0ReceiverGraph['object']);
        $this->assertEquals("d-0-receiver", $d0ReceiverGraph['requestedQuery']);
        $this->assertEquals("g-graph", $d0ReceiverGraph['type']);

        // Test d-0-receiver-table
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d0ReceiverTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d0ReceiverTable);
        $this->assertEquals('success', $d0ReceiverTable['result']);
        $this->assertArrayHasKey('message', $d0ReceiverTable);
        $this->assertEquals('Successfully queried database', $d0ReceiverTable['message']);
        $this->assertArrayHasKey('object', $d0ReceiverTable);
        $this->assertNotEmpty($d0ReceiverTable['object']);
        $this->assertEquals("d-0-receiver", $d0ReceiverTable['requestedQuery']);
        $this->assertEquals("t-table", $d0ReceiverTable['type']);

        // Test d-0-receiver-downloadCSV
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test r-3-none-graph
        $client->request("GET", "query", [
            "request" => "r-3-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r3NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r3NoneGraph);
        $this->assertEquals('success', $r3NoneGraph['result']);
        $this->assertArrayHasKey('message', $r3NoneGraph);
        $this->assertEquals('Successfully queried database', $r3NoneGraph['message']);
        $this->assertArrayHasKey('object', $r3NoneGraph);
        $this->assertNotEmpty($r3NoneGraph['object']);
        $this->assertEquals("r-3-none", $r3NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r3NoneGraph['type']);

        // Test r-3-none-table
        $client->request("GET", "query", [
            "request" => "r-3-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r3NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r3NoneTable);
        $this->assertEquals('success', $r3NoneTable['result']);
        $this->assertArrayHasKey('message', $r3NoneTable);
        $this->assertEquals('Successfully queried database', $r3NoneTable['message']);
        $this->assertArrayHasKey('object', $r3NoneTable);
        $this->assertNotEmpty($r3NoneTable['object']);
        $this->assertEquals("r-3-none", $r3NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $r3NoneTable['type']);

        // Test r-3-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-3-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test d-1-none-graph
        $client->request("GET", "query", [
            "request" => "d-1-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d1NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d1NoneGraph);
        $this->assertEquals('success', $d1NoneGraph['result']);
        $this->assertArrayHasKey('message', $d1NoneGraph);
        $this->assertEquals('Successfully queried database', $d1NoneGraph['message']);
        $this->assertArrayHasKey('object', $d1NoneGraph);
        $this->assertNotEmpty($d1NoneGraph['object']);
        $this->assertEquals("d-1-none", $d1NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $d1NoneGraph['type']);

        // Test d-1-none-table
        $client->request("GET", "query", [
            "request" => "d-1-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d1NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d1NoneTable);
        $this->assertEquals('success', $d1NoneTable['result']);
        $this->assertArrayHasKey('message', $d1NoneTable);
        $this->assertEquals('Successfully queried database', $d1NoneTable['message']);
        $this->assertArrayHasKey('object', $d1NoneTable);
        $this->assertNotEmpty($d1NoneTable['object']);
        $this->assertEquals("d-1-none", $d1NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $d1NoneTable['type']);

        // Test d-1-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "d-1-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test p-0-none-graph
        $client->request("GET", "query", [
            "request" => "p-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $p0NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $p0NoneGraph);
        $this->assertEquals('success', $p0NoneGraph['result']);
        $this->assertArrayHasKey('message', $p0NoneGraph);
        $this->assertEquals('Successfully queried database', $p0NoneGraph['message']);
        $this->assertArrayHasKey('object', $p0NoneGraph);
        $this->assertNotEmpty($p0NoneGraph['object']);
        $this->assertEquals("p-0-none", $p0NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $p0NoneGraph['type']);

        // Test p-0-none-table
        $client->request("GET", "query", [
            "request" => "p-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $p0NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $p0NoneTable);
        $this->assertEquals('success', $p0NoneTable['result']);
        $this->assertArrayHasKey('message', $p0NoneTable);
        $this->assertEquals('Successfully queried database', $p0NoneTable['message']);
        $this->assertArrayHasKey('object', $p0NoneTable);
        $this->assertNotEmpty($p0NoneTable['object']);
        $this->assertEquals("p-0-none", $p0NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $p0NoneTable['type']);

        // Test p-0-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "p-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test o-0-none-graph
        $client->request("GET", "query", [
            "request" => "o-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $o0NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $o0NoneGraph);
        $this->assertEquals('success', $o0NoneGraph['result']);
        $this->assertArrayHasKey('message', $o0NoneGraph);
        $this->assertEquals('Successfully queried database', $o0NoneGraph['message']);
        $this->assertArrayHasKey('object', $o0NoneGraph);
        $this->assertNotEmpty($o0NoneGraph['object']);
        $this->assertEquals("o-0-none", $o0NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $o0NoneGraph['type']);

        // Test o-0-none-table
        $client->request("GET", "query", [
            "request" => "o-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $o0NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $o0NoneTable);
        $this->assertEquals('success', $o0NoneTable['result']);
        $this->assertArrayHasKey('message', $o0NoneTable);
        $this->assertEquals('Successfully queried database', $o0NoneTable['message']);
        $this->assertArrayHasKey('object', $o0NoneTable);
        $this->assertNotEmpty($o0NoneTable['object']);
        $this->assertEquals("o-0-none", $o0NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $o0NoneTable['type']);

        // Test o-0-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "o-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        ////// More than 121 days apart //////

        $morethan150DaysTimestamp = strtotime("-150 day");
        $morethan150Days = new \DateTime();
        $morethan150Days->setTimestamp($morethan150DaysTimestamp);

        // Test r-0-vendor-graph
        $client->request("GET", "query", [
            "request" => "r-0-vendor",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r0VendorGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r0VendorGraph);
        $this->assertEquals('success', $r0VendorGraph['result']);
        $this->assertArrayHasKey('message', $r0VendorGraph);
        $this->assertEquals('Successfully queried database', $r0VendorGraph['message']);
        $this->assertArrayHasKey('object', $r0VendorGraph);
        $this->assertNotEmpty($r0VendorGraph['object']);
        $this->assertEquals("r-0-vendor", $r0VendorGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r0VendorGraph['type']);

        // Test r-0-vendor-table
        $client->request("GET", "query", [
            "request" => "r-0-vendor",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r0VendorTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r0VendorTable);
        $this->assertEquals('success', $r0VendorTable['result']);
        $this->assertArrayHasKey('message', $r0VendorTable);
        $this->assertEquals('Successfully queried database', $r0VendorTable['message']);
        $this->assertArrayHasKey('object', $r0VendorTable);
        $this->assertNotEmpty($r0VendorTable['object']);
        $this->assertEquals("r-0-vendor", $r0VendorTable['requestedQuery']);
        $this->assertEquals("t-table", $r0VendorTable['type']);

        // Test r-0-vendor-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-0-vendor",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test r-1-user-graph
        $client->request("GET", "query", [
            "request" => "r-1-user",
            "tokenId" => "anon.",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r1UserGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r1UserGraph);
        $this->assertEquals('success', $r1UserGraph['result']);
        $this->assertArrayHasKey('message', $r1UserGraph);
        $this->assertEquals('Successfully queried database', $r1UserGraph['message']);
        $this->assertArrayHasKey('object', $r1UserGraph);
        $this->assertNotEmpty($r1UserGraph['object']);
        $this->assertEquals("r-1-user", $r1UserGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r1UserGraph['type']);

        // Test r-1-user-table
        $client->request("GET", "query", [
            "request" => "r-1-user",
            "tokenId" => "anon.",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r1UserTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r1UserTable);
        $this->assertEquals('success', $r1UserTable['result']);
        $this->assertArrayHasKey('message', $r1UserTable);
        $this->assertEquals('Successfully queried database', $r1UserTable['message']);
        $this->assertArrayHasKey('object', $r1UserTable);
        $this->assertNotEmpty($r1UserTable['object']);
        $this->assertEquals("r-1-user", $r1UserTable['requestedQuery']);
        $this->assertEquals("t-table", $r1UserTable['type']);

        // Test r-1-user-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-1-user",
            "tokenId" => "anon.",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test r-2-shipper-graph
        $client->request("GET", "query", [
            "request" => "r-2-shipper",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r2ReceiverGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r2ReceiverGraph);
        $this->assertEquals('success', $r2ReceiverGraph['result']);
        $this->assertArrayHasKey('message', $r2ReceiverGraph);
        $this->assertEquals('Successfully queried database', $r2ReceiverGraph['message']);
        $this->assertArrayHasKey('object', $r2ReceiverGraph);
        $this->assertNotEmpty($r2ReceiverGraph['object']);
        $this->assertEquals("r-2-shipper", $r2ReceiverGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r2ReceiverGraph['type']);

        // Test r-2-shipper-table
        $client->request("GET", "query", [
            "request" => "r-2-shipper",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r2ReceiverTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r2ReceiverTable);
        $this->assertEquals('success', $r2ReceiverTable['result']);
        $this->assertArrayHasKey('message', $r2ReceiverTable);
        $this->assertEquals('Successfully queried database', $r2ReceiverTable['message']);
        $this->assertArrayHasKey('object', $r2ReceiverTable);
        $this->assertNotEmpty($r2ReceiverTable['object']);
        $this->assertEquals("r-2-shipper", $r2ReceiverTable['requestedQuery']);
        $this->assertEquals("t-table", $r2ReceiverTable['type']);

        // Test r-2-shipper-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-2-shipper",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test d-0-receiver-graph
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d0ReceiverGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d0ReceiverGraph);
        $this->assertEquals('success', $d0ReceiverGraph['result']);
        $this->assertArrayHasKey('message', $d0ReceiverGraph);
        $this->assertEquals('Successfully queried database', $d0ReceiverGraph['message']);
        $this->assertArrayHasKey('object', $d0ReceiverGraph);
        $this->assertNotEmpty($d0ReceiverGraph['object']);
        $this->assertEquals("d-0-receiver", $d0ReceiverGraph['requestedQuery']);
        $this->assertEquals("g-graph", $d0ReceiverGraph['type']);

        // Test d-0-receiver-table
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d0ReceiverTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d0ReceiverTable);
        $this->assertEquals('success', $d0ReceiverTable['result']);
        $this->assertArrayHasKey('message', $d0ReceiverTable);
        $this->assertEquals('Successfully queried database', $d0ReceiverTable['message']);
        $this->assertArrayHasKey('object', $d0ReceiverTable);
        $this->assertNotEmpty($d0ReceiverTable['object']);
        $this->assertEquals("d-0-receiver", $d0ReceiverTable['requestedQuery']);
        $this->assertEquals("t-table", $d0ReceiverTable['type']);

        // Test d-0-receiver-downloadCSV
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "1",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test r-3-none-graph
        $client->request("GET", "query", [
            "request" => "r-3-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r3NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r3NoneGraph);
        $this->assertEquals('success', $r3NoneGraph['result']);
        $this->assertArrayHasKey('message', $r3NoneGraph);
        $this->assertEquals('Successfully queried database', $r3NoneGraph['message']);
        $this->assertArrayHasKey('object', $r3NoneGraph);
        $this->assertNotEmpty($r3NoneGraph['object']);
        $this->assertEquals("r-3-none", $r3NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $r3NoneGraph['type']);

        // Test r-3-none-table
        $client->request("GET", "query", [
            "request" => "r-3-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $r3NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $r3NoneTable);
        $this->assertEquals('success', $r3NoneTable['result']);
        $this->assertArrayHasKey('message', $r3NoneTable);
        $this->assertEquals('Successfully queried database', $r3NoneTable['message']);
        $this->assertArrayHasKey('object', $r3NoneTable);
        $this->assertNotEmpty($r3NoneTable['object']);
        $this->assertEquals("r-3-none", $r3NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $r3NoneTable['type']);

        // Test r-3-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "r-3-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test d-1-none-graph
        $client->request("GET", "query", [
            "request" => "d-1-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d1NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d1NoneGraph);
        $this->assertEquals('success', $d1NoneGraph['result']);
        $this->assertArrayHasKey('message', $d1NoneGraph);
        $this->assertEquals('Successfully queried database', $d1NoneGraph['message']);
        $this->assertArrayHasKey('object', $d1NoneGraph);
        $this->assertNotEmpty($d1NoneGraph['object']);
        $this->assertEquals("d-1-none", $d1NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $d1NoneGraph['type']);

        // Test d-1-none-table
        $client->request("GET", "query", [
            "request" => "d-1-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $d1NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $d1NoneTable);
        $this->assertEquals('success', $d1NoneTable['result']);
        $this->assertArrayHasKey('message', $d1NoneTable);
        $this->assertEquals('Successfully queried database', $d1NoneTable['message']);
        $this->assertArrayHasKey('object', $d1NoneTable);
        $this->assertNotEmpty($d1NoneTable['object']);
        $this->assertEquals("d-1-none", $d1NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $d1NoneTable['type']);

        // Test d-1-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "d-1-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test p-0-none-graph
        $client->request("GET", "query", [
            "request" => "p-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $p0NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $p0NoneGraph);
        $this->assertEquals('success', $p0NoneGraph['result']);
        $this->assertArrayHasKey('message', $p0NoneGraph);
        $this->assertEquals('Successfully queried database', $p0NoneGraph['message']);
        $this->assertArrayHasKey('object', $p0NoneGraph);
        $this->assertNotEmpty($p0NoneGraph['object']);
        $this->assertEquals("p-0-none", $p0NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $p0NoneGraph['type']);

        // Test p-0-none-table
        $client->request("GET", "query", [
            "request" => "p-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $p0NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $p0NoneTable);
        $this->assertEquals('success', $p0NoneTable['result']);
        $this->assertArrayHasKey('message', $p0NoneTable);
        $this->assertEquals('Successfully queried database', $p0NoneTable['message']);
        $this->assertArrayHasKey('object', $p0NoneTable);
        $this->assertNotEmpty($p0NoneTable['object']);
        $this->assertEquals("p-0-none", $p0NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $p0NoneTable['type']);

        // Test p-0-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "p-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        // Test o-0-none-graph
        $client->request("GET", "query", [
            "request" => "o-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $o0NoneGraph = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $o0NoneGraph);
        $this->assertEquals('success', $o0NoneGraph['result']);
        $this->assertArrayHasKey('message', $o0NoneGraph);
        $this->assertEquals('Successfully queried database', $o0NoneGraph['message']);
        $this->assertArrayHasKey('object', $o0NoneGraph);
        $this->assertNotEmpty($o0NoneGraph['object']);
        $this->assertEquals("o-0-none", $o0NoneGraph['requestedQuery']);
        $this->assertEquals("g-graph", $o0NoneGraph['type']);

        // Test o-0-none-table
        $client->request("GET", "query", [
            "request" => "o-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "t-table"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $o0NoneTable = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $o0NoneTable);
        $this->assertEquals('success', $o0NoneTable['result']);
        $this->assertArrayHasKey('message', $o0NoneTable);
        $this->assertEquals('Successfully queried database', $o0NoneTable['message']);
        $this->assertArrayHasKey('object', $o0NoneTable);
        $this->assertNotEmpty($o0NoneTable['object']);
        $this->assertEquals("o-0-none", $o0NoneTable['requestedQuery']);
        $this->assertEquals("t-table", $o0NoneTable['type']);

        // Test o-0-none-downloadCSV
        $client->request("GET", "query", [
            "request" => "o-0-none",
            "tokenId" => "",
            "dateBegin" => $morethan150Days->format("m/d/Y"),
            "dateEnd" => "NOW",
            "type" => "d-csv"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/csv; charset=UTF-8'
            )
        );

        ////// Error out //////
        $client->request("GET", "query", [
            "request" => "",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => ""
        ]);
        
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $error = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $error);
        $this->assertEquals('error', $error['result']);

        ////// Error out 2 //////
        $client->request("GET", "query", [
            "request" => "r-100-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => ""
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $error2 = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $error2);
        $this->assertEquals('error', $error2['result']);

        ////// Error out 3 //////
        $client->request("GET", "query", [
            "request" => "o-100-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => ""
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $error3 = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $error3);
        $this->assertEquals('error', $error3['result']);

        ////// Error out 4 //////
        $client->request("GET", "query", [
            "request" => "d-100-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => ""
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $error4 = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $error4);
        $this->assertEquals('error', $error4['result']);

        ////// Error out 5 //////
        $client->request("GET", "query", [
            "request" => "p-100-none",
            "tokenId" => "",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => ""
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $error5 = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $error5);
        $this->assertEquals('error', $error5['result']);

        ////// No packages round //////
        $client->request("GET", "query", [
            "request" => "d-0-receiver",
            "tokenId" => "3",
            "dateBegin" => "NOW",
            "dateEnd" => "NOW",
            "type" => "g-graph"
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $noPackages = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('result', $noPackages);
        $this->assertEquals('success', $noPackages['result']);
        $this->assertArrayHasKey('message', $noPackages);
        $this->assertEquals('No packages found for given query', $noPackages['message']);
        $this->assertArrayHasKey('object', $noPackages);
        $this->assertEmpty($noPackages['object']);
        $this->assertEquals("d-0-receiver", $noPackages['requestedQuery']);
        $this->assertEquals("g-graph", $noPackages['type']);
    }

}