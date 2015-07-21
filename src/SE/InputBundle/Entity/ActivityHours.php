<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ActivityHours
 *
 * @ORM\Table(name="activityhours")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\ActivityHoursRepository")
 */
class ActivityHours
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
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\InputEntry", inversedBy="activity_hours", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $input;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Activity")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;

    /**
     * @var string
     *
     * @ORM\Column(name="regular_hours", type="decimal", precision=11, scale=2)
     */
    private $regularHours;

    /**
     * @var string
     *
     * @ORM\Column(name="ot_hours", type="decimal", precision=11, scale=2)
     */
    private $otHours;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Zone")
     * @ORM\JoinColumn(nullable=true)
     */
    private $zone;

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
     * Set activity
     *
     * @param \SE\InputBundle\Entity\Activity $activity
     * @return ActivityHours
     */
    public function setActivity(\SE\InputBundle\Entity\Activity $activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return \SE\InputBundle\Entity\Activity 
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set team
     *
     * @param \SE\InputBundle\Entity\Team $team
     * @return ActivityHours
     */
    public function setTeam(\SE\InputBundle\Entity\Team $team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \SE\InputBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set shift
     *
     * @param \SE\InputBundle\Entity\Shift $shift
     * @return ActivityHours
     */
    public function setShift(\SE\InputBundle\Entity\Shift $shift)
    {
        $this->shift = $shift;

        return $this;
    }

    /**
     * Get shift
     *
     * @return \SE\InputBundle\Entity\Shift 
     */
    public function getShift()
    {
        return $this->shift;
    }

    /**
     * Set zone
     *
     * @param \SE\InputBundle\Entity\Zone $zone
     * @return ActivityHours
     */
    public function setZone(\SE\InputBundle\Entity\Zone $zone = null)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Get zone
     *
     * @return \SE\InputBundle\Entity\Zone 
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set input
     *
     * @param \SE\InputBundle\Entity\InputEntry $input
     * @return ActivityHours
     */
    public function setInput(\SE\InputBundle\Entity\InputEntry $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Get input
     *
     * @return \SE\InputBundle\Entity\InputEntry 
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Set regularHours
     *
     * @param string $regularHours
     * @return ActivityHours
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
     * @return ActivityHours
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
}
