<?php


namespace Tests\AppBundle\Entity;
use AppBundle\Entity\Receiver;

class ReceiverEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor() {
        $receiver = new Receiver("Office", 111);

        $this->assertEquals("Office", $receiver->getName());
        $this->assertEquals(111, $receiver->getDeliveryRoom());
        $this->assertEquals(true, $receiver->getEnabled());

        $this->assertInstanceOf(Receiver::class, $receiver);
    }

    public function testGetName() {
        $receiver = new Receiver("Office", 111);

        $this->assertEquals("Office", $receiver->getName());
    }

    public function testSetName() {
        $receiver = new Receiver("Office", 111);
        $receiver->setName("IT");

        $this->assertEquals("IT", $receiver->getName());
    }

    public function testGetDeliveryRoom() {
        $receiver = new Receiver("Office", 111);

        $this->assertEquals(111, $receiver->getDeliveryRoom());
    }

    public function testSetDeliveryRoom() {
        $receiver = new Receiver("Office", 111);
        $receiver->setDeliveryRoom(1212);

        $this->assertEquals(1212, $receiver->getDeliveryRoom());
    }

    public function testGetEnabled() {
        $receiver = new Receiver("Office", 111);

        $this->assertEquals(true, $receiver->getEnabled());
    }

    public function testSetEnabled()  {
        $receiver = new Receiver("Office", 111);
        $receiver->setEnabled(false);

        $this->assertEquals(false, $receiver->getEnabled());
    }
}