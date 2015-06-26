<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityZone
 *
 * @ORM\Table(name="activityzone")
 * @ORM\Entity
 */
class ActivityZone
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
     * @ORM\Column(name="target", type="decimal")
     */
    private $target;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Activity", inversedBy="activity_zones", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Zone", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
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
     * Set target
     *
     * @param string $target
     * @return ActivityZone
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set activity
     *
     * @param \SE\InputBundle\Entity\Activity $activity
     * @return ActivityZone
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
     * Set zone
     *
     * @param \SE\InputBundle\Entity\Zone $zone
     * @return ActivityZone
     */
    public function setZone(\SE\InputBundle\Entity\Zone $zone)
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
}
