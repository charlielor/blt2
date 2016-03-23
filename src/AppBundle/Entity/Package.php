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

    public function __construct($trackingNumber, $numOfPackages, Shipper $shipper, Receiver $receiver, Vendor $vendor, $user) {
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
     * @return ReceivedPackage
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setNumberOfPackages($numberOfPackages)
    {
        $this->numberOfPackages = $numberOfPackages;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setDateDelivered($dateDelivered)
    {
        $this->dateDelivered = $dateDelivered;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setDateReceived($dateReceived)
    {
        $this->dateReceived = $dateReceived;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setDelivered($delivered)
    {
        $this->delivered = $delivered;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setShipper(\AppBundle\Entity\Shipper $shipper = null)
    {
        $this->shipper = $shipper;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setReceiver(\AppBundle\Entity\Receiver $receiver = null)
    {
        $this->receiver = $receiver;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setVendor(\AppBundle\Entity\Vendor $vendor = null)
    {
        $this->vendor = $vendor;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setUserWhoReceived($userWhoReceived)
    {
        $this->userWhoReceived = $userWhoReceived;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setUserWhoDelivered($userWhoDelivered)
    {
        $this->userWhoDelivered = $userWhoDelivered;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setDatePickedUp($datePickedUp)
    {
        $this->datePickedUp = $datePickedUp;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setUserWhoPickedUp($userWhoPickedUp)
    {
        $this->userWhoPickedUp = $userWhoPickedUp;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setUserWhoAuthorizedPickUp($userWhoAuthorizedPickUp)
    {
        $this->userWhoAuthorizedPickUp = $userWhoAuthorizedPickUp;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
     */
    public function setPickedUp($pickedUp)
    {
        $this->pickedUp = $pickedUp;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

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
     * @return ReceivedPackage
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
     * @return ReceivedPackage
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
     * @return Package
     */
    public function addPackingSlip(\AppBundle\Entity\PackingSlip $packingSlip)
    {
        $this->packingSlips[] = $packingSlip;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        return $this;
    }

    /**
     * Remove a packingSlips
     *
     * @param \AppBundle\Entity\PackingSlip $packingSlip
     */
    public function removePackingSlips(\AppBundle\Entity\PackingSlip $packingSlip)
    {
        $this->packingSlips->removeElement($packingSlip);

        $now = new \DateTime("NOW");
        $this->dateModified = $now;
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
