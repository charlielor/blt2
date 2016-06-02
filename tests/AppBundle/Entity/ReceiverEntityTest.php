<?php


namespace Tests\AppBundle\Entity;
use AppBundle\Entity\Receiver;

class ReceiverEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");

        $this->assertEquals("Office", $receiver->getName());
        $this->assertEquals(111, $receiver->getDeliveryRoom());
        $this->assertTrue($receiver->getEnabled());

        $this->assertInstanceOf(Receiver::class, $receiver);
    }

    public function testGetName() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");

        $this->assertEquals("Office", $receiver->getName());
    }

    public function testSetName() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");
        $receiver->setName("IT", "ReceiverTest");

        $this->assertEquals("IT", $receiver->getName());
    }

    public function testGetDeliveryRoom() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");

        $this->assertEquals(111, $receiver->getDeliveryRoom());
    }

    public function testSetDeliveryRoom() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");
        $receiver->setDeliveryRoom(1212, "ReceiverTest");

        $this->assertEquals(1212, $receiver->getDeliveryRoom());
    }

    public function testGetEnabled() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");

        $this->assertTrue($receiver->getEnabled());
    }

    public function testSetEnabled()  {
        $receiver = new Receiver("Office", 111, "ReceiverTest");
        $receiver->setEnabled(false, "ReceiverTest");

        $this->assertNotTrue($receiver->getEnabled());
    }

    public function testDateCreated() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");

        $this->assertNotNull($receiver->getDateCreated());
    }

    public function testGetDateModified() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");

        $this->assertNotNull($receiver->getDateModified());
    }

    public function testSetDateModified() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");
        $testDate = new \DateTime("2016-03-22");

        $receiver->setDateModified($testDate, "ReceiverTest");

        $this->assertEquals($testDate, $receiver->getDateModified());
    }

    public function testGetUserLastModified() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");

        $this->assertEquals("ReceiverTest", $receiver->getUserLastModified());
    }

    public function testSetUserLastModified() {
        $receiver = new Receiver("Office", 111, "ReceiverTest");
        $receiver->setUserLastModified("ReceiverTest2");

        $this->assertEquals("ReceiverTest2", $receiver->getUserLastModified());
    }
}