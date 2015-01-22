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

/**
 * BannersMapping
 *
 * @ORM\Table(name="banner_zone")
 * @ORM\Entity(repositoryClass="Stenik\BannersBundle\Entity\BannerZoneRepository")
 *
 * @package StenikBannersBundle
 * @author Georgi Gyurov <georgi@stenik.bg>
 */
class BannerZone
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
     *  Association with Banner entity
     *
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="Stenik\BannersBundle\Entity\Banner", mappedBy="zone")
     */
    protected $zones;

    /**
     * Key of the banner's zone
     *
     * @var string
     *
     * @ORM\Column(name="key_code", type="string", length=255)
     */
    protected $key;

    /**
     *  Value of the banner's zone (description)
     *
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    protected $value;

    /**
     *  Width of the banner's zone
     *
     * @var integer
     *
     * @ORM\Column(name="width", type="integer")
     */
    protected $width;

    /**
     *  Height of the banner's zone
     *
     * @var integer
     *
     * @ORM\Column(name="height", type="integer")
     */
    protected $height;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->zones = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set zone
     *
     * @param  integer        $zone
     * @return BannersMapping
     */
    public function setZones($zone)
    {
        foreach ($this->getZones() as $zone) {
            if (!$this->zones->contains($zone)) {
                $this->addZone($zone);
            }
        }

        return $this;
    }

    /**
     * Get zone
     *
     * @return integer
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Set key
     *
     * @param  string $keyCode
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value
     *
     * @param  string $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set width
     *
     * @param  integer $width
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param  integer $height
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    public function __toString()
    {
        return $this->getValue() ?: 'n/a';
    }

    /**
     * Add zone
     *
     * @param  \Stenik\BannersBundle\Entity\Banner $zone
     * @return BannersMapping
     */
    public function addZone(\Stenik\BannersBundle\Entity\Banner $zones)
    {
        $this->zones[] = $zones;

        return $this;
    }

    /**
     * Remove zone
     *
     * @param \Stenik\BannersBundle\Entity\Banner $zone
     */
    public function removeZone(\Stenik\BannersBundle\Entity\Banner $zones)
    {
        $this->zones->removeElement($zones);
    }
}
