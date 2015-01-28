<?php
namespace Application\Stenik\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\Stenik\ContentBundle\Entity\LandingPageRepository")
 * @ORM\Table(name="landing")
 */
class LandingPage
{
	use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;
	
	/** 
	 * @ORM\Id 
	 * @ORM\GeneratedValue 
	 * @ORM\Column(type="integer") 
	 */
    protected $id;

    /**
     * @var string
     * 
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=250, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * 
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var boolean
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="is_hidden", type="boolean",  options={"default" = "0"}), nullable=true)
     */
    protected $isHidden;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /** 
     * @ORM\OneToMany(targetEntity="Application\Stenik\ContentBundle\Entity\LandingPageTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return LandingPage
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
     * Set description
     *
     * @param string $description
     * @return LandingPage
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

       /**
     * Set isHidden
     *
     * @param boolean $isHidden
     * @return LandingPage
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden
     *
     * @return boolean 
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return LandingPage
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return LandingPage
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

    public function __toString()
    {
        return $this->getTitle() ? : 'n/a';
    }
}