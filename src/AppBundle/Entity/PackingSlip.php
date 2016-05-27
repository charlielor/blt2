<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="PackingSlip")
 */
class PackingSlip {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $filename;

    /**
     * @ORM\Column(type="string", length=3)
     */
    protected $extension;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted;

    /**
     * @ORM\Column(type="string")
     */
    protected $path;

    /**
     * @ORM\Column(type="string")
     */
    protected $md5;

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
     * PackingSlip constructor.
     * @param $filename
     * @param $extension
     * @param $path
     * @param $md5
     * @param $user
     */
    public function __construct($filename, $extension, $path, $md5, $user) {
        $filenameE = explode(".", $filename);

        if (count($filenameE) > 1) {
            $this->filename = $filenameE[0];
        } else {
            $this->filename = $filename;
        }

        $this->extension = $extension;
        $this->deleted = false;
        $this->path = $path;
        $this->md5 = $md5;

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
     * Set filename
     *
     * @param string $filename
     * @return PackingSlip
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return PackingSlip
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return PackingSlip
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return PackingSlip
     */
    public function setPath($path)
    {
        $this->path = $path;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set md5
     *
     * @param string $md5
     * @return PackingSlip
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;

        $now = new \DateTime("NOW");
        $this->dateModified = $now;

        return $this;
    }

    /**
     * Get md5
     *
     * @return string
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return PackingSlip
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
     * @return PackingSlip
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
     * @param \DateTime $userLastModified
     * @return PackingSlip
     */
    public function setUserLastModified($userLastModified)
    {
        $this->userLastModified = $userLastModified;

        return $this;
    }

    /**
     * Get userLastModified
     *
     * @return \DateTime
     */
    public function getUserLastModified()
    {
        return $this->userLastModified;
    }

    /**
     * Get the relative link to the file
     *
     * @return string
     */
    public function getRelativePath() {
        return $this->path . $this->filename . '.' . $this->extension;
    }

    /**
     * Get the download link to file
     */
    public function getDownloadLink() {
        // Remove 'upload/' from path
        $pathWithoutRoot = str_replace("uploads/", "", $this->path);

        return $pathWithoutRoot . $this->filename . '.' . $this->extension;
    }

    /**
     * Rename file to make it deleted
     *
     * @param $newFileName
     *
     * @return PackingSlip
     */
    public function renamePackingSlipToDeleted($newFileName) {
        if (!($this->deleted)) {

            $this->setFilename($newFileName);
            $this->deleted = true;
            $now = new \DateTime("NOW");
            $this->dateModified = $now;
        }

        return $this;
    }

    public function getCompleteFileName() {
        return $this->getFilename() . "." . $this->getExtension();
    }
}
