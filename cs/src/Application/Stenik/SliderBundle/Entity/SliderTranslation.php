<?php 

namespace Application\Stenik\SliderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stenik\SliderBundle\Entity\SliderTranslation as BaseSliderTranslation;

/**
 * Stenik\SliderBundle\Entity\SliderTranslation.php
 *
 * @ORM\Entity
 * @ORM\Table(name="slider_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   })}
 * )
 */
class SliderTranslation extends BaseSliderTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Application\Stenik\SliderBundle\Entity\Slider", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    
    /**
     * Gets the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}