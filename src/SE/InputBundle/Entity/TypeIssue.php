<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeIssue
 *
 * @ORM\Table(name="typeissue")
 * @ORM\Entity
 */
class TypeIssue
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
     * @ORM\Column(name="criticity", type="string", length=255)
     */
    private $criticity;


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
     * @return TypeIssue
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
     * Set criticity
     *
     * @param string $criticity
     * @return TypeIssue
     */
    public function setCriticity($criticity)
    {
        $this->criticity = $criticity;

        return $this;
    }

    /**
     * Get criticity
     *
     * @return string 
     */
    public function getCriticity()
    {
        return $this->criticity;
    }
}
