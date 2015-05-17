<?php

namespace SE\ZestBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use SE\ZestBundle\Validator\Antiflood;


/**
 * Advert
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SE\ZestBundle\Entity\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="title", message="Une annonce existe déjà avec ce titre.")
 */
class Advert
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
     * @Assert\DateTime()
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     * @Assert\Length(min=10)
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     * @Assert\Length(min=2)
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Antiflood()
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
    * @ORM\Column(name="published", type="boolean")
    */
    private $published = true;

    /**
     * @ORM\OneToOne(targetEntity="SE\ZestBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $image;

    /**
    * @ORM\ManyToMany(targetEntity="SE\ZestBundle\Entity\Category", cascade={"persist"})
    */
    private $categories;

    /**
    * @ORM\OneToMany(targetEntity="SE\ZestBundle\Entity\Application", mappedBy="advert")
    */
    private $applications; 

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
      * @ORM\Column(name="nb_applications", type="integer")
      */
    private $nbApplications = 0;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;


    public function increaseApplication()
    {
     $this->nbApplications++;
    }

    public function decreaseApplication()
    {
     $this->nbApplications--;
    }


    public function __construct()
    {
     // Par défaut, la date de l'annonce est la date d'aujourd'hui
     $this->date = new \Datetime();
     $this->categories   = new ArrayCollection();
     $this->applications = new ArrayCollection();

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
     * Set date
     *
     * @param \DateTime $date
     * @return Advert
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
     * Set title
     *
     * @param string $title
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Advert
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    public function setImage(Image $image = null)
    {
    $this->image = $image;
    }

    public function getImage()
    {
    return $this->image;
    }

    /**
     * Add categories
     *
     * @param \SE\ZestBundle\Entity\Category $categories
     * @return Advert
     */
    public function addCategory(\SE\ZestBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \SE\ZestBundle\Entity\Category $categories
     */
    public function removeCategory(\SE\ZestBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add applications
     *
     * @param \SE\ZestBundle\Entity\Application $applications
     * @return Advert
     */
    public function addApplication(\SE\ZestBundle\Entity\Application $application)
    {
        $this->applications[] = $application;

        // On lie l'annonce à la candidature
        $application->setAdvert($this);

        return $this;
    }

    /**
     * Remove applications
     *
     * @param \SE\ZestBundle\Entity\Application $applications
     */
    public function removeApplication(\SE\ZestBundle\Entity\Application $application)
    {
        $this->applications->removeElement($application);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
    * @ORM\PreUpdate
    */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
    }


    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Advert
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set nbApplications
     *
     * @param integer $nbApplications
     * @return Advert
     */
    public function setNbApplications($nbApplications)
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    /**
     * Get nbApplications
     *
     * @return integer 
     */
    public function getNbApplications()
    {
        return $this->nbApplications;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Advert
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }


  /**
   * @Assert\Callback
   */
  public function isTitleValid(ExecutionContextInterface $context)
  {
    $forbiddenWords = array('bite', 'couille');

    // On vérifie que le contenu ne contient pas l'un des mots
    if (preg_match('#'.implode('|', $forbiddenWords).'#', $this->getTitle())) {
      // La règle est violée, on définit l'erreur
      $context
        ->buildViolation('Contenu invalide car il contient bite ou couille.') // message
        ->atPath('title')                                                   // attribut de l'objet qui est violé(comme ta soeur)
        ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
      ;
    }
  }
}
