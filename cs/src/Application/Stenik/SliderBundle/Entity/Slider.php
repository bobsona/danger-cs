<?php
namespace Application\Stenik\SliderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stenik\SliderBundle\Entity\Slider as BaseSlider;

/**
 * @ORM\Table(name="slider")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="SliderRepository")
 * 
 */
class Slider extends BaseSlider
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /**
     * @var string
     * 
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /** 
     * @ORM\OneToMany(targetEntity="Application\Stenik\SliderBundle\Entity\SliderTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function getRoute()
    {
        return 'Sslider_view';
    }

    public function getRouteParams($params = array())
    {
        return array_merge(array('slug' => $this->getSlug()), $params);
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


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
    protected function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

}