<?php
/**
 * This file is part of the part of ApplicationSonataUserBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@stenik.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Sonata\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
/**
 *  Fixutre loading the information in db
 *
 * @package package
 * @author  Nikolay Tumbalev <n.tumbalev@stenik.bg>
 */
class LoadInterestsData implements FixtureInterface
{
    private $interests = array(
        'int1' => 'Интерес',
        'int2' => 'Интерес2',
        'int3' => 'Интерес3',
    );

    private $interestsEn = array(
        'int1' => 'Interest1',
        'int2' => 'Interest2',
        'int3' => 'Interest3',
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $entityClass = '\Application\Sonata\UserBundle\Entity\Interest';
        $i18nClass = '\Application\Sonata\UserBundle\Entity\InterestTranslation';

        foreach ($this->interests as $key => $value) {
            $rsm = new ResultSetMapping();
            $rsm->addEntityResult($entityClass, 'n');
            $rsm->addFieldResult('n', 'id', 'id');
            $rsm->addFieldResult('n', 'title', 'title');
            $rsm->addJoinedEntityResult($i18nClass, 'i18n', 'n', 'translations');
            $rsm->addFieldResult('i18n', 'i_id', 'id');
            $rsm->addFieldResult('i18n', 'title', 'title');

            $tableName = $em->getClassMetadata($entityClass)->getTableName();
            $tableI18nName = $em->getClassMetadata($i18nClass)->getTableName();

            $query = $em->createNativeQuery(
                "SELECT n.id, i18n.id as i_id, i18n.title
                FROM {$tableName} n left join {$tableI18nName} i18n on n.id=i18n.object_id
                WHERE
                    i18n.locale = 'bg' and
                    LOWER(i18n.title) = ?
                ",
                $rsm)
            ->setParameter(1, $value);

            if (count($query->getResult()) == 0) {
                $obj = new \Application\Sonata\UserBundle\Entity\Interest();
                $objI18n = new \Application\Sonata\UserBundle\Entity\InterestTranslation();

                $obj->setTitle($value);
                $em->persist($obj);

                $objI18n->setLocale('en');
                $objI18n->setObject($obj);
                $objI18n->setTitle($this->interestsEn[$key]);
                $em->persist($objI18n);
            }
        }

        $em->flush();
    }
}
