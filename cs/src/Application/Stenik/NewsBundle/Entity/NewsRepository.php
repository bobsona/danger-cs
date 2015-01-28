<?php 
namespace Application\Stenik\NewsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Stenik\NewsBundle\Entity\NewsRepository as BaseNewsRepository;

class NewsRepository extends BaseNewsRepository
{   
    /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array 
     */
    public function findAllByLocale($locale, $limit=null, $offset=null)
    {   
        $qb = $this->createQueryBuilder('n')
            ->leftJoin('n.translations', 't')
            ->where('t.locale = :locale and n.is_hidden=:is_hidden')
            ->orderBy('n.published_date', 'DESC')
            ->setParameters(array('locale' => $locale, 'is_hidden'=> 0))
            ->setFirstResult($limit* ($offset-1))
            ->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query;
    }

    public function findOneBySlugAndLocale($locale, $slug)
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.translations', 't')
            ->where('t.locale = :locale and t.slug = :slug')
            ->orderBy('e.published_date',  'DESC')
            ->setParameters(array('locale' => $locale, 'slug' => $slug));
        $query = $qb->getQuery();
        return $results = $query->getOneOrNullResult();
    }

    /**
     * Get all record according to given locale
     *
     * @param string | $locale
     * @return object
     */
    public function findAllForHomepage($locale)
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.translations', 't')
            ->where('t.locale = :locale and e.is_hidden=:is_hidden and e.is_homepage = 1')
            ->orderBy('e.published_date',  'DESC')
            ->setParameters(array('locale' => $locale, 'is_hidden' => 0));
        $query = $qb->getQuery();
        return $results = $query->getResult();
    }

     /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array 
     */
    public function findAllByCategory($locale, $cat, $limit=null, $offset=null)
    {   
        $qb = $this->createQueryBuilder('n')
            ->leftJoin('n.translations', 't')
            ->where('t.locale = :locale and n.is_hidden=:is_hidden and n.category= :category')
            ->orderBy('n.published_date', 'DESC')
            ->setParameters(array('locale' => $locale, 'is_hidden'=> 0, 'category' => (int)$cat))
            ->setFirstResult($limit* ($offset-1))
            ->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query;
    }
}