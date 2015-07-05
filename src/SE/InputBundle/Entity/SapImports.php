<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SapImports
 *
 * @ORM\Table(name="sapimports")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\SapImportsRepository")
 */
class SapImports
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
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="import", type="boolean", nullable=true)
     */
    private $import;

    /**
     * @var integer
     *
     * @ORM\Column(name="inputs", type="integer", nullable=true)
     */
    private $inputs = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="process", type="boolean", nullable=true)
     */
    private $process;

    /**
     * @var boolean
     *
     * @ORM\Column(name="review", type="boolean", nullable=true)
     */
    private $review;


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
     * Set date
     *
     * @param \DateTime $date
     * @return SapImports
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
     * Set import
     *
     * @param boolean $import
     * @return SapImports
     */
    public function setImport($import)
    {
        $this->import = $import;

        return $this;
    }

    /**
     * Get import
     *
     * @return boolean 
     */
    public function getImport()
    {
        return $this->import;
    }

    /**
     * Set process
     *
     * @param boolean $process
     * @return SapImports
     */
    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return boolean 
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Set review
     *
     * @param boolean $review
     * @return SapImports
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return boolean 
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set inputs
     *
     * @param integer $inputs
     * @return SapImports
     */
    public function setInputs($inputs)
    {
        $this->inputs = $inputs;

        return $this;
    }

    /**
     * Get inputs
     *
     * @return integer 
     */
    public function getInputs()
    {
        return $this->inputs;
    }
}
