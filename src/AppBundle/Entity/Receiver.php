<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Receiver")
 */
class Receiver {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $deliveryRoom;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = TRUE;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateModified;

    /**
     * @ORM\Column(type="string")
     */
    protected $userLastModified;

    /**
     * Receiver constructor.
     * @param $name
     * @param $deliveryRoom
     */
    public function __construct($name, $deliveryRoom, $user) {
        $this->name = $name;
        $this->deliveryRoom = $deliveryRoom;
        $this->enabled = TRUE;

        // Set the creation and modify date to current date
        $now = new \DateTime("NOW");
        $this->dateCreated = $now;
        $this->dateModified = $now;
        
        $this->userLastModified = $user;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Receiver
     */
    public function setName($name)
    {
        $this->name = $name;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set deliveryRoom
     *
     * @param integer $deliveryRoom
     * @return Receiver
     */
    public function setDeliveryRoom($deliveryRoom)
    {
        $this->deliveryRoom = $deliveryRoom;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        return $this;
    }

    /**
     * Get deliveryRoom
     *
     * @return integer
     */
    public function getDeliveryRoom()
    {
        return $this->deliveryRoom;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Receiver
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Receiver
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Receiver
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set userLastModified
     *
     * @param string $userLastModified
     * @return Receiver
     */
    public function setUserLastModified($userLastModified)
    {
        $this->userLastModified = $userLastModified;

        return $this;
    }

    /**
     * Get userLastModified
     *
     * @return string
     */
    public function getUserLastModified()
    {
        return $this->userLastModified;
    }
}
