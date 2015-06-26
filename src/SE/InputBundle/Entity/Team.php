<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\TeamRepository")
 */
class Team
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
     * @var integer
     *
     * @ORM\Column(name="shiftnb", type="integer")
     */
    private $shiftnb;

    /**
     * @ORM\ManyToMany(targetEntity="SE\InputBundle\Entity\Activity", mappedBy="teams", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $activities;


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
     * @return Team
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
     * Set shiftnb
     *
     * @param integer $shiftnb
     * @return Team
     */
    public function setShiftnb($shiftnb)
    {
        $this->shiftnb = $shiftnb;

        return $this;
    }

    /**
     * Get shiftnb
     *
     * @return integer 
     */
    public function getShiftnb()
    {
        return $this->shiftnb;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add activities
     *
     * @param \SE\InputBundle\Entity\Activity $activities
     * @return Team
     */
    public function addActivity(\SE\InputBundle\Entity\Activity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \SE\InputBundle\Entity\Activity $activities
     */
    public function removeActivity(\SE\InputBundle\Entity\Activity $activities)
    {
        $this->activities->removeElement($activities);
        $activities->removeTeam($this);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivities()
    {
        return $this->activities;
    }
}
