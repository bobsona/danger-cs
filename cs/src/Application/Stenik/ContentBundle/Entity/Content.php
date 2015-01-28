<?php
namespace Application\Stenik\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stenik\ContentBundle\Entity\Content as BaseContent;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\Stenik\ContentBundle\Entity\ContentRepository")
 * @ORM\Table(name="content")
 */
class Content extends BaseContent
{

    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /** 
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    protected $image;

    /** 
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $gallery;

    /** 
     * @ORM\OneToMany(targetEntity="Application\Stenik\ContentBundle\Entity\ContentTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function getRoute()
    {
        return 'content';
    }

    public function getRouteParams($params = array())
    {
        return array_merge(array('slug' => $this->getSlug()), $params);
    }

    /**
     * Gets the value of image.
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the value of image.
     *
     * @param mixed $image the image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Gets the value of gallery.
     *
     * @return mixed
     */
    public function getGallery()
    {
        if ($this->gallery != null && $this->gallery->getEnabled()) {
            return $this->gallery;
        }else{
            return null;
        }
    }

    /**
     * Sets the value of gallery.
     *
     * @param mixed $gallery the gallery
     *
     * @return self
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }
}