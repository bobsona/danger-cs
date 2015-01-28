<?php 

namespace Application\Stenik\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stenik\ContentBundle\Entity\ContentTranslation as BaseTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   }),  @ORM\UniqueConstraint(name="slug_unique_idx", columns={"slug"})}
 * )
 */
class ContentTranslation extends BaseTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Application\Stenik\ContentBundle\Entity\Content", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /** 
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    protected $image;

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
}