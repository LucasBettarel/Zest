<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SAPRF
 *
 * @ORM\Table(name="saprf")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\SAPRFRepository")
 */
class SAPRF
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="transfer_order", type="integer", nullable=true)
     */
    private $transferOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="material", type="string", length=255, nullable=true)
     */
    private $material;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_confirmation", type="date", nullable=true)
     */
    private $dateConfirmation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_confirmation", type="time", nullable=true)
     */
    private $timeConfirmation;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255, nullable=true)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="source_storage_type", type="string", length=255, nullable=true)
     */
    private $sourceStorageType;

    /**
     * @var string
     *
     * @ORM\Column(name="source_storage_bin", type="string", length=255, nullable=true)
     */
    private $sourceStorageBin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_import", type="datetime", nullable=true)
     */
    private $dateImport;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recorded", type="boolean", nullable=true)
     */
    private $recorded = 0;



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
     * Set transferOrder
     *
     * @param integer $transferOrder
     * @return SAPRF
     */
    public function setTransferOrder($transferOrder)
    {
        $this->transferOrder = $transferOrder;

        return $this;
    }

    /**
     * Get transferOrder
     *
     * @return integer 
     */
    public function getTransferOrder()
    {
        return $this->transferOrder;
    }

    /**
     * Set material
     *
     * @param string $material
     * @return SAPRF
     */
    public function setMaterial($material)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material
     *
     * @return string 
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Set dateConfirmation
     *
     * @param \DateTime $dateConfirmation
     * @return SAPRF
     */
    public function setDateConfirmation($dateConfirmation)
    {
        $this->dateConfirmation = $dateConfirmation;

        return $this;
    }

    /**
     * Get dateConfirmation
     *
     * @return \DateTime 
     */
    public function getDateConfirmation()
    {
        return $this->dateConfirmation;
    }

    /**
     * Set timeConfirmation
     *
     * @param \DateTime $timeConfirmation
     * @return SAPRF
     */
    public function setTimeConfirmation($timeConfirmation)
    {
        $this->timeConfirmation = $timeConfirmation;

        return $this;
    }

    /**
     * Get timeConfirmation
     *
     * @return \DateTime 
     */
    public function getTimeConfirmation()
    {
        return $this->timeConfirmation;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return SAPRF
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set sourceStorageType
     *
     * @param string $sourceStorageType
     * @return SAPRF
     */
    public function setSourceStorageType($sourceStorageType)
    {
        $this->sourceStorageType = $sourceStorageType;

        return $this;
    }

    /**
     * Get sourceStorageType
     *
     * @return string 
     */
    public function getSourceStorageType()
    {
        return $this->sourceStorageType;
    }

    /**
     * Set sourceStorageBin
     *
     * @param string $sourceStorageBin
     * @return SAPRF
     */
    public function setSourceStorageBin($sourceStorageBin)
    {
        $this->sourceStorageBin = $sourceStorageBin;

        return $this;
    }

    /**
     * Get sourceStorageBin
     *
     * @return string 
     */
    public function getSourceStorageBin()
    {
        return $this->sourceStorageBin;
    }

    /**
     * Set recorded
     *
     * @param boolean $recorded
     * @return SAPRF
     */
    public function setRecorded($recorded)
    {
        $this->recorded = $recorded;

        return $this;
    }

    /**
     * Get recorded
     *
     * @return boolean 
     */
    public function getRecorded()
    {
        return $this->recorded;
    }

    /**
     * Set dateImport
     *
     * @param \DateTime $dateImport
     * @return SAPRF
     */
    public function setDateImport($dateImport)
    {
        $this->dateImport = $dateImport;

        return $this;
    }

    /**
     * Get dateImport
     *
     * @return \DateTime 
     */
    public function getDateImport()
    {
        return $this->dateImport;
    }
}
