<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\PackingSlip;

class PackingSlipEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $this->assertEquals("ps", $packingSlip->getFilename());
        $this->assertEquals("jpg", $packingSlip->getExtension());
        $this->assertNotTrue($packingSlip->getDeleted());
        $this->assertEquals("fakepath/upload", $packingSlip->getPath());
        $this->assertEquals(md5("11111111111111111111111111111111"), $packingSlip->getMd5());
        $this->assertEquals("PackingSlipTest", $packingSlip->getUserLastModified());

        $this->assertNotNull($packingSlip->getDateCreated());
        $this->assertNotNull($packingSlip->getDateModified());

        $this->assertInstanceOf(PackingSlip::class, $packingSlip);
    }

    public function testGetFilename() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $this->assertEquals("ps", $packingSlip->getFilename());
    }

    public function testSetFilename() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");
        $packingSlip->setFilename("ps2");

        $this->assertEquals("ps2", $packingSlip->getFilename());
    }

    public function testGetExtension() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $this->assertEquals("jpg", $packingSlip->getExtension());
    }

    public function testSetExtension() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");
        $packingSlip->setExtension("png");

        $this->assertEquals("png", $packingSlip->getExtension());
    }

    public function testGetDeleted() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $this->assertNotTrue($packingSlip->getDeleted());
    }

    public function testSetDeleted() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");
        $packingSlip->setDeleted(true);

        $this->assertTrue($packingSlip->getDeleted());
    }

    public function testGetPath() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $this->assertEquals("fakepath/upload", $packingSlip->getPath());
    }

    public function testSetPath() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");
        $packingSlip->setPath("fakepath/uploads");

        $this->assertEquals("fakepath/uploads", $packingSlip->getPath());
    }

    public function testGetMd5() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $this->assertEquals(md5("11111111111111111111111111111111"), $packingSlip->getMd5());
    }

    public function testSetMd5() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");
        $packingSlip->setMd5(md5("22222222222222222222222222222222"));

        $this->assertEquals(md5("22222222222222222222222222222222"), $packingSlip->getMd5());
    }

    public function testGetDateCreated() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $this->assertNotNull($packingSlip->getDateCreated());
    }

    public function testGetDateModified() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $this->assertNotNull($packingSlip->getDateModified());
    }

    public function testSetDateModified() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");
        $testDate = new \DateTime("2016-03-22");
        $packingSlip->setDateModified($testDate);

        $this->assertEquals($testDate, $packingSlip->getDateModified());
    }

    public function testGetUserLastModified() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");

        $this->assertEquals("PackingSlipTest", $packingSlip->getUserLastModified());
    }

    public function testSetUserLastModified() {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", md5("11111111111111111111111111111111"), "PackingSlipTest");
        $packingSlip->setUserLastModified("PackingSlipTest2");

        $this->assertEquals("PackingSlipTest2", $packingSlip->getUserLastModified());
    }
}