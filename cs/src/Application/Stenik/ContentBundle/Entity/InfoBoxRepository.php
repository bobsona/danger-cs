<?php 
namespace Application\Stenik\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InfoBoxRepository extends EntityRepository
{
    /**
     * Get all record according to given locale
     *
     * @param string | $locale
     * @return object
     */
    public function findOneByLocale($locale)
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.translations', 't')
            ->where('t.locale = :locale')
            ->setParameters(array('locale' => $locale));
        $query = $qb->getQuery();
        return $results = $query->getOneOrNullResult();
    }
}