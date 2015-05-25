<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Workhours
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\WorkhoursRepository")
 */
class Workhours
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
     * @ORM\Column(name="regular_hours", type="decimal")
     */
    private $regularHours;

    /**
     * @var string
     *
     * @ORM\Column(name="ot_hours", type="decimal")
     */
    private $otHours;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ot_start_time", type="datetime")
     */
    private $otStartTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ot_end_time", type="datetime")
     */
    private $otEndTime;


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
     * Set regularHours
     *
     * @param string $regularHours
     * @return Workhours
     */
    public function setRegularHours($regularHours)
    {
        $this->regularHours = $regularHours;

        return $this;
    }

    /**
     * Get regularHours
     *
     * @return string 
     */
    public function getRegularHours()
    {
        return $this->regularHours;
    }

    /**
     * Set otHours
     *
     * @param string $otHours
     * @return Workhours
     */
    public function setOtHours($otHours)
    {
        $this->otHours = $otHours;

        return $this;
    }

    /**
     * Get otHours
     *
     * @return string 
     */
    public function getOtHours()
    {
        return $this->otHours;
    }

    /**
     * Set otStartTime
     *
     * @param \DateTime $otStartTime
     * @return Workhours
     */
    public function setOtStartTime($otStartTime)
    {
        $this->otStartTime = $otStartTime;

        return $this;
    }

    /**
     * Get otStartTime
     *
     * @return \DateTime 
     */
    public function getOtStartTime()
    {
        return $this->otStartTime;
    }

    /**
     * Set otEndTime
     *
     * @param \DateTime $otEndTime
     * @return Workhours
     */
    public function setOtEndTime($otEndTime)
    {
        $this->otEndTime = $otEndTime;

        return $this;
    }

    /**
     * Get otEndTime
     *
     * @return \DateTime 
     */
    public function getOtEndTime()
    {
        return $this->otEndTime;
    }
}
