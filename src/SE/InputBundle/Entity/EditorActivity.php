<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EditorActivity
 *
 * @ORM\Table(name="editoractivity")
 * @ORM\Entity
 */
class EditorActivity
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
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\EditorEntry", inversedBy="editoractivities", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $editorEntry;

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
     * Set regularHours
     *
     * @param string $regularHours
     * @return EditorActivity
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
     * @return EditorActivity
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
     * Set editorEntry
     *
     * @param \SE\InputBundle\Entity\EditorEntry $editorEntry
     * @return EditorActivity
     */
    public function setEditorEntry(\SE\InputBundle\Entity\EditorEntry $editorEntry)
    {
        $this->editorEntry = $editorEntry;

        return $this;
    }

    /**
     * Get editorEntry
     *
     * @return \SE\InputBundle\Entity\EditorEntry 
     */
    public function getEditorEntry()
    {
        return $this->editorEntry;
    }

    /**
     * Set activity
     *
     * @param \SE\InputBundle\Entity\Activity $activity
     * @return EditorActivity
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
     * @return EditorActivity
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
}
