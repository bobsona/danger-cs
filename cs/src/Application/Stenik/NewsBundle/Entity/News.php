<?php
namespace Application\Stenik\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stenik\NewsBundle\Entity\News as BaseNews;

/**
 * @ORM\Table(name="news")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="NewsRepository")
 * 
 */
class News extends BaseNews
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="category", type="integer")
     */
    protected $category;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="is_homepage", type="boolean", options={"default" = 0})
     */
    protected $is_homepage;

    /** 
     * @ORM\OneToMany(targetEntity="Application\Stenik\NewsBundle\Entity\NewsTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function getRoute()
    {
        return 'news_view';
    }

    public function getRouteParams($params = array())
    {
        return array_merge(array('slug' => $this->getSlug()), $params);
    }

    /**
     * Gets the value of is_homepage.
     *
     * @return mixed
     */
    public function getIsHomepage()
    {
        return $this->is_homepage;
    }

    /**
     * Sets the value of is_homepage.
     *
     * @param mixed $is_homepage the is homepage
     *
     * @return self
     */
    public function setIsHomepage($is_homepage)
    {
        $this->is_homepage = $is_homepage;

        return $this;
    }

    /**
     * Gets the value of category.
     *
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets the value of category.
     *
     * @param mixed $category the category
     *
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }
}