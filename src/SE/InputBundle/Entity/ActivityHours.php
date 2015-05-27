<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityHours
 *
 * @ORM\Table()
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
     * @ORM\OneToOne(targetEntity="SE\InputBundle\Entity\Workhours", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $workhours;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Team")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Shift")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shift;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Zone")
     * @ORM\JoinColumn(nullable=true)
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Workstation")
     * @ORM\JoinColumn(nullable=true)
     */
    private $workstation;

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
     * Set workhours
     *
     * @param \SE\InputBundle\Entity\Workhours $workhours
     * @return ActivityHours
     */
    public function setWorkhours(\SE\InputBundle\Entity\Workhours $workhours)
    {
        $this->workhours = $workhours;

        return $this;
    }

    /**
     * Get workhours
     *
     * @return \SE\InputBundle\Entity\Workhours 
     */
    public function getWorkhours()
    {
        return $this->workhours;
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
     * Set workstation
     *
     * @param \SE\InputBundle\Entity\Workstation $workstation
     * @return ActivityHours
     */
    public function setWorkstation(\SE\InputBundle\Entity\Workstation $workstation = null)
    {
        $this->workstation = $workstation;

        return $this;
    }

    /**
     * Get workstation
     *
     * @return \SE\InputBundle\Entity\Workstation 
     */
    public function getWorkstation()
    {
        return $this->workstation;
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
}
