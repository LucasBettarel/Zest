<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Presence
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Presence
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
     * @var boolean
     *
     * @ORM\Column(name="present", type="boolean")
     */
    private $present;

    /**
     * @var string
     *
     * @ORM\Column(name="absence_reason", type="string", length=255)
     */
    private $absenceReason;


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
     * Set present
     *
     * @param boolean $present
     * @return Presence
     */
    public function setPresent($present)
    {
        $this->present = $present;

        return $this;
    }

    /**
     * Get present
     *
     * @return boolean 
     */
    public function getPresent()
    {
        return $this->present;
    }

    /**
     * Set absenceReason
     *
     * @param string $absenceReason
     * @return Presence
     */
    public function setAbsenceReason($absenceReason)
    {
        $this->absenceReason = $absenceReason;

        return $this;
    }

    /**
     * Get absenceReason
     *
     * @return string 
     */
    public function getAbsenceReason()
    {
        return $this->absenceReason;
    }
}
