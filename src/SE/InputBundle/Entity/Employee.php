<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Employee
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\EmployeeRepository")
 */
class Employee
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="sesa", type="string", length=255)
     */
    private $sesa;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Team")
     * @ORM\JoinColumn(nullable=true)
     */
    private $default_team;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Shift")
     * @ORM\JoinColumn(nullable=true)
     */
    private $default_shift;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Activity")
     * @ORM\JoinColumn(nullable=true)
     */
    private $default_activity;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Zone")
     * @ORM\JoinColumn(nullable=true)
     */
    private $default_zone;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Status", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permanent", type="boolean")
     */
    private $permanent;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Job", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $job;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text")
     */
    private $remarks;

    /**
     * @ORM\OneToMany(targetEntity="SE\InputBundle\Entity\InputEntry", mappedBy="employee", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $inputs;

    /**
    * @ORM\Column(name="date_creation", type="date")
    */
    protected $date_creation;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->date_creation = new \Datetime();
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
     * Set name
     *
     * @param string $name
     * @return Employee
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sesa
     *
     * @param string $sesa
     * @return Employee
     */
    public function setSesa($sesa)
    {
        $this->sesa = $sesa;

        return $this;
    }

    /**
     * Get sesa
     *
     * @return string 
     */
    public function getSesa()
    {
        return $this->sesa;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     * @return Employee
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string 
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set status
     *
     * @param \SE\InputBundle\Entity\Status $status
     * @return Employee
     */
    public function setStatus(\SE\InputBundle\Entity\Status $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \SE\InputBundle\Entity\Status 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set date_creation
     *
     * @param \DateTime $dateCreation
     * @return Employee
     */
    public function setDateCreation($dateCreation)
    {
        $this->date_creation = $dateCreation;

        return $this;
    }

    /**
     * Get date_creation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }

    /**
     * Set default_team
     *
     * @param \SE\InputBundle\Entity\Team $defaultTeam
     * @return Employee
     */
    public function setDefaultTeam(\SE\InputBundle\Entity\Team $defaultTeam = null)
    {
        $this->default_team = $defaultTeam;

        return $this;
    }

    /**
     * Get default_team
     *
     * @return \SE\InputBundle\Entity\Team 
     */
    public function getDefaultTeam()
    {
        return $this->default_team;
    }

    /**
     * Set job
     *
     * @param \SE\InputBundle\Entity\Job $job
     * @return Employee
     */
    public function setJob(\SE\InputBundle\Entity\Job $job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return \SE\InputBundle\Entity\Job 
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set permanent
     *
     * @param boolean $permanent
     * @return Employee
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;

        return $this;
    }

    /**
     * Get permanent
     *
     * @return boolean 
     */
    public function getPermanent()
    {
        return $this->permanent;
    }

    /**
     * Set default_shift
     *
     * @param \SE\InputBundle\Entity\Shift $defaultShift
     * @return Employee
     */
    public function setDefaultShift(\SE\InputBundle\Entity\Shift $defaultShift = null)
    {
        $this->default_shift = $defaultShift;

        return $this;
    }

    /**
     * Get default_shift
     *
     * @return \SE\InputBundle\Entity\Shift 
     */
    public function getDefaultShift()
    {
        return $this->default_shift;
    }

    /**
     * Set default_activity
     *
     * @param \SE\InputBundle\Entity\Activity $defaultActivity
     * @return Employee
     */
    public function setDefaultActivity(\SE\InputBundle\Entity\Activity $defaultActivity = null)
    {
        $this->default_activity = $defaultActivity;

        return $this;
    }

    /**
     * Get default_activity
     *
     * @return \SE\InputBundle\Entity\Activity 
     */
    public function getDefaultActivity()
    {
        return $this->default_activity;
    }

    /**
     * Set default_zone
     *
     * @param \SE\InputBundle\Entity\Zone $defaultZone
     * @return Employee
     */
    public function setDefaultZone(\SE\InputBundle\Entity\Zone $defaultZone = null)
    {
        $this->default_zone = $defaultZone;

        return $this;
    }

    /**
     * Get default_zone
     *
     * @return \SE\InputBundle\Entity\Zone 
     */
    public function getDefaultZone()
    {
        return $this->default_zone;
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
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
    }

    /**
     * Add inputs
     *
     * @param \SE\InputBundle\Entity\InputEntry $inputs
     * @return Employee
     */
    public function addInput(\SE\InputBundle\Entity\InputEntry $inputs)
    {
        $this->inputs[] = $inputs;

        return $this;
    }

    /**
     * Remove inputs
     *
     * @param \SE\InputBundle\Entity\InputEntry $inputs
     */
    public function removeInput(\SE\InputBundle\Entity\InputEntry $inputs)
    {
        $this->inputs->removeElement($inputs);
    }

    /**
     * Get inputs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInputs()
    {
        return $this->inputs;
    }
}
