<?php
/**
 * This file is part of the StenikBackgroundsBundle.
 *
 * (c) Georgi Gyurov <georgi@stenik.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Stenik\BackgroundsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Application\Sonata\MediaBundle\Entity\Media;

/**
 * Background
 *
 * @ORM\Table(name="background")
 * @ORM\Entity(repositoryClass="Stenik\BackgroundsBundle\Entity\BackgroundRepository")
 * @Gedmo\Loggable
 *
 * @package StenikBackgroundsBundle
 * @author Georgi Gyurov <georgi@stenik.bg>
 */
class Background
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Background image
     *
     * @var Media
     *
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     */
    protected $image;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of image.
     *
     * @return Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the value of image.
     *
     * @param Media $image the image
     *
     * @return self
     */
    public function setImage(Media $image)
    {
        $this->image = $image;

        return $this;
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

    public function __toString()
    {
        return $this->getImage() ? $this->getImage()->getName() : 'n/a';
    }
}
