<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InputEntry
 *
 * @ORM\Table(name="inputentry")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\InputEntryRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @Assert\NotBlank(message = "Choose an employee for this edition, it won't work so good otherwise.")
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Employee", inversedBy="inputs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $employee;

    /**
     * @Assert\NotBlank(message = "Hey! No SESA = No Lines, No Lines = Bad Productivity ! No good lah!")
     * @ORM\Column(name="sesa", type="string", length=255, nullable=true)
     */
    private $sesa;

    /**
     * @var boolean
     *
     * @ORM\Column(name="present", type="boolean")
     * @Assert\NotNull()
     */
    private $present;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\AbsenceReason", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $absence_reason;

    /**
     * @ORM\OneToMany(targetEntity="SE\InputBundle\Entity\ActivityHours", mappedBy="input", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid()
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
     * @ORM\Column(name="total_hours", type="decimal", nullable=false, precision=11, scale=2)
     */
    private $totalHours = 0;

    /**
     * @ORM\Column(name="total_working_hours", type="decimal", nullable=false, precision=11, scale=2)
     */
    private $totalWorkingHours = 0;

    /**
     * @ORM\Column(name="total_overtime", type="decimal", nullable=false, precision=11, scale=2)
     */
    private $totalOvertime = 0;

    /**
     * @ORM\Column(name="total_to", type="integer", nullable=true)
     */
    private $totalTo = 0;

    /**
     * @ORM\Column(name="total_prod", type="decimal", nullable=true, precision=11, scale=2)
     */
    private $totalProd = 0;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\EditorStatus", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $editorStatus;


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


    /**
     * Set sesa
     *
     * @param string $sesa
     * @return InputEntry
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
     * Set totalHours
     *
     * @param integer $totalHours
     * @return InputEntry
     */
    public function setTotalHours($totalHours)
    {
        $this->totalHours = $totalHours;

        return $this;
    }

    /**
     * Get totalHours
     *
     * @return integer 
     */
    public function getTotalHours()
    {
        return $this->totalHours;
    }

    /**
     * Set totalTo
     *
     * @param integer $totalTo
     * @return InputEntry
     */
    public function setTotalTo($totalTo)
    {
        $this->totalTo = $totalTo;

        return $this;
    }

    /**
     * Get totalTo
     *
     * @return integer 
     */
    public function getTotalTo()
    {
        return $this->totalTo;
    }

    /**
     * Set totalProd
     *
     * @param string $totalProd
     * @return InputEntry
     */
    public function setTotalProd($totalProd)
    {
        $this->totalProd = $totalProd;

        return $this;
    }

    /**
     * Get totalProd
     *
     * @return string 
     */
    public function getTotalProd()
    {
        return $this->totalProd;
    }

    /**
     * Set totalWorkingHours
     *
     * @param integer $totalWorkingHours
     * @return InputEntry
     */
    public function setTotalWorkingHours($totalWorkingHours)
    {
        $this->totalWorkingHours = $totalWorkingHours;

        return $this;
    }

    /**
     * Get totalWorkingHours
     *
     * @return integer 
     */
    public function getTotalWorkingHours()
    {
        return $this->totalWorkingHours;
    }

    /**
     * Set present
     *
     * @param boolean $present
     * @return InputEntry
     */
    public function setPresent($present)
    {
        $this->present = $present;

        return $this;
    }

    /**
     * Get present
     *
     * @return boolean 
     */
    public function getPresent()
    {
        return $this->present;
    }

    /**
     * Set absence_reason
     *
     * @param \SE\InputBundle\Entity\AbsenceReason $absenceReason
     * @return InputEntry
     */
    public function setAbsenceReason(\SE\InputBundle\Entity\AbsenceReason $absenceReason = null)
    {
        $this->absence_reason = $absenceReason;

        return $this;
    }

    /**
     * Get absence_reason
     *
     * @return \SE\InputBundle\Entity\AbsenceReason 
     */
    public function getAbsenceReason()
    {
        return $this->absence_reason;
    }

    /**
     * Set totalOvertime
     *
     * @param string $totalOvertime
     * @return InputEntry
     */
    public function setTotalOvertime($totalOvertime)
    {
        $this->totalOvertime = $totalOvertime;

        return $this;
    }

    /**
     * Get totalOvertime
     *
     * @return string 
     */
    public function getTotalOvertime()
    {
        return $this->totalOvertime;
    }


    /**
     * Set editorStatus
     *
     * @param \SE\InputBundle\Entity\EditorStatus $editorStatus
     * @return InputEntry
     */
    public function setEditorStatus(\SE\InputBundle\Entity\EditorStatus $editorStatus = null)
    {
        $this->editorStatus = $editorStatus;

        return $this;
    }

    /**
     * Get editorStatus
     *
     * @return \SE\InputBundle\Entity\EditorStatus 
     */
    public function getEditorStatus()
    {
        return $this->editorStatus;
    }

    /**
     * @Assert\IsTrue(message = "Oooh no! I think that more than 11 hours a day is too much for a man... let the man sleep!")
     */
    public function isTooManyHours()
    {   
        $total = 0;
        foreach ($this->activity_hours as $a) {
            $total += $a->getRegularHours() + $a->getOtHours();
        }
        return $total < 11;
    }
}
