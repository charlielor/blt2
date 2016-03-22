<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\PackingSlip;

class PackingSlipEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $packingSlip = new PackingSlip("ps", "jpg", "fakepath/upload", "11111111111111111111111111111111", "PackingSlipTest");

        $this->assertEquals("ps", $packingSlip->getFileName());
        $this->assertEquals("jpg", $packingSlip->getExtension());
        $this->assertNotTrue($packingSlip->getDeleted());
        $this->assertEquals("fakepath/upload", $packingSlip->getPath());
        $this->assertEquals("11111111111111111111111111111111", $packingSlip->getMd5());
        $this->assertEquals("PackingSlipTest", $packingSlip->getUserLastModified());
        $this->assertNotNull($packingSlip->getDateCreated());
        $this->assertNotNull($packingSlip->getDateModified());
    }
}