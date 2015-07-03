<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InputReview
 *
 * @ORM\Table(name="inputreview")
 * @ORM\Entity
 */
class InputReview
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\TypeIssue")
     * @ORM\JoinColumn(nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\IssueStatus")
     * @ORM\JoinColumn(nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="toerror", type="string", length=255, nullable=true)
     */
    private $toerror;

    /**
     * @var string
     *
     * @ORM\Column(name="inputerror", type="string", length=255, nullable=true)
     */
    private $inputerror;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
     $this->date = new \Datetime();
    }

 

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return InputReview
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return InputReview
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set toerror
     *
     * @param string $toerror
     * @return InputReview
     */
    public function setToerror($toerror)
    {
        $this->toerror = $toerror;

        return $this;
    }

    /**
     * Get toerror
     *
     * @return string 
     */
    public function getToerror()
    {
        return $this->toerror;
    }

    /**
     * Set inputerror
     *
     * @param string $inputerror
     * @return InputReview
     */
    public function setInputerror($inputerror)
    {
        $this->inputerror = $inputerror;

        return $this;
    }

    /**
     * Get inputerror
     *
     * @return string 
     */
    public function getInputerror()
    {
        return $this->inputerror;
    }

    /**
     * Set type
     *
     * @param \SE\InputBundle\Entity\TypeIssue $type
     * @return InputReview
     */
    public function setType(\SE\InputBundle\Entity\TypeIssue $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \SE\InputBundle\Entity\TypeIssue 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param \SE\InputBundle\Entity\User $user
     * @return InputReview
     */
    public function setUser(\SE\InputBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \SE\InputBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set status
     *
     * @param \SE\InputBundle\Entity\IssueStatus $status
     * @return InputReview
     */
    public function setStatus(\SE\InputBundle\Entity\IssueStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \SE\InputBundle\Entity\IssueStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
