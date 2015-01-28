<?php 
namespace Application\Stenik\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Stenik\ContentBundle\Entity\ContentRepository as BaseContentRepository;

class ContentRepository extends BaseContentRepository
{   
    /**
     * Find one by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array 
     */
    public function findOneBySlugAndLocale($locale, $slug)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.translations', 't')
            ->where('t.locale = :locale and t.slug = :slug')
            ->setParameters(array('locale' => $locale, 'slug' => $slug));
        $query = $qb->getQuery();
        return $results = $query->getOneOrNullResult();
    }

    /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array 
     */
    public function findAllByLocale($locale)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.translations', 't')
            ->where('t.locale = :locale and c.is_hidden=:is_hidden and c.is_seo_visible= :is_seo_visible')
            ->setParameters(array('locale' => $locale, 'is_hidden' => 0, 'is_seo_visible' => 0));
        $query = $qb->getQuery();
        return $results = $query->getResult();
    }
}