<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SAPRF
 *
 * @ORM\Table()
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
     * @var string
     *
     * @ORM\Column(name="warehouse", type="string", length=255)
     */
    private $warehouse;

    /**
     * @var integer
     *
     * @ORM\Column(name="transfer_order", type="integer")
     */
    private $transferOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="material", type="string", length=255)
     */
    private $material;

    /**
     * @var string
     *
     * @ORM\Column(name="plant", type="string", length=255)
     */
    private $plant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_confirmation", type="date")
     */
    private $dateConfirmation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_confirmation", type="time")
     */
    private $timeConfirmation;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="source_storage_type", type="string", length=255)
     */
    private $sourceStorageType;


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
     * Set warehouse
     *
     * @param string $warehouse
     * @return SAPRF
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return string 
     */
    public function getWarehouse()
    {
        return $this->warehouse;
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
     * Set plant
     *
     * @param string $plant
     * @return SAPRF
     */
    public function setPlant($plant)
    {
        $this->plant = $plant;

        return $this;
    }

    /**
     * Get plant
     *
     * @return string 
     */
    public function getPlant()
    {
        return $this->plant;
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
}
