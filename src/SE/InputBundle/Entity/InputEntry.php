<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InputEntry
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\InputEntryRepository")
 */
class InputEntry
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
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Employee", inversedBy="inputs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $employee;

    /**
     * @ORM\OneToOne(targetEntity="SE\InputBundle\Entity\Presence", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $presence;

    /**
     * @ORM\OneToMany(targetEntity="SE\InputBundle\Entity\ActivityHours", mappedBy="input", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $activity_hours;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=255, nullable=true)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\UserInput", inversedBy="input_entries", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_input;


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
     * Set comments
     *
     * @param string $comments
     * @return InputEntry
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set employee
     *
     * @param \SE\InputBundle\Entity\Employee $employee
     * @return InputEntry
     */
    public function setEmployee(\SE\InputBundle\Entity\Employee $employee)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \SE\InputBundle\Entity\Employee 
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set presence
     *
     * @param \SE\InputBundle\Entity\Presence $presence
     * @return InputEntry
     */
    public function setPresence(\SE\InputBundle\Entity\Presence $presence)
    {
        $this->presence = $presence;

        return $this;
    }

    /**
     * Get presence
     *
     * @return \SE\InputBundle\Entity\Presence 
     */
    public function getPresence()
    {
        return $this->presence;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activity_hours = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add activity_hours
     *
     * @param \SE\InputBundle\Entity\ActivityHours $activityHours
     * @return InputEntry
     */
    public function addActivityHour(\SE\InputBundle\Entity\ActivityHours $activityHour)
    {
        $this->activity_hours[] = $activityHour;
        $activityHour->setInput($this);

        return $this;
    }

    /**
     * Remove activity_hours
     *
     * @param \SE\InputBundle\Entity\ActivityHours $activityHours
     */
    public function removeActivityHour(\SE\InputBundle\Entity\ActivityHours $activityHour)
    {
        $this->activity_hours->removeElement($activityHour);
       // $activityHours->setInput(null);
    }

    /**
     * Get activity_hours
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivityHours()
    {
        return $this->activity_hours;
    }

    /**
     * Set user_input
     *
     * @param \SE\InputBundle\Entity\UserInput $userInput
     * @return InputEntry
     */
    public function setUserInput(\SE\InputBundle\Entity\UserInput $userInput)
    {
        $this->user_input = $userInput;

        return $this;
    }

    /**
     * Get user_input
     *
     * @return \SE\InputBundle\Entity\UserInput 
     */
    public function getUserInput()
    {
        return $this->user_input;
    }
}
