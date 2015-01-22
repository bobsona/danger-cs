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

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * Translations for Banner entity
 *
 * @ORM\Entity
 * @ORM\Table(name="banners_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   })}
 * )
 * @Gedmo\Loggable
 *
 * @package StenikBannersBundle
 * @author Georgi Gyurov <georgi@stenik.bg>
 */
class BannerTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Stenik\BannersBundle\Entity\Banner", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * Title of the banner
     *
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * Banner's url
     *
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    protected $banner;

    /**
     * Gets the Title of the banner.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the Title of the banner.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the Banner's url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the Banner's url.
     *
     * @param string $url the url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Sets the value of banner.
     *
     * @param mixed $banner the banner
     *
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;
    }

    /**
     * Gets the value of banner.
     *
     * @return mixed
     */
    public function getBanner()
    {
        return $this->banner;
    }
}
