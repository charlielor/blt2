<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Shipper")
 */
class Shipper
{
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
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

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
     * Shipper constructor.
     */
    public function __construct($name) {
        $this->name = $name;
        $this->enabled = TRUE;

        // Set the creation and modify date to current date
        $now = new \DateTime("NOW");
        $this->dateCreated = $now;
        $this->dateModified = $now;
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
     * @return Shipper
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Shipper
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
     * @return Shipper
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
     * @return Shipper
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
     * @return Shipper
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
