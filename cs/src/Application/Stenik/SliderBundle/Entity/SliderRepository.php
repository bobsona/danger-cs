<?php 
namespace Application\Stenik\SliderBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Stenik\SliderBundle\Entity\SliderRepository as BaseSliderRepository;

class SliderRepository extends BaseSliderRepository
{   
    /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array 
     */
    public function getAllByLocale($locale, $limit=null, $offset=null)
    {   
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.translations', 't')
            ->where('t.locale = :locale and c.is_hidden = 0')
            ->setParameters(array('locale' => $locale))
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getOneBySlugAndLocale($locale, $slug)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.translations', 't')
            ->where('t.locale = :locale and t.slug = :slug')
            ->setParameters(array('locale' => $locale, 'slug' => $slug));
        $query = $qb->getQuery();
        $results = $query->getResult();
    
        if (count($results)) {
            return $results[0];
        }

        return null;
    }
}