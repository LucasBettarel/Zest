<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserInput
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\UserInputRepository")
 */
class UserInput
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
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\InputEntry")
     */
    private $input_entries;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
     $this->date = new \Datetime();
    }



    /**
     * Set date
     *
     * @param \DateTime $date
     * @return UserInput
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return UserInput
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set input_entries
     *
     * @param \SE\InputBundle\Entity\InputEntry $inputEntries
     * @return UserInput
     */
    public function setInputEntries(\SE\InputBundle\Entity\InputEntry $inputEntries = null)
    {
        $this->input_entries = $inputEntries;

        return $this;
    }

    /**
     * Get input_entries
     *
     * @return \SE\InputBundle\Entity\InputEntry 
     */
    public function getInputEntries()
    {
        return $this->input_entries;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
    }
}
