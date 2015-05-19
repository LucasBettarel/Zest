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
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Status")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Job")
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
    * @ORM\Column(name="date_creation", type="date")
    */
    protected $date_creation;

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
}
