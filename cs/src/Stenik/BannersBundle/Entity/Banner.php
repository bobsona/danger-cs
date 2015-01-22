<?php
/**
 * This file is part of the StenikBannersBundle.
 *
 * (c) Georgi Gyurov <georgi@stenik.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Stenik\BannersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
/**
 *  Entity holding information about Banner
 *
 * @ORM\Table(name="banners")
 * @Assert\Callback(methods={"checkBannerSizeValidation"})
 * @ORM\Entity(repositoryClass="Stenik\BannersBundle\Entity\BannerRepository")
 *
 * @package StenikBannersBundle
 * @author Georgi Gyurov <georgi@stenik.bg>
 */
class Banner
{
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Association with BannerZone entity
     *
     * @ORM\ManyToOne(targetEntity="Stenik\BannersBundle\Entity\BannerZone", inversedBy="zones")
     */
    protected $zone;

    /**
     * Association with Media entity (banner picture)
     *
     * @var Media
     *
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     */
    protected $banner;

    /**
     * Title of the banner
     *
     * @var string
     *
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * Banner's url
     *
     * @var string
     *
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * Where should the link goes (new window or same window)
     *
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $target;

    /**
     * Is it hidden?
     *
     * @var boolean
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="is_hidden", type="boolean")
     */
    protected $isHidden;

    /**
     * Track banner's impressions
     *
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="tracking_impressions", type="integer", nullable=true)
     */
    protected $trackingImpresions;

    /**
     * Track clicks onto banner
     *
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="tracking_clicks", type="integer", nullable=true)
     */
    protected $trackingClicks;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     *  Association with BannerTranslation entity
     *
     * @ORM\OneToMany(targetEntity="Stenik\BannersBundle\Entity\BannerTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->cinemas = new ArrayCollection();
        $this->zones = new ArrayCollection();
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
     * Set banner
     *
     * @param  string $banner
     * @return self
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner
     *
     * @return string
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Banner
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
     * Set url
     *
     * @param  string $url
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set isHidden
     *
     * @param  boolean $isHidden
     * @return self
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
     * @param  \DateTime $createdAt
     * @return self
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
     * @param  \DateTime $updatedAt
     * @return self
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
     * Set target
     *
     * @param  string $target
     * @return self
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


    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
    }

    /**
     * Check if the uploaded banner fit to the associated zone
     *
     * @param ExecutionContextInterface $context
     */
    public function checkBannerSizeValidation(ExecutionContextInterface $context)
    {
        $expectedWidth = $this->getZone()->getWidth();
        $expectedHeight = $this->getZone()->getHeight();
        foreach ($this->getTranslations() as $translation) {
            if ($banner = $translation->getBanner()) {
                $width = $banner->getWidth();
                $height = $banner->getHeight();
                if ($expectedHeight != $height || $expectedWidth != $width) {
                    $context->addViolationAt('banner', 'Моля сложете банер с размери width: '.$expectedWidth.' и height: '.$expectedHeight.'!');
                }
            }
        }
    }

    /**
     * Gets the value of zone.
     *
     * @return mixed
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Sets the value of zone.
     *
     * @param mixed $zone the zone
     *
     * @return self
     */
    public function setZone($zone)
    {
        $this->zone = $zone;

        return $this;
    }
}
