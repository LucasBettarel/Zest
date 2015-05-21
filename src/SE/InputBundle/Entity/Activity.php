<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\ActivityRepository")
 */
class Activity
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
     * @var double
     *
     * @ORM\Column(name="default_target", type="decimal", nullable=true)
     */
    private $default_target;

    /**
     * @ORM\ManyToMany(targetEntity="SE\InputBundle\Entity\Team", inversedBy="activities", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="SE\InputBundle\Entity\ActivityZone", mappedBy="activity", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $activity_zones;

    /**
     * @ORM\ManyToMany(targetEntity="SE\InputBundle\Entity\Workstation", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $workstations;


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
     * @return Activity
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
     * Constructor
     */
    public function __construct()
    {
        $this->workstations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add workstations
     *
     * @param \SE\InputBundle\Entity\Workstation $workstations
     * @return Activity
     */
    public function addWorkstation(\SE\InputBundle\Entity\Workstation $workstations)
    {
        $this->workstations[] = $workstations;

        return $this;
    }

    /**
     * Remove workstations
     *
     * @param \SE\InputBundle\Entity\Workstation $workstations
     */
    public function removeWorkstation(\SE\InputBundle\Entity\Workstation $workstations)
    {
        $this->workstations->removeElement($workstations);
    }

    /**
     * Get workstations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWorkstations()
    {
        return $this->workstations;
    }

    /**
     * Set default_target
     *
     * @param double $defaultTarget
     * @return Activity
     */
    public function setDefaultTarget($defaultTarget = null)
    {
        $this->default_target = $defaultTarget;

        return $this;
    }

    /**
     * Get default_target
     *
     * @return double 
     */
    public function getDefaultTarget()
    {
        return $this->default_target;
    }

    /**
     * Add activity_zones
     *
     * @param \SE\InputBundle\Entity\ActivityZone $activityZones
     * @return Activity
     */
    public function addActivityZone(\SE\InputBundle\Entity\ActivityZone $activityZones)
    {
        $this->activity_zones[] = $activityZones;
        $activityZones->setActivity($this);

        return $this;
    }

    /**
     * Remove activity_zones
     *
     * @param \SE\InputBundle\Entity\ActivityZone $activityZones
     */
    public function removeActivityZone(\SE\InputBundle\Entity\ActivityZone $activityZones)
    {
        $this->activity_zones->removeElement($activityZones);
        $activity_zones->setActivity(null);
    }

    /**
     * Get activity_zones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivityZones()
    {
        return $this->activity_zones;
    }

    /**
     * Add teams
     *
     * @param \SE\InputBundle\Entity\Team $teams
     * @return Activity
     */
    public function addTeam(\SE\InputBundle\Entity\Team $teams)
    {
        $this->teams[] = $teams;
        $teams->addActivity($this);

        return $this;
    }

    /**
     * Remove teams
     *
     * @param \SE\InputBundle\Entity\Team $teams
     */
    public function removeTeam(\SE\InputBundle\Entity\Team $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTeams()
    {
        return $this->teams;
    }
}
