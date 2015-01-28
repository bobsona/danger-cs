<?php 
namespace Application\Stenik\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LandingPageRepository extends EntityRepository
{
    /**
     * Get all record according to given locale
     *
     * @param string | $locale
     * @return object
     */
    public function findAllByLocale($locale)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT n, ni FROM ApplicationStenikContentBundle:LangindPage n
                JOIN n.translations ni
                WHERE ni.locale = :locale'
            )
            ->setParameter('locale', $locale)
            ->getResult();
    }
}