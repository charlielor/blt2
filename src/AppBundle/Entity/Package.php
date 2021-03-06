<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Package")
 */
class Package {

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $trackingNumber;

    /**
     * @ORM\Column(type="integer", length=100)
     */
    protected $numberOfPackages;

    /**
     * @ORM\ManyToMany(targetEntity="PackingSlip")
     * @ORM\JoinTable(name="PackagePackingSlip",
     *     joinColumns={
     *         @ORM\JoinColumn(name="trackingNumber", referencedColumnName="trackingNumber")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="packingSlipId", referencedColumnName="id", unique=true)
     *     }
     * )
     */
    protected $packingSlips;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateDelivered;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateReceived;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $delivered;

    /**
     * @ORM\Column(type="string")
     */
    protected $userWhoReceived;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $userWhoDelivered;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $pickedUp;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $datePickedUp;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $userWhoPickedUp;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $userWhoAuthorizedPickUp;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateModified;

    /**
     * @ORM\Column(type="string")
     */
    protected $userLastModified;

    /**
     * @ORM\ManyToOne(targetEntity="Shipper", cascade={"persist"})
     * @ORM\JoinColumn(name="shipperId", referencedColumnName="id")
     */
    protected $shipper;

    /**
     * @ORM\ManyToOne(targetEntity="Receiver", cascade={"persist"})
     * @ORM\JoinColumn(name="receiverId", referencedColumnName="id")
     */
    protected $receiver;

    /**
     * @ORM\ManyToOne(targetEntity="Vendor", cascade={"persist"})
     * @ORM\JoinColumn(name="vendorId", referencedColumnName="id")
     */
    protected $vendor;

    /**
     * Package constructor
     *
     * @param $trackingNumber
     * @param $numOfPackages
     * @param $shipper Shipper object
     * @param $receiver Receiver object
     * @param $vendor Vendor object
     * @param $user
     */
    public function __construct($trackingNumber, $numOfPackages, $shipper, $receiver, $vendor, $user) {
        $this->trackingNumber = $trackingNumber;
        $this->numberOfPackages = $numOfPackages;

        // Creates a new ArrayCollection to hold packing slips
        $this->packingSlips = new ArrayCollection();

        $this->delivered = FALSE;
        $this->pickedUp = FALSE;

        $this->shipper = $shipper;
        $this->receiver = $receiver;
        $this->vendor = $vendor;

        // Set the creation and modify date to current date
        $now = new \DateTime("NOW");
        $this->dateReceived = $now;
        $this->dateCreated = $now;
        $this->dateModified = $now;

        // Set the user who received and last modified
        $this->userWhoReceived = $user;
        $this->userLastModified = $user;
    }

    /**
     * Set trackingNumber
     *
     * @param string $trackingNumber
     * @param string $user
     *
     * @return Package
     */
    public function setTrackingNumber($trackingNumber, $user)
    {
        $this->trackingNumber = $trackingNumber;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get trackingNumber
     *
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * Set numberOfPackages
     *
     * @param integer $numberOfPackages
     * @param string $user
     *
     * @return Package
     */
    public function setNumberOfPackages($numberOfPackages, $user)
    {
        $this->numberOfPackages = $numberOfPackages;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get numberOfPackages
     *
     * @return integer
     */
    public function getNumberOfPackages()
    {
        return $this->numberOfPackages;
    }

    /**
     * Set dateDelivered
     *
     * @param \DateTime $dateDelivered
     * @param string $user
     *
     * @return Package
     */
    public function setDateDelivered($dateDelivered, $user)
    {
        $this->dateDelivered = $dateDelivered;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get dateDelivered
     *
     * @return \DateTime
     */
    public function getDateDelivered()
    {
        return $this->dateDelivered;
    }

    /**
     * Set dateReceived
     *
     * @param \DateTime $dateReceived
     * @param string $user
     *
     * @return Package
     */
    public function setDateReceived($dateReceived, $user)
    {
        $this->dateReceived = $dateReceived;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get dateReceived
     *
     * @return \DateTime
     */
    public function getDateReceived()
    {
        return $this->dateReceived;
    }

    /**
     * Set delivered
     *
     * @param boolean $delivered
     * @param string $user
     *
     * @return Package
     */
    public function setDelivered($delivered, $user)
    {
        $this->delivered = $delivered;

        $now = new \DateTime("NOW");

        if ($delivered) {
            $this->dateDelivered = $now;
            $this->userWhoDelivered = $user;
        }


        $this->dateModified = $now;
        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get delivered
     *
     * @return boolean
     */
    public function getDelivered()
    {
        return $this->delivered;
    }

    /**
     * Set shipper
     *
     * @param \AppBundle\Entity\Shipper $shipper
     * @param string $user
     *
     * @return Package
     */
    public function setShipper(\AppBundle\Entity\Shipper $shipper = null, $user)
    {
        $this->shipper = $shipper;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get shipper
     *
     * @return \AppBundle\Entity\Shipper
     */
    public function getShipper()
    {
        return $this->shipper;
    }

    /**
     * Set receiver
     *
     * @param \AppBundle\Entity\Receiver $receiver
     * @param string $user
     *
     * @return Package
     */
    public function setReceiver(\AppBundle\Entity\Receiver $receiver = null, $user)
    {
        $this->receiver = $receiver;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \AppBundle\Entity\Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set vendor
     *
     * @param \AppBundle\Entity\Vendor $vendor
     * @param string $user
     *
     * @return Package
     */
    public function setVendor(\AppBundle\Entity\Vendor $vendor = null, $user)
    {
        $this->vendor = $vendor;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \AppBundle\Entity\Vendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set userWhoReceived
     *
     * @param string $userWhoReceived
     * @param string $user
     *
     * @return Package
     */
    public function setUserWhoReceived($userWhoReceived, $user)
    {
        $this->userWhoReceived = $userWhoReceived;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get userWhoReceived
     *
     * @return string
     */
    public function getUserWhoReceived()
    {
        return $this->userWhoReceived;
    }

    /**
     * Set userWhoDelivered
     *
     * @param string $userWhoDelivered
     * @param string $user
     *
     * @return Package
     */
    public function setUserWhoDelivered($userWhoDelivered, $user)
    {
        $this->userWhoDelivered = $userWhoDelivered;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get userWhoDelivered
     *
     * @return string
     */
    public function getUserWhoDelivered()
    {
        return $this->userWhoDelivered;
    }

    /**
     * Set datePickedUp
     *
     * @param \DateTime $datePickedUp
     * @param string $user
     *
     * @return Package
     */
    public function setDatePickedUp($datePickedUp, $user)
    {
        $this->datePickedUp = $datePickedUp;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get datePickedUp
     *
     * @return \DateTime
     */
    public function getDatePickedUp()
    {
        return $this->datePickedUp;
    }

    /**
     * Set userWhoPickedUp
     *
     * @param string $userWhoPickedUp
     * @param string $user
     *
     * @return Package
     */
    public function setUserWhoPickedUp($userWhoPickedUp, $user)
    {
        $this->userWhoPickedUp = $userWhoPickedUp;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get userWhoPickedUp
     *
     * @return string
     */
    public function getUserWhoPickedUp()
    {
        return $this->userWhoPickedUp;
    }

    /**
     * Set userWhoAuthorizedPickUp
     *
     * @param string $userWhoAuthorizedPickUp
     * @param string $user
     *
     * @return Package
     */
    public function setUserWhoAuthorizedPickUp($userWhoAuthorizedPickUp, $user)
    {
        $this->userWhoAuthorizedPickUp = $userWhoAuthorizedPickUp;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get userWhoAuthorizedPickUp
     *
     * @return string
     */
    public function getUserWhoAuthorizedPickUp()
    {
        return $this->userWhoAuthorizedPickUp;
    }

    /**
     * Set pickedUp
     *
     * @param boolean $pickedUp
     * @param string $user
     *
     * @return Package
     */
    public function setPickedUp($pickedUp, $user)
    {
        $this->pickedUp = $pickedUp;

        $now = new \DateTime("NOW");

        if ($pickedUp) {
            $this->datePickedUp = $now;
            $this->userWhoAuthorizedPickUp = $user;
        }

        $this->dateModified = $now;
        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get pickedUp
     *
     * @return boolean
     */
    public function getPickedUp()
    {
        return $this->pickedUp;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @param string $user
     *
     * @return Package
     */
    public function setDateModified($dateModified, $user)
    {
        $this->dateModified = $dateModified;

        $this->userLastModified = $user;

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
     * @return Package
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

    /**
     * Add a packingSlip
     *
     * @param \AppBundle\Entity\PackingSlip $packingSlip
     * @param string $user
     *
     * @return Package
     */
    public function addPackingSlip(\AppBundle\Entity\PackingSlip $packingSlip, $user)
    {
        $this->packingSlips[] = $packingSlip;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Remove a packingSlips
     *
     * @param \AppBundle\Entity\PackingSlip $packingSlip
     * @param $user
     *
     * @return Package
     */
    public function removePackingSlips(\AppBundle\Entity\PackingSlip $packingSlip, $user)
    {
        $this->packingSlips->removeElement($packingSlip);

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        $this->userLastModified = $user;

        return $this;
    }

    /**
     * Get packingSlips
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPackingSlips()
    {
        return $this->packingSlips;
    }


}
